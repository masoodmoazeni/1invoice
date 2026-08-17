<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول departments برای مدیریت ساختار سازمانی
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->comment('در صورت حذف شرکت، تمام دپارتمان‌های مربوطه نیز حذف می‌شوند');

            $table->string('code', 50)->comment('کد شناسایی دپارتمان (منحصر‌به‌فرد برای هر شرکت)');
            
            $table->string('name', 100)->comment('نام دپارتمان');
            
            $table->unsignedBigInteger('parent_id')->comment('دپارتمان والد (برای ساختار سلسله‌مراتبی) اگر null باشد، به معنای دپارتمان سطح بالا (ریشه) است');

            $table->unsignedBigInteger('manager_id')->comment('مدیر دپارتمان (ارتباط با جدول users یا employees)');

            $table->boolean('is_active')->default(true)->comment('وضعیت فعال/غیرفعال')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);

            $table->index(['company_id', 'is_active']);
            $table->index(['parent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول departments در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};