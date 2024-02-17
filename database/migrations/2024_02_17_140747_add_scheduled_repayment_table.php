<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduledRepaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('scheduled_repayments', function (Blueprint $table) {
            $table->integer('amount');
            $table->string('currency_code');
            $table->date('processed_at');
            $table->integer('outstanding_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('scheduled_repayments', function (Blueprint $table) {
			$table->dropColumn('currency_code','amount','processed_at','outstanding_amount');
        });
    }
}
