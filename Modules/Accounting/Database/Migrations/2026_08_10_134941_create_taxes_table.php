<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول taxes با فیلدهای مورد نیاز و ارتباط با سایر جداول
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام مالیات‌های مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با کشور
            // در صورت حذف کشور، مالیات‌های مربوطه نیز حذف می‌شوند
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی مالیات (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام مالیات (مثلاً مالیات بر ارزش افزوده، مالیات بر درآمد)
            $table->string('name', 100);
            
            // توضیحات تکمیلی
            $table->text('description')->nullable();
            
            // نرخ مالیات (مثلاً 9% برای VAT)
            $table->decimal('rate', 5, 2)->default(0);
            
            // نوع مالیات: درصدی یا مبلغ ثابت
            $table->enum('tax_kind', ['percentage', 'fixed'])->default('percentage');
            
            // روش محاسبه: قبل از تخفیف یا بعد از تخفیف
            $table->enum('calculation_method', ['before_discount', 'after_discount', 'exclusive'])
                  ->default('before_discount');
            
            // شناسه حساب مالیاتی در جدول حساب‌ها (در صورت وجود)
            $table->foreignId('account_id')
                  ->nullable()
                  ->constrained('accounts')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // آیا مالیات در قیمت لحاظ شده است؟ (مالیات درون‌زا)
            $table->boolean('is_inclusive')->default(false);
            
            // آیا این مالیات به‌عنوان پیش‌فرض استفاده شود؟
            $table->boolean('is_default')->default(false);
            
            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد مالیات در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس ترکیبی برای جستجوی سریع‌تر مالیات‌های پیش‌فرض و فعال
            $table->index(['company_id', 'is_default', 'is_active']);
            $table->index(['country_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول taxes در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};