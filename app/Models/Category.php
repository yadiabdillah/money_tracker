<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'family_id',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Category optionally belongs to a Family (null if global).
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Category has many transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope to retrieve categories accessible by a family (global + family custom).
     */
    public function scopeForFamily(Builder $query, ?int $familyId): Builder
    {
        return $query->where(function (Builder $q) use ($familyId) {
            $q->whereNull('family_id');
            if ($familyId !== null) {
                $q->orWhere('family_id', $familyId);
            }
        });
    }

    /**
     * Scope to filter expense categories.
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope to filter income categories.
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope to active categories only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
