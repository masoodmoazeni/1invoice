<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // if (Schema::hasTable('header_menu_items')) {
        //     return;
        // }

        // Schema::create('header_menu_items', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('parent_id')
        //         ->nullable()
        //         ->constrained('header_menu_items')
        //         ->nullOnDelete();
        //     $table->string('label');
        //     $table->string('href')->nullable();
        //     $table->string('description')->nullable();
        //     $table->unsignedTinyInteger('cols')->nullable();
        //     $table->boolean('is_dropdown')->default(false);
        //     $table->enum('link_type', ['custom', 'page_builder'])->default('custom');
        //     $table->foreignId('page_id')
        //         ->nullable()
        //         ->constrained('pagebuilder_pages')
        //         ->nullOnDelete();
        //     $table->unsignedInteger('sort_order')->default(0);
        //     $table->enum('status', ['draft', 'published'])->default('published')->index();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_menu_items');
    }
};
