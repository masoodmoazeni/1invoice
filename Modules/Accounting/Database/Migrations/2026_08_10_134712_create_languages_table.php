<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول languages با فیلدهای مورد نیاز
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();

            $table->char('code', 5)->unique()->coment('کد زبان بر اساس استاندارد (مثلاً fa, en, ar, fr)')->index();
            
            $table->string('name', 100)->comment('نام کامل زبان به انگلیسی (مثلاً Persian, English, Arabic)');
            
            $table->string('native_name', 100)->comment('نام بومی زبان (مثلاً فارسی, English, العربية)')->nullable();
            
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr')->index();
            
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->comment('جدول اطلاعات زبانی');
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول languages در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
};