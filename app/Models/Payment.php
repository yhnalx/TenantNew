<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'pay_date',
        'pay_amount',
        'pay_method',
        'pay_status',
        'proof',
        'payment_for',
        'account_no',
    ];

    // Cast columns to proper data types
    protected $casts = [
        'pay_date'   => 'datetime',   // ensures Carbon instance
        'pay_amount' => 'decimal:2',
    ];

    /**
     * Relationship: Payment belongs to a tenant (User).
     * Either tenant() or user() can be used — both point to the users table.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function lease()
    {
        return null; // lease not yet implemented
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
