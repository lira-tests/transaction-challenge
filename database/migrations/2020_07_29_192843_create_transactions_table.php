<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->foreignUuid('payer_user_id')->references('id')->on('users');
            $table->foreignUuid('payee_user_id')->references('id')->on('users');
            $table->float('amount', 8, 2);
            $table->string('reason');
            $table->enum('status', ['created', 'executed', 'failed'])
                ->default('created');
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
        Schema::dropIfExists('transactions');
    }
}
