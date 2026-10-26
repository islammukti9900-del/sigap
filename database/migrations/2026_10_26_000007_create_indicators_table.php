<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ip', 'domain', 'url', 'hash']);
            $table->string('value');
            $table->enum('threat', ['malware', 'phishing', 'c2', 'botnet', 'lainnya'])->default('lainnya');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['type', 'value']);
        });

        Schema::create('incident_indicator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->foreignId('indicator_id')->constrained('indicators')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['incident_id', 'indicator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_indicator');
        Schema::dropIfExists('indicators');
    }
};
