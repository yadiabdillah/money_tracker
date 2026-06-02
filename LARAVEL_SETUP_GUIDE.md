# Laravel Money Tracker - Setup Guide
## Local-First Development with Laravel & Bootstrap

**Version:** 2.0  
**Last Updated:** June 2, 2026  
**Target:** Local Development Environment

---

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Installation Steps](#installation-steps)
3. [Database Setup](#database-setup)
4. [Telegram Bot Configuration](#telegram-bot-configuration)
5. [Running the Application](#running-the-application)
6. [Project Structure](#project-structure)
7. [Development Workflow](#development-workflow)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Software

#### 1. PHP 8.1 or Higher
**Windows:**
- Download from [php.net](https://windows.php.net/download/)
- Or install via Laragon/XAMPP (recommended for beginners)

**macOS:**
```bash
brew install php@8.1
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-xml php8.1-curl php8.1-mbstring
```

#### 2. Composer (PHP Package Manager)
Download from [getcomposer.org](https://getcomposer.org/)

**Verify installation:**
```bash
composer --version
```

#### 3. MySQL 8.0 or MariaDB 10.x
**Windows:** Install via XAMPP or Laragon  
**macOS:**
```bash
brew install mysql
```
**Linux:**
```bash
sudo apt install mysql-server
```

#### 4. Node.js & NPM (for frontend assets)
Download from [nodejs.org](https://nodejs.org/)

**Verify installation:**
```bash
node --version
npm --version
```

#### 5. Git (Version Control)
Download from [git-scm.com](https://git-scm.com/)

### Recommended Tools

- **Code Editor:** Visual Studio Code with Laravel extensions
- **Database GUI:** phpMyAdmin, MySQL Workbench, or TablePlus
- **API Testing:** Postman or Insomnia
- **Local Server:** Laragon (Windows), Laravel Valet (macOS), or XAMPP

---

## Installation Steps

### Step 1: Create New Laravel Project

```bash
# Navigate to your projects directory
cd ~/projects  # or C:\projects on Windows

# Create new Laravel project
composer create-project laravel/laravel money-tracker

# Navigate into project
cd money-tracker
```

### Step 2: Install Required Packages

```bash
# Install Telegram Bot SDK
composer require irazasyed/telegram-bot-sdk

# Install Laravel Debugbar (development only)
composer require barryvdh/laravel-debugbar --dev

# Install Laravel IDE Helper (optional, for better IDE support)
composer require --dev barryvdh/laravel-ide-helper
```

### Step 3: Install Frontend Dependencies

```bash
# Install Node packages
npm install

# Install Bootstrap and dependencies
npm install bootstrap @popperjs/core

# Install Chart.js for visualizations
npm install chart.js

# Install Bootstrap Icons
npm install bootstrap-icons
```

### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file with your local settings:

```env
APP_NAME="Money Tracker"
APP_ENV=local
APP_KEY=base64:... # Generated automatically
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=money_tracker
DB_USERNAME=root
DB_PASSWORD=your_password

TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_URL=http://localhost:8000/api/telegram/webhook
```

---

## Database Setup

### Step 1: Create Database

**Using MySQL Command Line:**
```sql
CREATE DATABASE money_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Using phpMyAdmin:**
1. Open phpMyAdmin (usually http://localhost/phpmyadmin)
2. Click "New" in the left sidebar
3. Enter database name: `money_tracker`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 2: Create Migrations

Create migration files for all tables:

```bash
# Create families table
php artisan make:migration create_families_table

# Create users table (modify existing)
php artisan make:migration add_telegram_fields_to_users_table

# Create categories table
php artisan make:migration create_categories_table

# Create transactions table
php artisan make:migration create_transactions_table
```

### Step 3: Define Migration Schemas

**database/migrations/xxxx_create_families_table.php:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('invite_code', 20)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
```

**database/migrations/xxxx_add_telegram_fields_to_users_table.php:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->constrained()->onDelete('cascade');
            $table->bigInteger('telegram_id')->unique()->nullable();
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn(['family_id', 'telegram_id', 'role', 'is_active']);
        });
    }
};
```

**database/migrations/xxxx_create_categories_table.php:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->enum('type', ['income', 'expense']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

**database/migrations/xxxx_create_transactions_table.php:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->string('description', 200)->nullable();
            $table->date('transaction_date');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['family_id', 'transaction_date']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Create Seeders

```bash
# Create category seeder
php artisan make:seeder CategorySeeder

# Create demo data seeder
php artisan make:seeder DemoDataSeeder
```

**database/seeders/CategorySeeder.php:**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Expense categories
            ['name' => 'Food', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Transport', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Shopping', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Bills', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Healthcare', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Entertainment', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Education', 'type' => 'expense', 'is_active' => true],
            ['name' => 'Other', 'type' => 'expense', 'is_active' => true],
            
            // Income categories
            ['name' => 'Salary', 'type' => 'income', 'is_active' => true],
            ['name' => 'Business', 'type' => 'income', 'is_active' => true],
            ['name' => 'Investment', 'type' => 'income', 'is_active' => true],
            ['name' => 'Gift', 'type' => 'income', 'is_active' => true],
            ['name' => 'Other', 'type' => 'income', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'type' => $category['type'],
                'family_id' => null, // Global categories
                'is_active' => $category['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
```

**Run seeders:**
```bash
php artisan db:seed --class=CategorySeeder
```

---

## Telegram Bot Configuration

### Step 1: Create Telegram Bot

1. Open Telegram and search for [@BotFather](https://t.me/botfather)
2. Send `/newbot` command
3. Follow instructions to name your bot
4. Copy the bot token provided
5. Add token to `.env` file:

```env
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
```

### Step 2: Configure Telegram SDK

**config/telegram.php:**
```php
<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'async_requests' => false,
    'http_client_handler' => null,
    'base_bot_url' => null,
    'resolve_injected_dependencies' => true,
    'commands' => [
        // Register bot commands here
    ],
];
```

### Step 3: Set Up Webhook (for production) or Polling (for local)

**For Local Development (Polling):**
Create a command to poll for updates:

```bash
php artisan make:command TelegramPoll
```

**For Production (Webhook with ngrok):**
```bash
# Install ngrok
# Download from https://ngrok.com/

# Start ngrok tunnel
ngrok http 8000

# Set webhook (replace with your ngrok URL)
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://your-ngrok-url.ngrok.io/api/telegram/webhook"
```

---

## Running the Application

### Option 1: PHP Built-in Server (Simplest)

```bash
# Start Laravel development server
php artisan serve

# Application will be available at:
# http://localhost:8000
```

### Option 2: Laragon (Windows - Recommended)

1. Download and install [Laragon](https://laragon.org/)
2. Move project to `C:\laragon\www\money-tracker`
3. Right-click Laragon tray icon → Start All
4. Access at: `http://money-tracker.test`

### Option 3: XAMPP

1. Install XAMPP
2. Move project to `C:\xampp\htdocs\money-tracker`
3. Start Apache and MySQL from XAMPP Control Panel
4. Access at: `http://localhost/money-tracker/public`

### Option 4: Laravel Valet (macOS)

```bash
# Install Valet
composer global require laravel/valet
valet install

# Navigate to project directory
cd ~/projects/money-tracker

# Park the directory
valet park

# Access at: http://money-tracker.test
```

### Compile Frontend Assets

```bash
# Development mode (with hot reload)
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

---

## Project Structure

```
money-tracker/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── TransactionController.php
│   │   │   ├── TelegramController.php
│   │   │   └── CategoryController.php
│   │   └── Middleware/
│   │       └── CheckFamilyAccess.php
│   ├── Models/
│   │   ├── Family.php
│   │   ├── User.php
│   │   ├── Transaction.php
│   │   └── Category.php
│   └── Services/
│       └── TelegramService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── transactions/
│   │   │   ├── index.blade.php
│   │   │   └── create.blade.php
│   │   └── reports/
│   │       └── index.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php
│   └── api.php
├── public/
│   └── index.php
├── .env
├── composer.json
└── package.json
```

---

## Development Workflow

### Daily Development Routine

1. **Start Services:**
```bash
# Start MySQL (if not running)
# Start Laravel server
php artisan serve

# In another terminal, watch for asset changes
npm run dev
```

2. **Make Changes:**
- Edit controllers, models, views as needed
- Test in browser at http://localhost:8000
- Test Telegram bot commands

3. **Database Changes:**
```bash
# Create new migration
php artisan make:migration add_new_field

# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback
```

4. **Clear Caches:**
```bash
# Clear all caches
php artisan optimize:clear

# Or individually:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Testing Telegram Bot Locally

1. **Using ngrok (Recommended):**
```bash
# Start ngrok
ngrok http 8000

# Copy the HTTPS URL and set webhook
# Update TELEGRAM_WEBHOOK_URL in .env
```

2. **Using Polling (Alternative):**
Create a polling command to fetch updates without webhook

### Git Workflow

```bash
# Initialize repository
git init
git add .
git commit -m "Initial commit"

# Create .gitignore (Laravel includes this by default)
# Make sure it includes:
# /vendor
# /node_modules
# .env
# /storage/*.key
```

---

## Troubleshooting

### Common Issues

#### 1. "Class not found" Error
```bash
# Regenerate autoload files
composer dump-autoload
```

#### 2. Permission Errors (Linux/macOS)
```bash
# Fix storage and cache permissions
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

#### 3. Database Connection Error
- Check MySQL is running
- Verify credentials in `.env`
- Test connection:
```bash
php artisan tinker
DB::connection()->getPdo();
```

#### 4. Telegram Webhook Not Working
- Ensure HTTPS (use ngrok for local)
- Check webhook status:
```bash
curl https://api.telegram.org/bot<TOKEN>/getWebhookInfo
```

#### 5. Assets Not Loading
```bash
# Rebuild assets
npm run build

# Clear browser cache
# Check public/build directory exists
```

#### 6. Migration Errors
```bash
# Reset database (WARNING: deletes all data)
php artisan migrate:fresh

# With seeders
php artisan migrate:fresh --seed
```

### Debug Mode

Enable detailed error messages in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Install Laravel Telescope for advanced debugging:
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: http://localhost:8000/telescope

---

## Next Steps

1. **Create Models and Relationships**
2. **Build Controllers**
3. **Design Blade Views with Bootstrap**
4. **Implement Telegram Bot Commands**
5. **Add Authentication**
6. **Create Dashboard with Charts**
7. **Test Thoroughly**
8. **Deploy (Optional)**

---

## Useful Commands Reference

```bash
# Laravel
php artisan serve                    # Start development server
php artisan migrate                  # Run migrations
php artisan db:seed                  # Run seeders
php artisan make:controller Name     # Create controller
php artisan make:model Name -m       # Create model with migration
php artisan tinker                   # Interactive shell
php artisan route:list               # List all routes
php artisan optimize:clear           # Clear all caches

# Composer
composer install                     # Install dependencies
composer update                      # Update dependencies
composer dump-autoload               # Regenerate autoload

# NPM
npm install                          # Install dependencies
npm run dev                          # Development build
npm run build                        # Production build
npm run watch                        # Watch for changes

# Git
git status                           # Check status
git add .                            # Stage all changes
git commit -m "message"              # Commit changes
git push                             # Push to remote
```

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Chart.js Documentation](https://www.chartjs.org/docs)
- [Laravel Telegram Bot SDK](https://telegram-bot-sdk.readme.io/)

---

**Happy Coding! 🚀**