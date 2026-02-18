<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorEvent extends Model
{
    use HasFactory;

    protected $table = 'error_events';

    public $timestamps = false;

    protected $guarded = ['id']; // Allow all except ID


    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(ErrorGroup::class, 'error_group_id');
    }
}
