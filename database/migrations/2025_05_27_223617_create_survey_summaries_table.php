<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
            $table->integer('total_responses')->default(0);
            $table->string('sentiment')->nullable();
            $table->text('summary')->nullable();
            $table->string('status')->default('pending')->comment('pending, generating, completed, failed');
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_summaries');
    }
};
