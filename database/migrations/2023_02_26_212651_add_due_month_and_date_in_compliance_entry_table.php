<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDueMonthAndDateInComplianceEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // delete due_date column of previous type
        if (Schema::hasColumn('compliance_entry', 'due_date')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->dropColumn('due_date');
            });
        }
        Schema::table('compliance_entry', function (Blueprint $table) {
            $table->integer('due_date')->after('frequency')->nullable();
            $table->integer('due_month')->after('due_date')->nullable();
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
            //
        });
    }
}
