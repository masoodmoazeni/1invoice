<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول number_sequences برای مدیریت شماره‌گذاری خودکار اسناد
     * این جدول امکان تعریف الگوهای شماره‌گذاری برای انواع مختلف اسناد را فراهم می‌کند
     *
     * @return void
     */
    public function up()
    {
        Schema::create('number_sequences', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (سیستم چندشرکتی)
            // در صورت حذف شرکت، تمام شماره‌گذاری‌های مربوطه نیز حذف می‌شوند
            $table->unsignedBigInteger('company_id');

            // ماژول یا بخش مربوطه (مثلاً sales, purchase, accounting, hr)
            $table->string('module', 50)->index();

            // پیشوند شماره (مثلاً INV-, PO-, REC-)
            $table->string('prefix', 20)->nullable();

            // پسوند شماره (مثلاً -A, -B)
            $table->string('suffix', 20)->nullable();

            // شماره جاری (آخرین شماره استفاده شده)
            $table->unsignedBigInteger('current_number')->default(0);

            // تعداد ارقام شماره (برای صفرگذاری)
            $table->unsignedTinyInteger('padding')->default(5);

            // نوع ریست شماره‌گذاری
            // daily: روزانه, monthly: ماهانه, yearly: سالانه, never: هرگز
            $table->enum('reset_type', [
                'daily',
                'monthly',
                'yearly',
                'never'
            ])->default('yearly')->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            $table->softDeletes();

            // ایندکس ترکیبی برای جلوگیری از تکرار شماره‌گذاری در هر ماژول و شرکت
            $table->unique(['company_id', 'module', 'prefix', 'suffix']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'module']);
            $table->index(['company_id', 'reset_type']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول number_sequences در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('number_sequences');
    }
};
