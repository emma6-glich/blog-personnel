<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('ip_address'); // IP du visiteur
            $table->timestamps();

            // Un même IP ne peut compter qu'une seule fois par article
            $table->unique(['post_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_views');
    }
};
