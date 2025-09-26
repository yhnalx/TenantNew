<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',           // tenant | manager
        'status',         // pending | approved | rejected
        'contact_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 🔗 A user can have many leases (if tenant)
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    // 🔗 If user is a manager, they can manage many properties
    public function properties()
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    // 🔗 Tenant payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    // 🔗 Tenant maintenance requests
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'user_id');
    }


    // 🔗 Tenant Application (one-to-one)
    public function tenantApplication()
    {
        return $this->hasOne(TenantApplication::class);
    }

    public function hasCompletedTenantApplication()
    {
        $app = $this->tenantApplication;
        return $app && $app->completed; // make sure 'completed' column exists in tenant_applications
    }

    // ⚡ Helper: check if tenant is approved
    public function isApprovedTenant(): bool
    {
        return $this->role === 'tenant' && $this->status === 'approved';
    }
}
