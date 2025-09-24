<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'room_number',
        'lease_start_date',
        'lease_end_date',
        'lease_status',
        'lease_terms',
        'renewal_requested',
    ];

    // 🔗 Lease belongs to a tenant (User with role = tenant)
    public function tenant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔗 Lease belongs to a property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
