<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // if (!Schema::hasTable('faq_settings')) {
        //     Schema::create('faq_settings', function (Blueprint $table) {
        //         $table->id();
        //         $table->json('schema')->nullable();
        //         $table->timestamps();
        //     });
        // }
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_settings');
    }
};
