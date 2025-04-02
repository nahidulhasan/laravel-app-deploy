<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionNameDepartmentNameDivisionIdDepartmentIdInComplianceEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('compliance_entry') && !Schema::hasColumn('compliance_entry', 'division_name')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->string('division_name')->nullable()->after('designation');
            });
        }

        if(Schema::hasTable('compliance_entry') && !Schema::hasColumn('compliance_entry', 'department_name')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->string('department_name')->nullable()->after('division_name');
            });
        }

        if (Schema::hasTable('compliance_entry') && !Schema::hasColumn('compliance_entry', 'division_id')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->string('division_id')->nullable()->after('department_name');
            });
        }

        if (Schema::hasTable('compliance_entry') && !Schema::hasColumn('compliance_entry', 'department_id')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
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
        if (Schema::hasTable('compliance_entry') && Schema::hasColumn('compliance_entry', 'division_name')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->dropColumn('division_name');
            });
        }

        if (Schema::hasTable('compliance_entry') && Schema::hasColumn('compliance_entry', 'department_name')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->dropColumn('department_name');
            });
        }

        if (Schema::hasTable('compliance_entry') && Schema::hasColumn('compliance_entry', 'division_id')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->dropColumn('division_id');
            });
        }

        if (Schema::hasTable('compliance_entry') && Schema::hasColumn('compliance_entry', 'department_id')) {
            Schema::table('compliance_entry', function (Blueprint $table) {
                $table->dropColumn('department_id');
            });
        }
    }
}
