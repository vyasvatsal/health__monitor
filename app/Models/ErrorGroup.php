<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorGroup extends Model
{
    use HasFactory;

    protected $table = 'error_groups';

    protected $guarded = ['id']; // Allow all except ID


    protected $casts = [
        'last_seen_at' => 'datetime',
        'ai_analysis' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function events()
    {
        return $this->hasMany(ErrorEvent::class);
    }
}
