<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionNameDepartmentNameDivisionIdDepartmentIdInPeriodicTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('periodic_tickets') && !Schema::hasColumn('periodic_tickets', 'division_name')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->string('division_name')->nullable()->after('status');
            });
        }

        if(Schema::hasTable('periodic_tickets') && !Schema::hasColumn('periodic_tickets', 'department_name')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->string('department_name')->nullable()->after('division_name');
            });
        }

        if (Schema::hasTable('periodic_tickets') && !Schema::hasColumn('periodic_tickets', 'division_id')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->string('division_id')->nullable()->after('department_name');
            });
        }

        if (Schema::hasTable('periodic_tickets') && !Schema::hasColumn('periodic_tickets', 'department_id')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->string('department_id')->nullable()->after('division_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('periodic_tickets') && Schema::hasColumn('periodic_tickets', 'division_name')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->dropColumn('division_name');
            });
        }

        if (Schema::hasTable('periodic_tickets') && Schema::hasColumn('periodic_tickets', 'department_name')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->dropColumn('department_name');
            });
        }

        if (Schema::hasTable('periodic_tickets') && Schema::hasColumn('periodic_tickets', 'division_id')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->dropColumn('division_id');
            });
        }

        if (Schema::hasTable('periodic_tickets') && Schema::hasColumn('periodic_tickets', 'department_id')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->dropColumn('department_id');
            });
        }
    }
}
