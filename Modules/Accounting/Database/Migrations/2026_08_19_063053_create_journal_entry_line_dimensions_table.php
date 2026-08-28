<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entry_line_dimensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_line_id');
            $table->unsignedBigInteger('dimension_value_id');

            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['journal_entry_line_id', 'dimension_value_id'], 'unique_journal_entry_line_dimension');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entry_line_dimensions');
    }
};
