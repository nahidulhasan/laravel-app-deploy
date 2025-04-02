<?php
namespace App\Services;

use App\Models\ComplianceEntry;
use App\Models\PeriodicTicket;
use App\Repositories\ComplianceEntryRepository;
use App\Services\ApiBaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardService extends ApiBaseService
{
    protected $ticketCreationApiService;

    protected $complianceEntryRepository;

    /**
     * constructor
     *
     * @param ComplianceEntryRepository $complianceEntryRepository
     * @param TicketCreationApiService $ticketCreationApiService
     */
    public function __construct(ComplianceEntryRepository $complianceEntryRepository, TicketCreationApiService $ticketCreationApiService)
    {
        $this->ticketCreationApiService = $ticketCreationApiService;
        $this->complianceEntryRepository = $complianceEntryRepository;
    }

    /**
     * Index method 
     *
     * @param mixed $userId 
     * @throws Exception 
     * @return mixed
     */
    public function index($userId)
    {
        try {
            $data = null;
            $userInfo = $this->getUserDetails([$userId]);
            if (!isset($userInfo[$userId])) {
                throw new Exception('User Information Not Found !');
            }

            $userDetails = $userInfo[$userId];

            if (!$userDetails) {
                throw new Exception('User Group Details Error From User Service');
            }
            if (!$userDetails['is_ceo'] && !$userDetails['is_cxo']) {
                throw new Exception('Given User Was Not CXO/CEO. Please Contact Administrator');
            }
            // for cxo 
            if ($userDetails['is_cxo']) {
                if (is_null($userDetails['division_id'])) {
                    throw new Exception('Division Id Not Found. Please Contact Administrator');
                }
                $data = $this->getDashboardForCxo($userDetails['division_id']);
                return $this->sendSuccessResponse($data, 'Dashboard Data Fetched Successfully');
            }
            // for ceo 
            if ($userDetails['is_ceo']) {
                $data = $this->getDashboardForCeo();
                return $this->sendSuccessResponse($data, 'Dashboard Data Fetched Successfully');
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * Verify if the user is ceo or cxo 
     *
     * @param array $userId 
     * @throws Exception  
     * @return mixed
     */
    public function verifyCxoCeoUser(array $userIds)
    {
        try {
            $data = $this->getUserDetails($userIds);
            return $this->sendSuccessResponse($data, 'User Details Fetched Successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('User Details Not Fetched');
        }
    }
    /**
     * get dashboard tickets 
     * @param array $data
     * @return mixed 
     */
    public function getDashboardTickets(array $data)
    {
        try {
            $regulatoryBody = $data['regulatory_body'] ?? null;
            $divisionName = $data['division_name'] ?? null;
            $filter = $data['filter'] ?? null;
            $options = [
                'filter' => $filter,
                'add_tickets' => true
            ];
            $divisionId = $this->getDivisionIdByName($divisionName);
            $assured = $this->getAssuredForDashboard($regulatoryBody, $divisionId, $options);
            $unassured = $this->getUnassured($regulatoryBody, $divisionId, $options);
            $nonConscious = $this->getNonConsciousCompliance($regulatoryBody, $divisionId, $options);
            $new = $this->getNewCompliance($regulatoryBody, $divisionId, $options);
            $response = ['assured' => $assured, 'unassured' => $unassured, 'non_conscious' => $nonConscious,'new'=>$new];
            return $this->sendSuccessResponse($response, 'Dashboard Data Fetched Successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getTraceAsString());
        }
    }

    private function getDivisionIdByName($divisionName)
    {
        if (is_null($divisionName)) {
            return null;
        }
        return $this->complianceEntryRepository->getDivisionIdByName($divisionName);
    }

    /**
     * dashboard-reports
     * @param array $data
     * @param $userId
     * @return mixed
     */
    public function getDashboardReports(array $data)
    {
        try {
            if (!isset($data['user_ids']) || empty($data['user_ids'])) {
                return $this->sendErrorResponse('No user_ids value found !');
            }
            $errors = [];
            $responseData = [];
            $userInfos = $this->getUserDetails($data['user_ids']);
            foreach ($data['user_ids'] as $userId) {
                try {

                    $userDetails = $userInfos[$userId] ?? [];
                    if (!$userDetails) {
                        throw new Exception('User Group Details Error From User Service');
                    }
                    if (!$userDetails['is_ceo'] && !$userDetails['is_cxo']) {
                        throw new Exception('Given User Was Not CXO/CEO. Please Contact Administrator');
                    }
                    if ($userDetails['is_cxo']) {
                        if (is_null($userDetails['division_id'])) {
                            throw new Exception('Division Id Not Found. Please Contact Administrator');
                        }
                        $data = $this->getDashboardForCxo($userDetails['division_id']);
                    }
                    if ($userDetails['is_ceo']) {
                        $data = $this->getDashboardForCeo();
                    }
                    $responseData[$userId] = $data;
                } catch (Exception $e) {
                    $errors[] = ['error' => $e->getMessage(), 'user_id' => $userId];
                }
            }
            if (!empty($errors)) {
                $responseData['errors'] = $errors;
            }
            return $this->sendSuccessResponse($responseData, 'Dashboard Data Fetched Successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * Get user details for the given user ID.
     *
     * @param mixed $userId 
     * @return mixed 
     */
    private function getUserDetails($userIds)
    {
        $response = $this->ticketCreationApiService->getCxoCeoDetails($userIds);
        // return $this->ticketCreationApiService->getDummyCxoCeoDetails($userIds);
        if (isset($response['status_code']) && $response['status_code'] == 200) {
            return $response['data'];
        }
        return $response;
    }

    /**
     * Get dashboard for CEO.
     *
     * @param $userDetails
     * @return array
     */
    private function getDashboardForCeo()
    {
        $data = [];
        $regulatoryBodies = $this->getUniqueRegulatoryBodies();
        $divisions = $this->getUniqueDivisions();
        $data['total'] = [
            'all' => [
                'assured' => $this->getAssuredForDashboard(null, null),
                'unassured' => $this->getUnassured(null, null),
                'conscious_non_compliance' => $this->getNonConsciousCompliance(null, null),
                'new' => $this->getNewCompliance(null, null),
            ],
        ];
        foreach ($divisions as $divisionId => $division) {
            $data['total']['divisions'][$division] = [
                'assured' => $this->getAssuredForDashboard(null, $divisionId),
                'unassured' => $this->getUnassured(null, $divisionId),
                'conscious_non_compliance' => $this->getNonConsciousCompliance(null, $divisionId),
                'new' => $this->getNewCompliance(null, $divisionId),
            ];
        }
        foreach ($regulatoryBodies as $body) {
            $data['regulatory_body'][$body]['all'] = [
                'assured' => $this->getAssuredForDashboard($body, null),
                'unassured' => $this->getUnassured($body, null),
                'conscious_non_compliance' => $this->getNonConsciousCompliance($body, null),
                'new' => $this->getNewCompliance($body, null),
            ];

            foreach ($divisions as $divisionId => $division) {
                $data['regulatory_body'][$body]['divisions'][$division] = [
                    'assured' => $this->getAssuredForDashboard($body, $divisionId),
                    'unassured' => $this->getUnassured($body, $divisionId),
                    'conscious_non_compliance' => $this->getNonConsciousCompliance($body, $divisionId),
                    'new' => $this->getNewCompliance($body, $divisionId),
                ];
            }
        }
        return $data;
    }

    /**
     * get dashboard for cxo 
     *
     * @param int $divisionId
     * @return array
     */
    private function getDashboardForCxo($userDivision = null)
    {
        $data = null;
        $regulatoryBodies = $this->getUniqueRegulatoryBodies();
        $divisions = $this->getUniqueDivisions();
        // Overall data
        $data['total'] = [
            'all' => [
                'assured' => $this->getAssuredForDashboard(null, $userDivision),
                'unassured' => $this->getUnassured(null, $userDivision),
                'conscious_non_compliance' => $this->getNonConsciousCompliance(null, $userDivision),
                'new' => $this->getNewCompliance(null, $userDivision),
            ],
        ];

        foreach ($divisions as $divisionId => $division) {
            if ($divisionId == $userDivision) {
                $data['total']['divisions'][$division] = [
                    'assured' => $this->getAssuredForDashboard(null, $divisionId),
                    'unassured' => $this->getUnassured(null, $divisionId),
                    'conscious_non_compliance' => $this->getNonConsciousCompliance(null, $divisionId),
                    'new' => $this->getNewCompliance(null, $divisionId),
                ];
            }
        }

        // Regulatory body data
        foreach ($regulatoryBodies as $body) {
            $data['regulatory_body'][$body]['all'] = []; // we dont need all for cxo as its the same 
            foreach ($divisions as $divisionId => $division) {
                if ($divisionId == $userDivision) {
                    $data['regulatory_body'][$body]['divisions'][$division] = [
                        'assured' => $this->getAssuredForDashboard($body, $divisionId),
                        'unassured' => $this->getUnassured($body, $divisionId),
                        'conscious_non_compliance' => $this->getNonConsciousCompliance($body, $divisionId),
                        'new' => $this->getNewCompliance($body, $divisionId),
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Retrieve unique regulatory bodies from compliance entry repository.
     *
     * @return array
     */
    private function getUniqueRegulatoryBodies()
    {
        return $this->complianceEntryRepository->getUniqueRegulatoryBodies();
    }

    /**
     * Get Unique Divisions 
     * @return array
     */
    private function getUniqueDivisions()
    {
        return $this->complianceEntryRepository->getUniqueDivisions();
    }
    /**
     * Calculate the total compliance for the dashboard.
     *
     * @param  $body 
     * @param  $division 
     * @param  $range 
     * @return array
     */
    private function getAssuredForDashboard($body = null, $division = null, $options = [])
    {
        return $this->complianceEntryRepository->getAssuredForDashboard($body, $division, $options);
    }

    /**
     * Get unassured for dashboard.
     *
     * @param $body 
     * @param $division 
     * @param $range 
     * @return array
     */
    private function getUnassured($body = null, $division = null, $options = [])
    {
        return $this->complianceEntryRepository->getUnassuredForDashboard($body, $division, $options);
    }


    /**
     * Retrieves non-conscious compliance for the dashboard.
     *
     * @param mixed $body 
     * @param mixed $division f
     * @param mixed $range 
     * @return array
     */
    private function getNonConsciousCompliance($body = null, $division = null, $options = [])
    {
        return $this->complianceEntryRepository->getNonConsciousComplianceForDashboard($body, $division, $options);
    }

    /**
     * get new compliance
     *
     * @param string $body
     * @param int $division
     * @param array $options
     * @return array
     */
    private function getNewCompliance($body = null, $division = null, $options = [])
    {
        return $this->complianceEntryRepository->getNewComplianceForDashboard($body, $division, $options);
    }
}
