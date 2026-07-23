<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_category',
        'title',
        'summary',
        'source_name',
        'url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
