<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'intelligence_signal_id',
        'score',
        'intent_category',
        'reasoning',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function intelligenceSignal(): BelongsTo
    {
        return $this->belongsTo(IntelligenceSignal::class);
    }
}
