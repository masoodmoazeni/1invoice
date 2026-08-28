<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول files برای مدیریت فایل‌های ذخیره شده در سیستم
     * این جدول امکان ذخیره اطلاعات متادیتا و مدیریت فایل‌ها را فراهم می‌کند
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // دیسک ذخیره‌سازی (local, public, s3, etc.)
            $table->string('disk', 50)->default('local')->index();

            // مسیر فایل در دیسک
            $table->string('path', 500);

            // هش فایل (برای تشخیص فایل‌های تکراری)
            $table->string('hash', 64)->unique()->index();

            // حجم فایل به بایت
            $table->unsignedBigInteger('size')->default(0);

            // نوع MIME فایل
            $table->string('mime', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();
            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['disk', 'path']);
            $table->index(['mime']);
            $table->index(['size']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول files در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
