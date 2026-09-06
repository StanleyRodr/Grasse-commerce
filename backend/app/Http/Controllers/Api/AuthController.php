<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use App\Models\User as UserModel;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($validated);
        $user->sendEmailVerificationNotification();

        return response()->json($this->tokenResponse($user), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        return response()->json($this->tokenResponse($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function sendVerificationNotification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'El correo ya está verificado.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación enviado.']);
    }

    public function verifyEmail(Request $request, int $id, string $hash): Response
    {
        $user = User::query()->findOrFail($id);
        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        if (!$request->expectsJson()) {
            return redirect()->away(rtrim((string) env('FRONTEND_URL', 'http://localhost:5173'), '/') . '/verificar-correo?verified=1');
        }

        return response()->json(['message' => 'Correo verificado correctamente.']);
    }

    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => ['required', 'email:rfc']]);
        $status = Password::sendResetLink($validated);

        return response()->json([
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Si existe una cuenta, recibirás instrucciones por correo.'
                : 'No pudimos enviar las instrucciones en este momento.',
        ], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset($validated, function (UserModel $user, string $password): void {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            $user->tokens()->delete();
            Event::dispatch(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => ['El enlace de recuperación no es válido o expiró.']]);
        }

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }

    private function tokenResponse(User $user): array
    {
        return [
            'user' => $user->only(['id', 'name', 'email', 'email_verified_at', 'role']),
            'token' => $user->createToken('grasse-spa')->plainTextToken,
        ];
    }
}
