<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxoNotificationConfig extends Model
{
    use HasFactory;
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql';


    /**
     * @var string
     */
    protected $table = "cxo_notification_configs";

    /**
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email_subject',
        'email_body',
        'dynamic_content',
        'email_receiver',
        'email_receiver_cc',
        'type',
        'day',
    ];

}
