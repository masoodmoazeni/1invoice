<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول payment_terms برای مدیریت شرایط پرداخت
     * این جدول شامل شرایط پرداخت مانند نقدی، مدت‌دار، چند قسطی و غیره است
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (سیستم چندشرکتی)
            // در صورت حذف شرکت، تمام شرایط پرداخت مربوطه نیز حذف می‌شوند
            $table->unsignedBigInteger('company_id');

            // کد شناسایی شرط پرداخت (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام شرط پرداخت
            $table->string('name', 100);
            
            // توضیحات
            $table->text('description')->nullable();
            
            // تعداد روزهای سررسید (برای شرایط مدت‌دار)
            $table->unsignedInteger('due_days')->default(0);
            
            // آیا پرداخت فوری است؟ (نقدی)
            $table->boolean('is_immediate')->default(false)->index();
            
            // آیا این شرط به‌عنوان پیش‌فرض استفاده شود؟
            $table->boolean('is_default')->default(false)->index();
            
            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد شرط پرداخت در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'is_default', 'is_active']);
            $table->index(['company_id', 'is_immediate']);
            $table->index(['due_days']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول payment_terms در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_terms');
    }
};
