<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCxoNotificationConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cxo_notification_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('email_receiver')->nullable();
            $table->text('dynamic_content')->nullable();
            $table->text('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->enum('type', ['Monthly','Weekly','Daily'])->default('Monthly');
            $table->integer('day')->nullable();
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
        Schema::dropIfExists('cxo_notification_configs');
    }
}
