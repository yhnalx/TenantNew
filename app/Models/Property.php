<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'manager_id',
    ];

    // 🔗 Property belongs to a manager (User with role = manager)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // 🔗 Property has many leases
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
}
