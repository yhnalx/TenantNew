<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',            // tenant | manager
        'status',          // pending | approved | rejected
        'contact_number',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 🔗 Relationships
    public function leases()
    {
        return $this->hasMany(Lease::class, 'user_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'tenant_id');
    }

    public function tenantApplication()
    {
        return $this->hasOne(TenantApplication::class, 'user_id');
    }

    public function hasCompletedTenantApplication(): bool
    {
        $app = $this->tenantApplication;
        return $app && $app->completed;
    }

    public function isApprovedTenant(): bool
    {
        return $this->role === 'tenant' && $this->status === 'approved';
    }

    // ⚡ Helpers for payments

    // Total paid for a specific type (Rent, Utilities, Deposit)
    public function totalPaid(string $paymentFor): float
    {
        return (float) $this->payments()
            ->where('payment_for', $paymentFor)
            ->where('pay_status', 'Paid')
            ->sum('pay_amount');
    }

    // Remaining balance for rent or utilities
    public function unpaidBalance(string $paymentFor, float $expectedAmount): float
    {
        return max(0, $expectedAmount - $this->totalPaid($paymentFor));
    }

    // Check if deposit is paid
    public function depositPaid(): bool
    {
        return $this->payments()
            ->where('payment_for', 'Deposit')
            ->where('pay_status', 'Paid')
            ->exists();
    }
}
