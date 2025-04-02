<?php

namespace App\Repositories;

use App\Models\ComplianceEntry;
use App\Models\PeriodicTicket;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class GroupRepository
 * @package App\Repositories
 */
class ComplianceEntryRepository extends BaseRepository
{
    protected $modelName = ComplianceEntry::class;



    public function __construct(ComplianceEntry $model)
    {
        $this->model = $model;
    }

    /**
     * @param $ticketId
     * @return mixed
     */
    public function getLastComplianceEntry($ticketId)
    {
        return $this->model->where(['ticket_id' => $ticketId])->orderBy('created_at', 'desc')->first();
    }
    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getComplianceEntry()
    {
        $model = $this->getModel();
        $calculateD15Logic = Carbon::now()->addDays(15);
        $query = $model->whereDate('next_due_date', '<=', $calculateD15Logic)->where('status', 'Active');
        $complianceEntryes = $query->get();
        return $complianceEntryes;
    }
    /**
     * get compliance point no
     *
     * @param array $data
     * @return int
     */
    public function getCompliancePointCount(array $data)
    {
        $value = $data['compliance_point_no'];
        $query = $this->getModel()->newQuery();
        $query->where(['compliance_point_no' => $value]);
        return $query->get()->count();
    }

    /**
     * Get unique regulatory bodies.
     *
     * @return array
     */
    public function getUniqueRegulatoryBodies()
    {
        $ignoreBody = 'TEST';
        return $this->model
            ->whereNotNull('regulatory_body') // Exclude records where 'regulatory_body' is null
            ->whereRaw('LOWER(regulatory_body) <> LOWER(?)', [$ignoreBody]) // Exclude records where 'regulatory_body' is 'TEST'
            ->select('regulatory_body')
            ->distinct('regulatory_body') // Retrieve distinct values for 'regulatory_body'
            ->pluck('regulatory_body') // Pluck the values from the 'regulatory_body' column
            ->toArray(); // Convert the result to an array
    }

    /**
     * Retrieves unique divisions from the model.
     *
     * @return array
     */
    public function getUniqueDivisions()
    {
        return $this->model->whereNotNull(['division_name','division_id'])
            ->groupBy('division_name')
            ->select('division_name', DB::raw('MAX(division_id) AS division_id'))
            ->pluck('division_name', 'division_id')
            ->toArray();
    }

    /**
     * Get the total compliance for the dashboard.
     *
     * @param mixed $body 
     * @param mixed $division 
     * @param mixed $range 
     * @return array
     */
    public function getAssuredForDashboard($body = null, $division = null, array $options = [])
    {
        $ignoreBody = 'TEST';
        $data = ComplianceEntry::query()
            ->with([
                'periodicTicket'
            ])
            ->where('regulatory_body', '!=', $ignoreBody)
            ->whereNotNull(['division_id', 'regulatory_body'])
            ->when($body, function ($query) use ($body) {
                return $query->where('regulatory_body', $body);
            })
            ->when($division, function ($query) use ($division) {
                return $query->where('division_id', $division);
            })
            ->where('status', 'Active')
            ->has('periodicTicket')
            ->get()
            ->toArray();
        $total = $this->filterAssuredData($data);

        $month = $this->filterAssuredData($data, true);
        $responseData = $this->processResponseData($total, $month, $options);
        return $responseData;


    }

    /**
     * A description of the getUnassuredForDashboard PHP function.
     *
     * @param $body 
     * @param $division 
     * @param $range 
     * @return array
     */
    public function getUnassuredForDashboard($body, $division = null, array $options = [])
    {
        $ignoreBody = 'TEST';
        $data = ComplianceEntry::query()
            ->with([
                'periodicTicket' => function ($query) {
                    $query->where('status', 'created');
                }
            ])
            ->where('regulatory_body', '!=', $ignoreBody)
            ->whereNotNull(['division_id', 'regulatory_body'])
            ->when($body, function ($query) use ($body) {
                return $query->where('regulatory_body', $body);
            })
            ->when($division, function ($query) use ($division) {
                return $query->where('division_id', $division);
            })
            ->whereStatus('Active')
            ->has('periodicTicket')
            ->get()
            ->toArray();
        $total = $this->filterUnassuredData($data);
        $month = $this->filterUnassuredData($data, true);
        $responseData = $this->processResponseData($total, $month, $options);
        return $responseData;
    }

