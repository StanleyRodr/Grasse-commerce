<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('guest_email')->nullable();
            $table->string('guest_name')->nullable();
            $table->json('guest_address')->nullable();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_user_id_foreign');
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_address_id_foreign');
            DB::statement('ALTER TABLE orders ALTER COLUMN user_id DROP NOT NULL');
            DB::statement('ALTER TABLE orders ALTER COLUMN address_id DROP NOT NULL');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_address_id_foreign FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE RESTRICT');
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['guest_email', 'guest_name', 'guest_address']);
        });
    }
};
