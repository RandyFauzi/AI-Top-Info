<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('total_funding');
            $table->string('whatsapp_number')->nullable()->after('contact_email');
            $table->text('linkedin_url')->nullable()->after('whatsapp_number');
            $table->text('discord_url')->nullable()->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'whatsapp_number', 'linkedin_url', 'discord_url']);
        });
    }
};
