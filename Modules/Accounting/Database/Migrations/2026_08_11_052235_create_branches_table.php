<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول branches برای مدیریت شعب شرکت
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام شعب مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی شعبه (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام شعبه
            $table->string('name', 100);
            
            // ارتباط با کشور
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('restrict') // جلوگیری از حذف کشوری که شعبه دارد
                  ->onUpdate('cascade')
                  ->index();

            // نام شهر
            $table->string('city', 100)->nullable();
            
            // آدرس کامل شعبه
            $table->text('address')->nullable();
            
            // شماره تلفن شعبه
            $table->string('phone', 20)->nullable();
            
            // آدرس ایمیل شعبه
            $table->string('email', 100)->nullable()->unique();
            
            // مدیر شعبه (ارتباط با جدول users یا employees)
            $table->foreignId('manager_id')
                  ->nullable()
                  ->constrained('users') // یا employees
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // آیا این شعبه به‌عنوان شعبه پیش‌فرض شرکت است؟
            $table->boolean('is_default')->default(false)->index();
            
            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد شعبه در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس ترکیبی برای جستجوی سریع‌تر شعب یک شرکت
            $table->index(['company_id', 'is_active']);
            $table->index(['country_id', 'city']);
            $table->index(['is_default', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول branches در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
};