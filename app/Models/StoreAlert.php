<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'severity',
        'title',
        'message',
        'data',
        'read_at',
        'hash'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }
}
