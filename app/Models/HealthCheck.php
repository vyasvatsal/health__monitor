<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function results()
    {
        return $this->hasMany(CheckResult::class);
    }

    public function latestResult()
    {
        return $this->hasOne(CheckResult::class)->latestOfMany();
    }
}
