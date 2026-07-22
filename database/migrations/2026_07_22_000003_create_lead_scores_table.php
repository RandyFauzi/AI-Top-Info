<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('intelligence_signal_id')->constrained('intelligence_signals')->onDelete('cascade');
            $table->integer('score'); // 1-100
            $table->string('intent_category'); // e.g., 'Computer Vision', 'Gen-Video', 'Text-LLM'
            $table->text('reasoning');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_scores');
    }
};
