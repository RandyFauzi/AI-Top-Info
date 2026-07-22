<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'industry',
        'description',
        'total_funding',
        'contact_email',
        'whatsapp_number',
        'linkedin_url',
        'discord_url',
    ];

    public function intelligenceSignals(): HasMany
    {
        return $this->hasMany(IntelligenceSignal::class);
    }

    public function leadScores(): HasMany
    {
        return $this->hasMany(LeadScore::class);
    }

    public function latestLeadScore(): HasOne
    {
        return $this->hasOne(LeadScore::class)->latestOfMany();
    }

    public function leadScore(): HasOne
    {
        return $this->latestLeadScore();
    }

    public function outreachStrategies(): HasMany
    {
        return $this->hasMany(OutreachStrategy::class);
    }

    public function latestOutreachStrategy(): HasOne
    {
        return $this->hasOne(OutreachStrategy::class)->latestOfMany();
    }

    public function outreachStrategy(): HasOne
    {
        return $this->latestOutreachStrategy();
    }
}
