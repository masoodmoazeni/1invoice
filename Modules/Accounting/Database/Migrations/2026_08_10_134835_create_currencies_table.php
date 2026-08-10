<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول currencies با فیلدهای مورد نیاز
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // کد ارز بر اساس استاندارد ISO 4217 (مثلاً USD, EUR, IRR)
            // از نوع char با طول 3 برای کدهای سه‌حرفی
            $table->char('code', 3)->unique()->index();
            
            // نام کامل ارز به انگلیسی (مثلاً US Dollar, Euro, Iranian Rial)
            $table->string('name', 100);
            
            // نماد ارز (مثلاً $, €, ﷼, £)
            $table->string('symbol', 10)->nullable();
            
            // تعداد ارقام اعشاری (مثلاً 2 برای دلار، 0 برای rial)
            // برای اکثر ارزها 2 است اما برخی مانند rial و yen 0 دارند
            $table->unsignedTinyInteger('decimal_places')->default(2);
            
            // مقدار گرد کردن (برای محاسبات مالی)
            // مثلاً 0.01 برای دلار، 1 برای rial
            $table->decimal('rounding', 10, 4)->default(0.01);
            
            // وضعیت فعال/غیرفعال (پیش‌فرض فعال)
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول currencies در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};