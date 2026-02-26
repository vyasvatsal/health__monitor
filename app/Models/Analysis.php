<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    protected $fillable = [
        'store_id',
        'url',
        'performance_score',
        'accessibility_score',
        'best_practices_score',
        'seo_score',
        'core_web_vitals',
        'ai_insights',
        'desktop_screenshot',
        'mobile_screenshot',
    ];

    protected $casts = [
        'core_web_vitals' => 'array',
        'ai_insights' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
