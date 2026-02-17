<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'type',
        'message',
        'file',
        'line',
        'trace',
        'context',
        'severity',
        'status',
        'count',
        'last_seen_at',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'array',
        'last_seen_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
