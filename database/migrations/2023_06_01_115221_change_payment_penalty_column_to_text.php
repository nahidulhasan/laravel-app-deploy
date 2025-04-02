<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePaymentPenaltyColumnToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text', function (Blueprint $table) {
            //
        });
        Schema::table('compliance_entry', function (Blueprint $table) {
            $table->text('payment_penalty_implication_risk')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compliance_entry', function (Blueprint $table) {
            $table->string('payment_penalty_implication_risk', 500)->change();
        });
    }
}
