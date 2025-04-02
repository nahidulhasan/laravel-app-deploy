<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodicTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodic_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('compliance_entry_id')->index();
            $table->string('periodic_ticket_id');
            $table->date('due_date')->comment('Same as compliance entry table next due date');
            $table->enum('status', ['created', 'completed', 'cancelled', 'escalated', 'reminder'])->default('created');
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
        Schema::dropIfExists('periodic_tickets');
    }
}
