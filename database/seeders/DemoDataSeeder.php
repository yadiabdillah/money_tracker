<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Family;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Family
        $family = Family::firstOrCreate(
            ['invite_code' => 'DEMOFAM1'],
            ['name' => 'Smith Family']
        );

        // 2. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@smithfamily.local'],
            [
                'family_id' => $family->id,
                'name' => 'John Smith',
                'password' => Hash::make('password123'),
                'telegram_id' => 100000001,
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 3. Create Member User
        $member = User::firstOrCreate(
            ['email' => 'jane@smithfamily.local'],
            [
                'family_id' => $family->id,
                'name' => 'Jane Smith',
                'password' => Hash::make('password123'),
                'telegram_id' => 100000002,
                'role' => 'member',
                'is_active' => true,
            ]
        );

        // 4. Resolve Categories
        $salaryCat = Category::where('name', 'Salary')->where('type', 'income')->first();
        $foodCat = Category::where('name', 'Food')->where('type', 'expense')->first();
        $transportCat = Category::where('name', 'Transport')->where('type', 'expense')->first();
        $billsCat = Category::where('name', 'Bills')->where('type', 'expense')->first();

        // 5. Create Demo Transactions
        $demoTransactions = [
            [
                'family_id' => $family->id,
                'user_id' => $admin->id,
                'category_id' => $salaryCat?->id ?? 1,
                'type' => 'income',
                'amount' => 5000000.00,
                'description' => 'Monthly Salary June',
                'transaction_date' => now()->startOfMonth()->toDateString(),
            ],
            [
                'family_id' => $family->id,
                'user_id' => $admin->id,
                'category_id' => $foodCat?->id ?? 2,
                'type' => 'expense',
                'amount' => 125000.00,
                'description' => 'Weekly Groceries at Supermarket',
                'transaction_date' => now()->subDays(2)->toDateString(),
            ],
            [
                'family_id' => $family->id,
                'user_id' => $member->id,
                'category_id' => $transportCat?->id ?? 3,
                'type' => 'expense',
                'amount' => 35000.00,
                'description' => 'Train and Bus fare',
                'transaction_date' => now()->subDays(1)->toDateString(),
            ],
            [
                'family_id' => $family->id,
                'user_id' => $admin->id,
                'category_id' => $billsCat?->id ?? 4,
                'type' => 'expense',
                'amount' => 450000.00,
                'description' => 'Electricity and Water Utility Bill',
                'transaction_date' => now()->toDateString(),
            ],
        ];

        foreach ($demoTransactions as $tx) {
            Transaction::firstOrCreate(
                [
                    'family_id' => $tx['family_id'],
                    'user_id' => $tx['user_id'],
                    'description' => $tx['description'],
                    'transaction_date' => $tx['transaction_date'],
                ],
                [
                    'category_id' => $tx['category_id'],
                    'type' => $tx['type'],
                    'amount' => $tx['amount'],
                    'is_deleted' => false,
                ]
            );
        }
    }
}
