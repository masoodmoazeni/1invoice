<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول journals برای مدیریت دفترهای روزنامه
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام دفترهای روزنامه مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی دفتر روزنامه (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام دفتر روزنامه
            $table->string('name', 100);
            
            // نوع دفتر روزنامه
            $table->enum('type', [
                'general',      // دفتر روزنامه عمومی
                'sales',        // دفتر روزنامه فروش
                'purchase',     // دفتر روزنامه خرید
                'cash',         // دفتر روزنامه نقدی
                'bank',         // دفتر روزنامه بانکی
                'salary',       // دفتر روزنامه حقوق و دستمزد
                'inventory',    // دفتر روزنامه انبار
                'adjustment',   // دفتر روزنامه تعدیلات
                'closing'       // دفتر روزنامه اختتامیه
            ])->default('general')->index();

            // آیا این دفتر روزنامه به‌عنوان پیش‌فرض استفاده شود؟
            $table->boolean('is_default')->default(false)->index();
            
            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد دفتر روزنامه در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'is_default', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول journals در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journals');
    }
};