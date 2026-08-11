<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول dimension_values برای مدیریت مقادیر ابعاد مالی
     * هر بعد می‌تواند دارای چندین مقدار باشد (مانند مراکز هزینه مختلف)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dimension_values', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با بعد (هر مقدار متعلق به یک بعد است)
            // در صورت حذف بعد، تمام مقادیر مربوطه نیز حذف می‌شوند
            $table->foreignId('dimension_id')
                  ->constrained('dimensions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی مقدار (منحصر‌به‌فرد برای هر بعد)
            $table->string('code', 50);
            
            // نام مقدار
            $table->string('name', 100);
            
            // مقدار والد (برای ساختار سلسله‌مراتبی)
            // اگر null باشد، به معنای مقدار سطح بالا (ریشه) است
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('dimension_values')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد مقدار در هر بعد
            $table->unique(['dimension_id', 'code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['dimension_id', 'parent_id']);
            $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول dimension_values در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dimension_values');
    }
};