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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')
                  ->nullable()
                  ->constrained('families')
                  ->onDelete('cascade');
            $table->string('name', 50);
            $table->enum('type', ['income', 'expense']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['family_id', 'type', 'is_active'], 'categories_family_type_active_index');
            $table->index('type', 'categories_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
        });
        Schema::dropIfExists('categories');
    }
};
