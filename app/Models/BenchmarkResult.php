<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenchmarkResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
    }
}
