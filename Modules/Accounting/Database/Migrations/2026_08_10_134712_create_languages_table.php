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
            // شناسه اصلی (auto-increment)
            $table->id();

            // کد زبان بر اساس استاندارد (مثلاً fa, en, ar, fr)
            // از نوع char با طول 2 برای کدهای دوحرفی ISO 639-1
            $table->char('code', 2)->unique()->index();
            
            // نام کامل زبان به انگلیسی (مثلاً Persian, English, Arabic)
            $table->string('name', 100);
            
            // نام بومی زبان (مثلاً فارسی, English, العربية)
            $table->string('native_name', 100)->nullable();
            
            // جهت نوشتار: rtl (راست به چپ) یا ltr (چپ به راست)
            // با مقدار پیش‌فرض ltr برای اکثر زبان‌ها
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr')->index();
            
            // وضعیت فعال/غیرفعال (پیش‌فرض فعال)
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی (created_at, updated_at)
            $table->timestamps();
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