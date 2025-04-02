<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicTicket extends Model
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
    protected $table = "periodic_tickets";

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'compliance_entry_id',
        'periodic_ticket_id',
        'due_date',
        'status',
        'ticket_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function complianceOwner(){
        return $this->belongsTo(ComplianceEntry::class, 'compliance_entry_id', 'id');
    }
    public function complianceEntry(){
        return $this->belongsTo(ComplianceEntry::class, 'compliance_entry_id', 'id');
    }
    
}