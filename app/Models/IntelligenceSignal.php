<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\ProcessRawDataWithGeminiJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntelligenceSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'source',
        'raw_content',
        'extracted_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (IntelligenceSignal $signal) {
            ProcessRawDataWithGeminiJob::dispatch($signal);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function leadScores(): HasMany
    {
        return $this->hasMany(LeadScore::class);
    }
}
