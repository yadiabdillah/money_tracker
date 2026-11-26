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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')
                  ->constrained('families')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('restrict');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->string('description', 200)->nullable();
            $table->date('transaction_date');
            $table->boolean('is_deleted')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // Strategic Performance Indexes
            $table->index(['family_id', 'transaction_date'], 'transactions_family_date_index');
            $table->index(['family_id', 'type', 'transaction_date'], 'transactions_family_type_date_index');
            $table->index('category_id', 'transactions_category_id_index');
            $table->index('user_id', 'transactions_user_id_index');
            $table->index('is_deleted', 'transactions_is_deleted_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('transactions');
    }
};
