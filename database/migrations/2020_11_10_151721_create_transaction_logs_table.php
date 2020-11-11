<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id('id')->comment('factor id');
            $table->bigInteger('sales_id');
            $table->unsignedInteger('price');
            $table->string('source',45);
            $table->string('selected_gateway',45);
            $table->string('final_gateway',45);
            $table->string('status',45)->default('init');
            $table->string('transaction_id')->nullable();
            $table->string('error_message')->nullable();
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
        Schema::dropIfExists('transaction_logs');
    }
}
