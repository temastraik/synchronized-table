<?php
// app/Models/TextItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TextItem extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    // Local Scope для выборки только разрешенных записей
    public function scopeAllowed(Builder $query): Builder
    {
        return $query->where('status', 'Allowed');
    }

    // Local Scope для ограничения количества записей
    public function scopeLimitCount(Builder $query, ?int $count = null): Builder
    {
        if ($count) {
            return $query->limit($count);
        }
        return $query;
    }
}