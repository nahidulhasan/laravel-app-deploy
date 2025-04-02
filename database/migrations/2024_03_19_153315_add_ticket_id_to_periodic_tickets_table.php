<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketIdToPeriodicTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('periodic_tickets')) {
            if (!Schema::hasColumn('periodic_tickets', 'ticket_id')) {
                Schema::table('periodic_tickets', function (Blueprint $table) {
                    $table->string('ticket_id')->after('compliance_entry_id')->nullable();
                });
            }

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('periodic_tickets', 'ticket_id')) {
            Schema::table('periodic_tickets', function (Blueprint $table) {
                $table->dropColumn('ticket_id');
            });
        }
    }
}
