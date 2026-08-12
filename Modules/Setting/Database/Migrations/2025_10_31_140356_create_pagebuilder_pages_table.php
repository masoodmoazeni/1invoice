<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::create('pagebuilder_pages', function (Blueprint $table) {
        //     $table->id();

        //     $table->string('title', 255);
        //     $table->string('slug', 255)->unique();
        //     $table->string('type', 255)->unique();
        //     $table->string('image', 255)->unique();

        //     $table->jsonb('content')->nullable();
        //     $table->jsonb('root')->nullable();

        //     $table->enum('status', [
        //         'draft',
        //         'temp',
        //         'published',
        //         'archived'
        //     ])->default('draft')->index();

        //     $table->timestamp('published_at')->nullable();

        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->index('slug');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pagebuilder_pages');
    }
};
