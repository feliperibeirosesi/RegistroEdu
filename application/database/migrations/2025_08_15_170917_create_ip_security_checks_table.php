<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ip_security_checks', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->json('security_data');
            $table->integer('risk_score')->default(0);
            $table->string('country')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['checked_at', 'is_blocked']);
            $table->index('risk_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_security_checks');
    }
};
