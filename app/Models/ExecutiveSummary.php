<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutiveSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'content',
        'metrics_snapshot',
        'period_start',
        'period_end'
    ];

    protected $casts = [
        'metrics_snapshot' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
