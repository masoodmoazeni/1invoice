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
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->unsignedBigInteger('country_id');

            $table->string('code', 50)->comment('کد شناسایی مالیات (منحصر‌به‌فرد برای هر شرکت)');
            
            $table->string('name', 100)->comment('نام مالیات (مثلاً مالیات بر ارزش افزوده، مالیات بر درآمد)');
            
            $table->text('description')->nullable();
            
            $table->decimal('rate', 5, 2)->comment('نرخ مالیات (مثلاً 9% برای VAT)')->default(0);
            
            $table->enum('tax_kind', ['percentage', 'fixed'])->default('percentage')->comment('نوع مالیات: درصدی یا مبلغ ثابت');
            
            $table->enum('calculation_method', ['before_discount', 'after_discount', 'exclusive'])
                  ->default('before_discount');
            
            $table->unsignedBigInteger('account_id');

            $table->boolean('is_inclusive')->comment('آیا مالیات در قیمت لحاظ شده است؟ (مالیات درون‌زا)')->default(false);
            
            $table->boolean('is_default')->default(false);
            
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);

            $table->index(['company_id', 'is_default', 'is_active']);
            $table->index(['country_id', 'is_active']);

            $table->comment('جدول اطلاعات مالیات ها');
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