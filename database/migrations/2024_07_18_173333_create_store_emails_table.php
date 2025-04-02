<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_emails', function (Blueprint $table) {
            $table->id();
            $table->text('receiver')->nullable();
            $table->text('subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->string('report_type')->nullable();
            $table->string('status')->default('Waiting');
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
        Schema::dropIfExists('store_emails');
    }
}
