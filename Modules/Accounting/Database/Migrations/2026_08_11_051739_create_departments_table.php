<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول departments برای مدیریت ساختار سازمانی
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام دپارتمان‌های مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی دپارتمان (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام دپارتمان
            $table->string('name', 100);
            
            // دپارتمان والد (برای ساختار سلسله‌مراتبی)
            // اگر null باشد، به معنای دپارتمان سطح بالا (ریشه) است
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('departments')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // مدیر دپارتمان (ارتباط با جدول users یا employees)
            $table->foreignId('manager_id')
                  ->nullable()
                  ->constrained('users') // یا employees
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد دپارتمان در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس ترکیبی برای جستجوی سریع‌تر دپارتمان‌های یک شرکت
            $table->index(['company_id', 'is_active']);
            $table->index(['parent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول departments در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};