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
            $table->id();

            $table->unsignedBigInteger('company_id')->comment('ارتباط با شرکت (برای سیستم‌های چندشرکتی) - در صورت حذف شرکت، تمام شعب مربوطه نیز حذف می‌شوند');

            $table->string('code', 50)->comment('کد شناسایی شعبه (منحصر‌به‌فرد برای هر شرکت)');
            
            $table->string('name', 100)->comment('نام شعبه');
            
            $table->unsignedBigInteger('country_id')->comment('ارتباط با کشور');

            $table->string('city', 100)->comment('نام شهر')->nullable();
            
            $table->text('address')->comment('آدرس کامل شعبه')->nullable();
            
            $table->string('phone', 20)->comment('شماره تلفن شعبه')->nullable();
            
            $table->string('email', 100)->comment('آدرس ایمیل شعبه')->nullable()->unique();
            
            $table->unsignedBigInteger('manager_id')->comment('مدیر شعبه (ارتباط با جدول users یا employees)');

            $table->boolean('is_default')->default(false)->comment('آیا این شعبه به‌عنوان شعبه پیش‌فرض شرکت است؟')->index();
            
            $table->boolean('is_active')->comment('وضعیت فعال/غیرفعال')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);

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