<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'family_id',
        'user_id',
        'category_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'is_deleted',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_deleted' => 'boolean',
    ];

    /**
     * Transaction belongs to a Family.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Transaction was recorded by a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transaction is categorized under Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope transactions for a specific family.
     */
    public function scopeForFamily(Builder $query, int $familyId): Builder
    {
        return $query->where('family_id', $familyId);
    }

    /**
     * Scope transactions within a date range (sargable date query).
     */
    public function scopeInDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope expense transactions.
     */
    public function scopeExpenses(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope income transactions.
     */
    public function scopeIncomes(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope transactions not flagged as deleted.
     */
    public function scopeNotDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', false);
    }
}