    /**
     * Get non-conscious compliance for dashboard.
     *
     * @param  $body description
     * @param  $division description
     * @param  $range description
     * @return array
     */
    public function getNonConsciousComplianceForDashboard($body, $division = null, $options = [])
    {
        $ignoreBody = 'TEST';
        $columns = $this->getComplianceEntryColumns();
        $total = ComplianceEntry::query()
            ->select($columns)
            ->with('periodicTicket')
            ->where('regulatory_body', '!=', $ignoreBody)
            ->whereNotNull(['division_id', 'regulatory_body'])
            ->when($body, function ($query) use ($body) {
                return $query->where('regulatory_body', $body);
            })->when($division, function ($query) use ($division) {
                return $query->where('division_id', $division);
            })
            ->whereStatus('conscious_non_compliance')
            ->get()
            ->each(function ($entry) {
                $entry->due_date = $this->getDueDateText($entry->frequency, $entry->due_date, $entry->due_month);
            })->toArray();
        foreach ($total as $key => $compliance) {
            $compliance['periodic_ticket_ids'] = implode(',', collect($compliance['periodic_ticket'])->pluck('periodic_ticket_id')->toArray());
            $total[$key] = $compliance;
        }
        $month = [];
        $responseBody = $this->processResponseData($total, $month, $options);
        return $responseBody;
    }

    /**
     * Get new compliance for dashboard.
     *
     * @param string $body
     * @param int $division
     * @param array $options
     * @return array
     */
    public function getNewComplianceForDashboard($body = null, $division = null, $options = [])
    {
        $ignoreBody = 'TEST';
        $columns = $this->getComplianceEntryColumns();
        $total = ComplianceEntry::query()
            ->select($columns)
            ->whereNotNull(['division_id', 'regulatory_body'])
            ->where('regulatory_body', '!=', $ignoreBody)
            ->when($body, function ($query) use ($body) {
                return $query->where('regulatory_body', $body);
            })->when($division, function ($query) use ($division) {
                return $query->where('division_id', $division);
            })
            ->whereStatus('Active')
            ->whereDoesntHave('periodicTicket')
            ->get()
            ->toArray();
        foreach ($total as $key => $compliance) {
            $compliance['periodic_ticket_ids'] = '';
            $total[$key] = $compliance;
        }
        $month = [];
        $responseBody = $this->processResponseData($total, $month, $options);
        return $responseBody;
    }
    /**
     * filter un-assured data
     * @param array $data
     * @param boolean $filterMonth
     * @return array
     */
    private function filterUnassuredData(array $data, $filterMonth = false)
    {
        $data = array_filter($data, function ($entry) {
            return !empty ($entry['periodic_ticket']);
        }, ARRAY_FILTER_USE_BOTH);
        $data = array_values($data);

        $processingDate = Carbon::now()->startOfDay();
        $allowed = [];
        foreach ($data as $key => $datum) {
            $allowedPeriodicTickets = [];
            if ($filterMonth) {
                // allow those periodic ticket which due date is in current month
                $datum['periodic_ticket'] = collect($datum['periodic_ticket'])->filter(function ($e) {
                    $dueDate = Carbon::parse($e['due_date']);
                    return $dueDate->isCurrentMonth();
                })->toArray();
                if (empty($datum['periodic_ticket'])) {
                    continue;
                }
            }
            foreach ($datum['periodic_ticket'] as $periodicTicket) {
                $dueDate = Carbon::parse($periodicTicket['due_date']);
                if ($filterMonth) {
                    if ($dueDate->isCurrentMonth()) {
                        if ($processingDate->isAfter($dueDate)) {
                            $allowedPeriodicTickets[] = $periodicTicket;
                        }
                    }
                } else {
                    if ($processingDate->isAfter($dueDate)) {
                        $allowedPeriodicTickets[] = $periodicTicket;
                    }
                }
            }
            if (count($allowedPeriodicTickets) > 0) {
                $datum['periodic_ticket'] = $allowedPeriodicTickets;
                $allowed[] = $datum;
            }
        }
        foreach ($allowed as $key => $compliance) {
            $compliance['due_date'] = $this->getDueDateText($compliance['frequency'], $compliance['due_date'], $compliance['due_month']);
            $compliance['periodic_ticket_ids'] = $this->getCompliancePeriodicTicketIds($compliance['periodic_ticket']);
            $allowed[$key] = $compliance;
        }
        return $allowed;
    }

