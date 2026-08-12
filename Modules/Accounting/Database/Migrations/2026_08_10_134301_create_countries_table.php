<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول countries با فیلدهای مورد نیاز
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('iso2', 2)->comment('کد دو حرفی کشور')->unique()->index();
            
            $table->string('iso3', 3)->comment('کد سه حرفی کشور')->unique()->index();
            
            $table->string('name', 100)->comment('نام کامل کشور');
            
            $table->string('numeric_code', 10)->comment('کد عددی کشور')->nullable();
            
            $table->string('phone_code', 10)->comment('کد تلفن کشور')->nullable();
            
            $table->string('capital', 100)->comment('نام پایتخت')->nullable();
            
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();


            $table->comment('جدول اطلاعات کشورها');
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول countries در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};