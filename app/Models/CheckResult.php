<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckResult extends Model
{
    use HasFactory;

    public $timestamps = false; // Manually managing created_at

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function check()
    {
        return $this->belongsTo(HealthCheck::class, 'health_check_id');
    }
}
