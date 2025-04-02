<?php

namespace App\Repositories;

use App\Models\PeriodicTicket;

/**
 * Class GroupRepository
 * @package App\Repositories
 */
class PeriodicTicketRepository  extends BaseRepository
{
    protected $modelName = PeriodicTicket::class;

    public function __construct(PeriodicTicket $model)
    {
        $this->model = $model;
    }
    public function emtTotalPeriodicComplianceTicket($startDate,$endDate,$ComplianceOwner){
        return PeriodicTicket::with(['complianceOwner'])
            ->whereHas('complianceOwner', function($q) use($ComplianceOwner) {
                $q->whereIn('compliance_owner', $ComplianceOwner);
            })
            ->whereBetween('due_date',[$startDate,$endDate])
            ->get();

    }

    public function lastMonthTotalPeriodicComplianceTicket($startDate,$endDate,$ComplianceOwner){
       return PeriodicTicket::with(['complianceOwner'])
           ->whereHas('complianceOwner', function($q) use($ComplianceOwner) {
               $q->whereIn('compliance_owner', $ComplianceOwner);
           })
           ->whereBetween('due_date',[$startDate,$endDate])
           ->count();

    }

    public function lastMonthTotalPeriodicNonComplianceTicket($startDate,$endDate,$ComplianceOwner){
       return PeriodicTicket::with(['complianceOwner'])
           ->whereHas('complianceOwner', function($q) use($ComplianceOwner) {
               $q->whereIn('compliance_owner', $ComplianceOwner);
           })
           ->whereBetween('due_date',[$startDate,$endDate])
           ->whereNotIn('status',['completed','cancelled'])
           ->count();
    }

    public function totalComplianceTicket($startDate,$endDate,$ComplianceOwner){
       return PeriodicTicket::with(['complianceOwner'])
           ->whereHas('complianceOwner', function($q) use($ComplianceOwner) {
               $q->whereIn('compliance_owner', $ComplianceOwner);
           })
           ->where('due_date','<=',$endDate)
           ->whereNotIn('status',['completed','cancelled'])
           ->count();
    }

}
