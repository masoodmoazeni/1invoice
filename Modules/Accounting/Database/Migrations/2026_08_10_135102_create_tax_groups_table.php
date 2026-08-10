<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول tax_groups برای گروه‌بندی مالیات‌ها
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_groups', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام گروه‌های مالیاتی مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی گروه مالیاتی (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام گروه مالیاتی (مثلاً مالیات‌های استاندارد، مالیات‌های ویژه)
            $table->string('name', 100);
            
            // توضیحات تکمیلی درباره گروه مالیاتی
            $table->text('description')->nullable();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد گروه مالیاتی در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس ترکیبی برای جستجوی سریع‌تر گروه‌های مالیاتی یک شرکت
            $table->index(['company_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول tax_groups در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_groups');
    }
};