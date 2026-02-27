<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->api_key)) {
                $store->api_key = 'sk_live_' . bin2hex(random_bytes(16));
            }
            if (empty($store->public_key)) {
                $store->public_key = 'pk_live_' . bin2hex(random_bytes(16));
            }
            if (empty($store->private_tracking_key)) {
                $store->private_tracking_key = 'rum_' . bin2hex(random_bytes(16));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthChecks()
    {
        return $this->hasMany(HealthCheck::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function errorGroups()
    {
        return $this->hasMany(ErrorGroup::class);
    }
}
