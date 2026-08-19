<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول account_templates برای مدیریت قالب‌های نمودار حساب‌ها
     * هر قالب شامل مجموعه‌ای از حساب‌های از پیش تعریف شده است
     * که می‌تواند بر اساس کشور یا استانداردهای مختلف باشد
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_templates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('country_id');

            $table->string('name', 100)->comment('نام قالب (مثلاً "نمودار حساب‌های استاندارد ایران")');
            
            $table->string('version', 20)->default('1.0.0')->comment('نسخه قالب (برای مدیریت تغییرات و به‌روزرسانی‌ها)');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['country_id', 'name']);

            $table->index(['country_id', 'version']);

            $this->comment('ایجاد جدول account_templates برای مدیریت قالب‌های نمودار حساب‌ها');
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول account_templates در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_templates');
    }
};