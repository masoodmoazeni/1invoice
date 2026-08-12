<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول currencies با فیلدهای مورد نیاز
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->char('code', 5)->unique()->comment('کد ارز بر اساس استاندارد ISO 4217 (مثلاً USD, EUR, IRR)')->index();
            
            $table->string('name', 100)->comment('نام کامل ارز به انگلیسی (مثلاً US Dollar, Euro, Iranian Rial)');
            
            $table->string('symbol', 10)->comment('نماد ارز (مثلاً $, €, ﷼, £)')->nullable();
            
            $table->unsignedTinyInteger('decimal_places')->default(2);
            
            $table->decimal('rounding', 10, 4)->default(0.01);
            
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->comment('جدول اطلاعات ارز ها');
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول currencies در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};