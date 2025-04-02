<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionNameAndDepartmentNameInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('users') && !Schema::hasColumn('users', 'mobile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('mobile',15)->nullable();
                $table->string('designation',255)->nullable();
            });
        }


        if(Schema::hasTable('users') && !Schema::hasColumn('users', 'division_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('division_name')->nullable()->after('designation');
            });
        }

        if(Schema::hasTable('users') && !Schema::hasColumn('users', 'department_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('department_name')->nullable()->after('designation');
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
        if (Schema::hasColumn('users', 'division_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('division_name');
            });
        }

        if (Schema::hasColumn('users', 'department_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('department_name');
            });
        }
    }
}
