<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_a_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Lucía Méndez',
            'email' => 'lucia@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'lucia@example.com']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_registration_sends_email_verification_notification(): void
    {
        Notification::fake();

        $this->postJson('/api/auth/register', [
            'name' => 'Lucía Méndez',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        Notification::assertSentTo(User::where('email', 'verify@example.com')->first(), VerifyEmail::class);
    }

    public function test_signed_email_verification_marks_user_as_verified(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->getJson($url)->assertOk();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_password_reset_link_can_be_requested_and_used(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'old-password']);

        $this->postJson('/api/auth/password/forgot', ['email' => $user->email])
            ->assertOk();
        Notification::assertSentTo($user, ResetPassword::class);

        $token = Password::broker()->createToken($user);
        $this->postJson('/api/auth/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertOk();

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ])->assertOk();
    }

    public function test_user_can_login_and_access_their_account(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $login = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertOk();

        $token = $login->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_user_can_update_profile_and_email_change_requires_verification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'old@example.com', 'email_verified_at' => now()]);

        $this->actingAs($user, 'sanctum')->patchJson('/api/auth/profile', [
            'name' => 'Nuevo Nombre',
            'email' => 'new@example.com',
        ])->assertOk()->assertJsonPath('user.email', 'new@example.com');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nuevo Nombre', 'email' => 'new@example.com', 'email_verified_at' => null]);
        Notification::assertSentTo($user->fresh(), VerifyEmail::class);
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_customer_cannot_access_admin_endpoints(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/overview')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_endpoints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/overview')
            ->assertOk()
            ->assertJsonStructure(['data' => ['users', 'products']]);
    }
}
