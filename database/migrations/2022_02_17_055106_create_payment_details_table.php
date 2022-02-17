<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(1)->comment('1 for active and 0 for inactive');
            $table->integer('loan_id');
            $table->timestamp('last_repayment_date')->nullable();
            $table->timestamp('next_repayment_date')->nullable();
            $table->integer('installment_amount');
            $table->integer('pending_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
}
