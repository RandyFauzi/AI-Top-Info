<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutreachStrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'target_persona',
        'suggested_angle',
        'email_draft',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
