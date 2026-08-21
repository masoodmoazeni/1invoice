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
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('code', 50)->comment('کد شناسایی بعد (منحصر‌به‌فرد برای هر شرکت)');
            
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

            $table->boolean('is_active')->comment('وضعیت فعال/غیرفعال')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);

            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'is_active']);
            $table->index(['type', 'is_active']);

            // اگر می‌خواهید روی خود جدول کامنت بگذارید:
            // $table->comment('جدول dimensions برای مدیریت ابعاد مالی');
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