<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')
                  ->nullable()
                  ->constrained('families')
                  ->onDelete('cascade');
            $table->string('name', 100);
            $table->string('email', 255)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->nullable();
            $table->unsignedBigInteger('telegram_id')->nullable()->unique();
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('is_active', 'users_is_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
        });
        Schema::dropIfExists('users');
    }
};
