<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'lease_id',
        'tenant_id',
        'payment_date',
        'pay_amount',
        'pay_method',
        'pay_status',
        'proof',
        'payment_for',
        'account_no',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
