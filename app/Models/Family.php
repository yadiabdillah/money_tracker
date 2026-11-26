<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Family extends Model
{
    use HasFactory;

    protected $table = 'families';

    protected $fillable = [
        'name',
        'invite_code',
    ];

    /**
     * Family has many users (members).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Family has many custom categories.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Family has many transactions in its ledger.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Generate an alphanumeric invite code.
     */
    public static function generateInviteCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('invite_code', $code)->exists());

        return $code;
    }
}
