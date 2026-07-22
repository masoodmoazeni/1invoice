<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('package_orders', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('list_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('package_type')->comment('type of package');

            $table->decimal('total_amount', 10, 2)->nullable();
            
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('items')->nullable();
            $table->json('responsedata')->nullable()->comment('response from webwook');

            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=active, 2=cancelled');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_orders');
    }
};
