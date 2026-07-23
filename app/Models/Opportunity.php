<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'source_platform',
        'source_url',
        'summary',
        'extracted_contacts',
        'posted_at',
    ];

    protected $casts = [
        'extracted_contacts' => 'array',
        'posted_at' => 'datetime',
    ];
}