    /**
     * filter assured data
     *
     * @param array $data
     * @param boolean $filterMonth
     * @return array
     */
    private function filterAssuredData(array $data, $filterMonth = false)
    {
        $data = array_filter($data, function ($entry) {
            return !empty ($entry['periodic_ticket']);
        }, ARRAY_FILTER_USE_BOTH);
        $data = array_values($data);
        $processingDate = Carbon::now()->startOfDay();
        $allowed = [];
        foreach ($data as $key => $datum) {
            $allowedPeriodicTickets = [];
            $completedPeriodicTickets = [];
            if ($filterMonth) {
                // allow those periodic ticket which due date is in current month
                $datum['periodic_ticket'] = collect($datum['periodic_ticket'])->filter(function ($e) {
                    $dueDate = Carbon::parse($e['due_date']);
                    return $dueDate->isCurrentMonth();
                })->toArray();
                if (empty($datum['periodic_ticket'])) {
                    continue;
                }
            }
            foreach ($datum['periodic_ticket'] as $periodicTicket) {
                $dueDate = Carbon::parse($periodicTicket['due_date']);
                if ($periodicTicket['status'] == 'created' && $dueDate->isBefore($processingDate)) {
                    $allowedPeriodicTickets = [];
                    break;
                }
                if ($filterMonth) {
                    if ($dueDate->isCurrentMonth()) {
                        if ($periodicTicket['status'] != 'created') {
                            $completedPeriodicTickets[] = $periodicTicket;
                        }
                        if ($periodicTicket['status'] == 'created') {
                            $isAllowedTicket = $this->isAllowedPeriodicTicket($periodicTicket, $dueDate, $processingDate);
                            if ($isAllowedTicket) {
                                $allowedPeriodicTickets[] = $isAllowedTicket;
                            }
                        }
                    }
                } else {
                    if ($periodicTicket['status'] != 'created') {
                        $completedPeriodicTickets[] = $periodicTicket;
                    }
                    if ($periodicTicket['status'] == 'created') {
                        $isAllowedTicket = $this->isAllowedPeriodicTicket($periodicTicket, $dueDate, $processingDate);
                        if ($isAllowedTicket) {
                            $allowedPeriodicTickets[] = $isAllowedTicket;
                        }
                    }
                }
            }
            if (count($completedPeriodicTickets) == count($datum['periodic_ticket'])) {
                $datum['periodic_ticket'] = $completedPeriodicTickets;
                $allowed[] = $datum;
            }
            if (count($allowedPeriodicTickets) > 0) {
                $datum['periodic_ticket'] = $allowedPeriodicTickets;
                $allowed[] = $datum;
            }
        }
        foreach ($allowed as $key => $compliance) {
            $compliance['due_date'] = $this->getDueDateText($compliance['frequency'], $compliance['due_date'], $compliance['due_month']);
            $compliance['periodic_ticket_ids'] = $this->getCompliancePeriodicTicketIds($compliance['periodic_ticket']);
            $allowed[$key] = $compliance;
        }
        return $allowed;
    }

    private function isAllowedPeriodicTicket($periodicTicket, $dueDate, $processingDate)
    {
        if ($periodicTicket['status'] == 'created') {
            if ($dueDate->isAfter($processingDate) || $dueDate->isSameDay($processingDate)) {
                return $periodicTicket;
            }
        }
    }
    private function getCompliancePeriodicTicketIds($periodicTickets)
    {
        $ticketIds = collect($periodicTickets)->pluck('periodic_ticket_id')->toArray();
        $wrappedTicketIds = implode(',', array_map(function ($id) {
            return "$id";
        }, $ticketIds));
        return $wrappedTicketIds;
    }
    private function processResponseData($total, $month, $options = [], $ticketColumn = 'ticket_id')
    {
        $total = array_filter($total);
        $month = array_filter($month);
        if (isset($options['add_tickets']) && ($options['add_tickets'])) {
            if ($options['filter'] == 'total') {
                return $total;
            }
            if ($options['filter'] == 'month') {
                return $month;
            }
        }
        // $totalTicketIds = array_column($total, $ticketColumn);
        // $totalTicketIds = array_unique($totalTicketIds);
        // $monthTicketIds = array_column($month, $ticketColumn);
        // $monthTicketIds = array_unique($monthTicketIds);
        $responseData = [
            'total' => count($total),
            'month' => count($month)
        ];
        return $responseData;
    }

    private function getComplianceEntryColumns()
    {
        return [
            'ticket_id',
            'regulatory_body',
            'compliance_point_no',
            'compliance_point_description',
            'compliance_owner',
            'due_date',
            'due_month',
            'frequency',
            'department_name',
            'division_name'
        ];
    }

    private function getDueDateText($frequency, $dueDate, $dueMonth)
    {
        $frequency = strtolower($frequency);
        if ($frequency == "yearly") {
            $monthName = date("F", mktime(0, 0, 0, $dueMonth, 1));
            return $this->ordinal($dueDate) . " of $monthName";
        } elseif ($frequency == "quarterly") {
            return $this->ordinal($dueDate) . " day of " . $this->ordinal($dueMonth) . " month";
        } elseif ($frequency == "monthly") {
            return $this->ordinal($dueDate) . " day of the month";
        } elseif ($frequency == "fortnightly") {
            return $this->ordinal($dueDate) . " day of fortnight";
        } elseif ($frequency == "weekly") {
            return $this->ordinal($dueDate) . " day of week";
        } elseif ($frequency == "daily") {
            return "daily";
        } else {
            return "Invalid frequency";
        }
    }

    private function ordinal($number)
    {
        try {
            $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
            if (($number % 100) >= 11 && ($number % 100) <= 13) {
                $suffix = 'th';
            } else {
                $suffix = $suffix[$number % 10];
            }
            return $number . $suffix;
        } catch (Exception $e) {
            dd($number);
        }

    }

    public function getDivisionIdByName($name)
    {
        $result = $this->model->where('division_name', $name)->latest()->first();
        if (! $result) {
            return null;
        }
        return $result->division_id;
    }
}
