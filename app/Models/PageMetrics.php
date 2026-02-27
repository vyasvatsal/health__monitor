<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMetrics extends Model
{
    protected $guarded = [];

    protected $casts = [
        'vitals' => 'array',
        'cta_clicks' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
