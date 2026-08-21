<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول partners برای مدیریت طرف‌های حساب (مشتریان، تامین‌کنندگان، کارمندان و ...)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (سیستم چندشرکتی)
            $table->unsignedBigInteger('company_id');

            // کد شناسایی طرف حساب (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نوع طرف حساب
            $table->enum('partner_type', [
                'customer',      // مشتری
                'vendor',        // تامین‌کننده
                'employee',      // کارمند
                'bank',          // بانک
                'government',    // سازمان دولتی
                'other'          // سایر
            ])->default('customer')->index();

            // نام اصلی
            $table->string('name', 100);
            
            // نام حقوقی (برای شرکت‌ها و سازمان‌ها)
            $table->string('legal_name', 100)->nullable();
            
            // نام نمایشی (برای نمایش در رابط کاربری)
            $table->string('display_name', 100)->nullable();
            
            // شماره مالیاتی
            $table->string('tax_number', 50)->nullable();
            
            // شماره ثبت (برای شرکت‌ها)
            $table->string('registration_number', 50)->nullable();
            
            // کشور
            $table->unsignedBigInteger('country_id');

            // استان
            $table->string('state', 100)->nullable();
            
            // شهر
            $table->string('city', 100)->nullable();
            
            // آدرس
            $table->text('address')->nullable();
            
            // کد پستی
            $table->string('postal_code', 20)->nullable();
            
            // تلفن
            $table->string('phone', 20)->nullable();
            
            // موبایل
            $table->string('mobile', 20)->nullable();
            
            // ایمیل
            $table->string('email', 100)->nullable()->index();
            
            // وب‌سایت
            $table->string('website', 100)->nullable();
            
            // ارز پیش‌فرض
            $table->unsignedBigInteger('currency_id');

            // سقف اعتباری
            $table->decimal('credit_limit', 15, 2)->default(0);
            
            // شرایط پرداخت پیش‌فرض
            $table->unsignedBigInteger('payment_term_id');

            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            
            // حذف نرم (soft delete)
            $table->softDeletes();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد طرف حساب در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'partner_type']);
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'name']);
            $table->index(['company_id', 'email']);
            $table->index(['company_id', 'city']);
            $table->index(['partner_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول partners در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
};
