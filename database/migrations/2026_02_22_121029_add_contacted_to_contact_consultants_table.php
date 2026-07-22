<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contact_consultants', function (Blueprint $table) {
            $table->boolean('contacted')->default(false)->after('describe_service');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_consultants', function (Blueprint $table) {
            $table->dropColumn('contacted');
        });
    }
};
