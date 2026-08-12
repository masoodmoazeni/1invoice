<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Schema::create('packages', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('parent_id')->nullable(); // bigIncrements نمی‌تواند nullable باشد
        //     $table->string('title');
        //     $table->text('description')->nullable();

        //     $table->enum('type_package', ['diy', 'broker'])->default('diy')->comment('type package for diy or broker');
        //     $table->enum('type_stripe', ['one-time', 'subscription'])->default('one-time')->comment('type package for one-time or subscription');
        //     $table->enum('purchaseType', ['hourly', 'daily', 'weekly', 'monthly', 'yearly'])->default('daily');
        //     $table->integer('minQuantity');
            
        //     $table->decimal('price', 10, 2)->comment('price for one-time payment')->default(0);
        //     $table->decimal('monthly_price', 10, 2)->comment('price for subscription payment')->default(0);
            
        //     $table->string('currency', 3)->default('usd');

        //     $table->string('stripe_product_id')->nullable();
        //     $table->string('stripe_price_id')->nullable()->comment('id price for one-time payment');

        //     $table->tinyInteger('main_package')->nullable()->comment('main package');

        //     $table->tinyInteger('status')->default(1)->comment('0=> inactive, 1=> active');

        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
