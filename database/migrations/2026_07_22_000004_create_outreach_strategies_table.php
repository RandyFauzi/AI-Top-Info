<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('target_persona'); // e.g. CTO, Lead AI Engineer
            $table->text('suggested_angle');
            $table->text('email_draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_strategies');
    }
};
