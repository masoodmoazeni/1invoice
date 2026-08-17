<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول journals برای مدیریت دفترهای روزنامه
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('code', 50)->comment('کد شناسایی دفتر روزنامه (منحصر‌به‌فرد برای هر شرکت)');
            
            $table->string('name', 100)->comment('نام دفتر روزنامه');
            
            $table->enum('type', [
                'general',      // دفتر روزنامه عمومی
                'sales',        // دفتر روزنامه فروش
                'purchase',     // دفتر روزنامه خرید
                'cash',         // دفتر روزنامه نقدی
                'bank',         // دفتر روزنامه بانکی
                'salary',       // دفتر روزنامه حقوق و دستمزد
                'inventory',    // دفتر روزنامه انبار
                'adjustment',   // دفتر روزنامه تعدیلات
                'closing'       // دفتر روزنامه اختتامیه
            ])->default('general')->comment('نوع دفتر روزنامه')->index();

            $table->boolean('is_default')->default(false)->comment('آیا این دفتر روزنامه به‌عنوان پیش‌فرض استفاده شود؟')->index();
            
            $table->boolean('is_active')->default(true)->comment('وضعیت فعال/غیرفعال')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);

            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'is_default', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول journals در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journals');
    }
};