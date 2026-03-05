<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRoute extends Model
{
    protected $fillable = [
        'store_id',
        'uri',
        'name',
        'action',
    ];

    /**
     * Get the store that owns the route.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
