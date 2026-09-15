<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول time_zones با فیلدهای مورد نیاز و ارتباط با جدول countries
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('country_id');

            $table->string('name', 100)->comment('نام منطقه زمانی (مثلاً Asia/Tehran, America/New_York)')->unique();
            
            $table->string('utc_offset', 10)->comment('مقدار UTC Offset (مثلاً +03:30, -05:00)');
            
            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'is_default']);

            $table->comment('جدول اطلاعات موقعیت زمانی');
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول time_zones در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_zones');
    }
};