<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceEntry extends Model
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
    protected $table = "compliance_entry";

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $guarded = [];
    public function periodicTicket(){
        return $this->hasMany(PeriodicTicket::class,'compliance_entry_id','id');
//            ->select(['periodic_ticket_id','due_date']);
    }
}
