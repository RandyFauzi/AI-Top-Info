<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intelligence_signals', function (Blueprint $table) {
            $table->id();
            // company_id is foreign key but nullable because company details are extracted by Gemini later
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->string('source'); // enum: news, discord, linkedin
            $table->text('raw_content');
            $table->string('extracted_url')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intelligence_signals');
    }
};
