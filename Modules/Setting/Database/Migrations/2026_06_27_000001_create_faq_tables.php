<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // if (!Schema::hasTable('faq_categories')) {
        //     Schema::create('faq_categories', function (Blueprint $table) {
        //         $table->id();
        //         $table->string('title');
        //         $table->string('slug')->unique();
        //         $table->unsignedInteger('sort_order')->default(0);
        //         $table->enum('status', ['draft', 'published'])->default('published')->index();
        //         $table->timestamps();
        //         $table->softDeletes();
        //     });
        // }

        // if (!Schema::hasTable('faq_items')) {
        //     Schema::create('faq_items', function (Blueprint $table) {
        //         $table->id();
        //         $table->foreignId('faq_category_id')
        //             ->constrained('faq_categories')
        //             ->cascadeOnDelete();
        //         $table->string('question');
        //         $table->longText('answer');
        //         $table->unsignedInteger('sort_order')->default(0);
        //         $table->enum('status', ['draft', 'published'])->default('published')->index();
        //         $table->timestamps();
        //         $table->softDeletes();
        //     });
        // }
    }

    public function down()
    {
        Schema::dropIfExists('faq_items');
        Schema::dropIfExists('faq_categories');
    }
};
