<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول dimensions برای مدیریت ابعاد مالی (مراکز هزینه، پروژه‌ها، دپارتمان‌ها و ...)
     * ابعاد مالی برای گزارش‌گیری و تحلیل دقیق‌تر هزینه‌ها و درآمدها استفاده می‌شوند
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dimensions', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام ابعاد مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی بعد (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام بعد
            $table->string('name', 100);
            
            // نوع بعد: دسته‌بندی ابعاد مالی
            // cost_center: مرکز هزینه
            // project: پروژه
            // department: دپارتمان
            // customer: مشتری
            // vendor: تامین‌کننده
            // employee: کارمند
            // product: محصول
            // region: منطقه
            // custom: سفارشی
            $table->enum('type', [
                'cost_center',
                'project',
                'department',
                'customer',
                'vendor',
                'employee',
                'product',
                'region',
                'custom'
            ])->default('custom')->index();

            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد بعد در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول dimensions در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dimensions');
    }
};