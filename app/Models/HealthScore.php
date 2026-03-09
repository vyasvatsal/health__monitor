<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthScore extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'recorded_at' => 'date',
        'metric_availability' => 'float',
        'metric_performance' => 'float',
        'metric_incidents' => 'float',
        'metrics_json' => 'array',
        'is_daily_snapshot' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
