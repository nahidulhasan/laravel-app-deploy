<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailReceiveCcToReminderNotificationConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('reminder_notification_configs')) {
            if (!Schema::hasColumn('reminder_notification_configs', 'email_receiver_cc')) {
                Schema::table('reminder_notification_configs', function (Blueprint $table) {
                    $table->string('email_receiver_cc')->after('email_receiver')->nullable();
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
        if (!Schema::hasColumn('reminder_notification_configs', 'email_receiver_cc')) {
            Schema::table('reminder_notification_configs', function (Blueprint $table) {
                $table->dropColumn('email_receiver_cc');
            });
        }
    }
}
