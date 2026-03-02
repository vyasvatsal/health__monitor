<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawledPage extends Model
{
    protected $fillable = [
        'store_id',
        'url',
        'title',
        'status_code',
        'last_crawled_at',
    ];

    protected $casts = [
        'last_crawled_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function ctas()
    {
        return $this->hasMany(DiscoveredCta::class);
    }
}
