<?php

use App\Models\Lease;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'lease_id', 'tenant_id', 'property_id', 'request_date', 'description', 'status'
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
