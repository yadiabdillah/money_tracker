<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Expense Categories
            ['name' => 'Food', 'type' => 'expense'],
            ['name' => 'Transport', 'type' => 'expense'],
            ['name' => 'Shopping', 'type' => 'expense'],
            ['name' => 'Bills', 'type' => 'expense'],
            ['name' => 'Healthcare', 'type' => 'expense'],
            ['name' => 'Entertainment', 'type' => 'expense'],
            ['name' => 'Education', 'type' => 'expense'],
            ['name' => 'Other Expense', 'type' => 'expense'],

            // Income Categories
            ['name' => 'Salary', 'type' => 'income'],
            ['name' => 'Business', 'type' => 'income'],
            ['name' => 'Investment', 'type' => 'income'],
            ['name' => 'Gift', 'type' => 'income'],
            ['name' => 'Other Income', 'type' => 'income'],
        ];

        $now = now();

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'family_id' => null,
                ],
                [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
