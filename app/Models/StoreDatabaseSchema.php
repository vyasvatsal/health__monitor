<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDatabaseSchema extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'schema_json',
        'version_hash',
        'occurred_at'
    ];

    protected $casts = [
        'schema_json' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
