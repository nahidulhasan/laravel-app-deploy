<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplianceEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_entry', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->nullable(false);
            $table->string('regulatory_body')->nullable();
            $table->string('compliance_point_no')->nullable();
            $table->string('compliance_level')->nullable();
            $table->string('compliance_category')->nullable();
            $table->string('compliance_sub_category')->nullable();
            $table->text('compliance_point_description')->nullable();
            $table->string('instruction_type')->nullable();
            $table->string('document_subject',500)->nullable();
            $table->date('document_date')->nullable();
            $table->string('section_no_as_per_document',500)->nullable();
            $table->string('compliance_applicable_for')->nullable();
            $table->date('start_date')->nullable();
            $table->string('frequency')->nullable();
            $table->date('due_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->string('payment_penalty_implication_risk',500)->nullable();
            $table->string('compliance_owner')->nullable();
            $table->string('remarks',500)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compliance_entry');
    }
}
