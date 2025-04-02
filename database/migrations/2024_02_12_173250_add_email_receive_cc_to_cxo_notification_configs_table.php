<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailReceiveCcToCxoNotificationConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('cxo_notification_configs', 'email_receiver_cc')) {
            Schema::table('cxo_notification_configs', function (Blueprint $table) {
                $table->string('email_receiver_cc')->after('email_receiver')->nullable();
            });
        }

        if (Schema::hasTable('settings')) {
            Schema::rename('settings', 'reminder_notification_configs');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('cxo_notification_configs', 'email_receiver_cc')) {
            Schema::table('cxo_notification_configs', function (Blueprint $table) {
                $table->dropColumn('email_receiver_cc');
            });
        }
    }
}
