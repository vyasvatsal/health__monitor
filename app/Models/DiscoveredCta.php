<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscoveredCta extends Model
{
    protected $fillable = [
        'crawled_page_id',
        'text',
        'tag',
        'href',
        'css_classes',
    ];

    public function page()
    {
        return $this->belongsTo(CrawledPage::class, 'crawled_page_id');
    }
}
