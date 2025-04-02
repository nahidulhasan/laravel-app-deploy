<?php

namespace App\Services;

use App\Models\PeriodicTicket;
use App\Models\Token;
use App\Traits\RequestService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Class TicketCreationApiService
 * @package App\Services
 */
class TicketCreationApiService
{
    use RequestService;

    private $secret;
    private $url;
    private $workflow_load_form_url;
    private $formFieldMappingService;
    private $userDropdownUrl;
    private $userByGroupIds;
    private $usersGroupUrl;
    private $workflowConfig;
    private $workflow;
    private $category;
    private $cxoCeoUrl;
    private $systemConfigUrl;

    private $subCategory;
    private $externalApi;


    /**
     * @param FormFieldMappingService $formFieldMappingService
     * @throws Exception
     */
    public function __construct(FormFieldMappingService $formFieldMappingService)
    {
        $this->formFieldMappingService = $formFieldMappingService;
        $macAddress = $this->getHost();
        //        $this->secret = $this->getToken();
//        $this->secret = empty($this->secret)?$this->getToken():$this->secret;
        // $this->secret = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImV4cCI6MTY4MzA4ODcwMiwic2NvcGUiOiJiYXNpYyBlbWFpbCBjcmVkLXVzZXIiLCJjaGFubmVsX2lkIjoxfQ.BiGnev47eFbzPkZRYRomaYKVrMwRXrsGO10-25Y5teg';
        $this->url = $macAddress . '/api/v1/ticket/tickets';
        $this->workflow_load_form_url = $macAddress . '/api/v1/workflow/loadform';
        $this->userDropdownUrl = $macAddress . '/api/v1/user/group-users-dropdown';
        $this->userByGroupIds = $macAddress . '/api/v1/user/get-user-by-group-ids';
        $this->usersGroupUrl = $macAddress . '/api/v1/user/users-group-by-emails';
        //        $this->userDropdownUrl = $macAddress . '/api/v1/user/users/dropdown';
        $this->workflowConfig = $macAddress . '/api/v1/get-config/';
        $this->cxoCeoUrl = $macAddress . '/api/v1/user/cxo-ceo-details';
        $this->systemConfigUrl = $macAddress . '/api/v1/get-config';
        $this->externalApi = $macAddress . '/api/v1/get-external-api';
    }

    /**
     * @param $method
     * @param $url
     * @param $content
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processRequest($method, $url, $content)
    {
        switch (strtolower($method)) {
            case "get":
                return $this->get($url);
                break;
            case "post":
                return $this->post($url, $content);
                break;
            case "put":
                return $this->put($url, $content);
                break;
            case "delete":
                return $this->delete($url, $content);
                break;
        }
    }


    /**
     * @param $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url)
    {
        $this->secret = $this->getToken();
        return $this->request('GET', $url);
    }


    /**
     * @param $url
     * @param $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($url, $data)
    {
        $this->secret = $this->getToken();
        return $this->request('POST', $url, $data);
    }


    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createTicket($complianceEntrye)
    {
        $title = $this->getPeriodicTicketTitle($complianceEntrye);
        $msisdn = isset($complianceEntrye->msisdn) ? $complianceEntrye->msisdn : '017390513630';
        $this->getWorkflowConfig();
        $name = $title;
        $category_id = $this->category ?? 177;
        $sub_category_id = $this->subCategory ?? 179;
        $workflow_id = $this->workflow ?? 230;
        $kam_msisdn = isset($complianceEntrye->msisdn) ? $complianceEntrye->msisdn : '017390513630';
        $data = $this->postParameter($complianceEntrye, $msisdn, $name, $category_id, $sub_category_id, $workflow_id, $kam_msisdn);
        Log::info('create ticket APi call data = ' . json_encode($data));
        return $this->post($this->url, $data);
    }

    /**
     * @param $complianceEntrye
     * @return string
     */
    public function getPeriodicTicketTitle($complianceEntrye)
    {
        $countPeriodicTicket = PeriodicTicket::where('compliance_entry_id', $complianceEntrye->id)->count() + 1;
        $title = $complianceEntrye->name . '_' . $complianceEntrye->frequency . '_' . $countPeriodicTicket;
        return $title;
    }

    /**
     * @param $msisdn
     * @param $name
     * @param $category_id
     * @param null $sub_category_id
     * @param $workflow_id
     * @param $kam_msisdn
     * @param bool $is_draft
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postParameter($complianceEntrye, $msisdn, $name, $category_id, $sub_category_id = null, $workflow_id, $kam_msisdn, $is_draft = false)
    {
        if (!empty($sub_category_id)) {
            $form_category = $sub_category_id;
        } else {
            $form_category = $category_id;
        }
        $data = [
            "msisdn" => $msisdn,
            "name" => $name,
            "category_id" => $category_id,
            "sub_category_id" => $sub_category_id,
            "workflow_id" => $workflow_id,
            "is_draft" => $is_draft,
            "kam_msisdn" => $kam_msisdn,
            "workflow_transition_field_values" => $this->getFormfields($complianceEntrye, $form_category)
        ];
        return $data;
    }

    /**
     * @param $category_id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */

    public function getFormfields($complianceEntrye, $categoryId)
    {
        $response = $this->getWorkflowForm($categoryId);
//        Log::info('Get workflow form response  = ' . json_encode($response));
        $dweFormInfo = $response['data'];
        $workflowFormFields = [];
        $formFieldIDs = $this->formFieldMappingService->getAllFormFieldMappings();
        $formField = $dweFormInfo['workflowForms']['workflow_form_fields'];
//        Log::info('Get workflow form field   = ' . json_encode($formField));
        foreach ($formField as $workflowFormField) {
            $fieldId = $workflowFormField['fieldId'] ?? $workflowFormField['fieldid'];
            Log::info('fieldId======'.$fieldId);
            $workflowFormFields[] = array(
                "workflow_form_id" => $workflowFormField['workflow_form_id'],
                "workflow_form_field_id" => $workflowFormField['id'],
                "fieldid" => $fieldId,
                "fieldId" => $fieldId,
                "is_repeatable" => 0,
                "name" => $workflowFormField['name'],
                "field_name" => $workflowFormField['name'],
                "input_type" => $workflowFormField['input_type'],
                "field_value" => $this->getFieldValue($complianceEntrye, $fieldId, $formFieldIDs),
                "value" => $this->getFieldValue($complianceEntrye, $fieldId, $formFieldIDs)
            );
        }
        return $workflowFormFields;
    }

    /**
     * @param $complianceEntrye
     * @param $fieldId
     * @param $formFieldIDs
     * @return mixed
     */
    public function getFieldValue($complianceEntrye, $fieldId, $formFieldIDs)
    {
        if ($fieldId == 'Period') {
            Log::info('Period');
            return $this->ticketPeriod($complianceEntrye);
        }
        if ($fieldId == 'DueDate') {
            return $complianceEntrye->next_due_date;
        }
        if ($fieldId == 'Frequency') {
            $complianceEntrye->frequency = ucfirst($complianceEntrye->frequency);
        }
        foreach ($formFieldIDs as $formFieldID) {
            if ($fieldId == $formFieldID['form_field_id']) {
                $complianceEntryColumn = $formFieldID['compliance_entry_table_column_reference'];
                return $complianceEntrye->$complianceEntryColumn;
            }
        }
    }

    public function ticketPeriod($complianceEntrye)
    {
        $periodDate = $this->getPeriodForNewEntry($complianceEntrye);

        if (isset($complianceEntrye->is_reopen) && $complianceEntrye->is_reopen == true) {
            $complianceEntrye->is_reopen = false;
            $complianceEntrye->save();
            return $this->getPeriodText($complianceEntrye->frequency, $periodDate);
        } else {
            return $this->getPeriodText($complianceEntrye->frequency, $periodDate);
        }
    }

    /**
     * @param $complianceEntrye
     * @return string
     */
    public function getPeriodForNewEntry($complianceEntrye)
    {
        $frequency = strtolower($complianceEntrye->frequency);
        $nextDueDate = Carbon::create($complianceEntrye->next_due_date);
        if ($frequency == "yearly") {
            $subtractedCustomDate = $nextDueDate->subYear()->format('Y-m-d');
        } elseif ($frequency == "quarterly") {
            $subtractedCustomDate = $nextDueDate->subQuarterNoOverflow()->format('Y-m-d');
        } elseif ($frequency == "monthly") {
            $subtractedCustomDate = $nextDueDate->subMonthNoOverflow()->format('Y-m-d');
        } elseif ($frequency == "fortnightly") {
            $subtractedCustomDate = $nextDueDate->subDays(15)->format('Y-m-d');
        } elseif ($frequency == "daily") {
            $subtractedCustomDate = $nextDueDate->subDay()->format('Y-m-d');
        } elseif ($frequency == "weekly") {
            $subtractedCustomDate = $nextDueDate->subWeek()->format('Y-m-d');
        } else {
            return "Invalid frequency";
        }
        return $subtractedCustomDate;
    }

    /**
     * Get workflow load form
     * @param $category_id
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWorkflowForm($categoryId)
    {
        $url = $this->workflow_load_form_url . '/' . $categoryId;
        return $this->get($url);
    }


    /**
     * @param $url
     * @param $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($url, $data)
    {
        return $this->request('PUT', $url, $data);
    }


    /**
     * @param $url
     * @param $data
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($url, $data)
    {
        return $this->request('DELETE', $url, $data);
    }

    /**
     * @param $hashedTooken
     * @return mixed
     */
    public static function getUser($hashedTooken)
    {
        $token = PersonalAccessToken::findToken($hashedTooken);
        if ($token != null) {
            return $token->tokenable;
        }
        return false;
    }

    /**
     * @param $query
     * @return mixed
     * @throws Exception
     */
    public function getUserDropdown($query)
    {
        $url = $this->userDropdownUrl;
        return $this->request('GET', $url, $query);
    }

    /**
     * @param $query
     * @return mixed
     * @throws Exception
     */
    public function getUserByGroupIds($query)
    {
        $url = $this->userByGroupIds;
        return $this->request('POST', $url, $query);
    }

    public function getSystemConfig($key)
    {
        $url = $this->systemConfigUrl . '/' . $key;
        return $this->request('GET', $url, []);
    }

    public function getExternalApi($key)
    {
        $url = $this->externalApi . '/' . $key;
        return $this->request('GET', $url, []);
    }
    public function getUsersGroup(array $emails,$type=null)
    {
        $url = $this->usersGroupUrl;
        if (!empty($type)){
            $url = $this->usersGroupUrl.'/'.$type;
        }
        return $this->post($url, $emails);
    }
    public function getCxoCeoDetails(array $userIds)
    {
        $url = $this->cxoCeoUrl;
        return $this->post($url, $userIds);
    }


    public function getDummyCxoCeoDetails(array $userIds)
    {
        $ceoIds = [118, 12142, 12041];
        $cxoIds = [117, 116, 12141, 12040];
        $cxoDivisionIds = [
            117 => 1810,
            118 => 1810,
            116 => 1804,
            12040 => 1804
        ];
        $masterData = [];
        foreach ($userIds as $userId) {
            $data = [
                'is_ceo' => false,
                'is_cxo' => false,
                'division_id' => null
            ];
            if (in_array($userId, $cxoIds)) {
                $data['is_cxo'] = true;
                $data['division_id'] = $cxoDivisionIds[$userId] ?? null;
            }
            if (in_array($userId, $ceoIds)) {
                $data['is_ceo'] = true;
                $data['division_id'] = $cxoDivisionIds[$userId] ?? null;
            }
            $masterData[$userId] = $data;
        }
        return $masterData;
    }
    /**
     * @return mixed
     * @throws Exception
     */
    public function getToken()
    {
        RequestService::checkValidToken();
        $token = Token::where('today', date('Y-m-d'))->first();
        if (!empty($token)) {
            return $token->secret;
        } else {
            $response = TokenService::getAccessToken();
            Log::info('Application access token (getAccessToken) response  = ' . json_encode($response));
            if (isset($response['success'])) {
                if ($response['success']) {
                    Token::truncate();
                    Token::create([
                        'name' => 'CHT_TOKEN',
                        'key' => 'cht_token',
                        'secret' => $response['data']['access_token'],
                        'today' => date('Y-m-d')
                    ]);
                    $this->secret = $response['data']['access_token'];
                    return $response['data']['access_token'];
                } else {
                    return $response;
                }
            } else {
                return $response;
            }
        }
    }

    /**
     * Get Host from env file
     *
     * @return string
     */
    public function getHost()
    {
        return env('CHT_HOST');
    }

    /**
     * @throws Exception
     */
    public function getWorkflowConfig()
    {
        $this->secret = $this->getToken();
        $url = $this->workflowConfig . env('WORKFLOW_CONFIG_KEY', 'rcms-workflow-config');
        $response = $this->request('GET', $url);
        Log::info('rcms-ticket-create-api-service get workflow config  = ' . json_encode($response));
        if (!empty($response) && isset($response['status']) && $response['status'] == 'SUCCESS') {
            $configValue = $response['data'];
            if (count($configValue) > 0) {
                $this->workflow = $configValue['workflow_id'];
                $this->category = $configValue['category_id'];
                $this->subCategory = $configValue['sub_category'];
                Log::info('workflow config data : ' . json_encode($response['data']));
            }
            Log::info('workflow config data set successfully ');
        } else {
            Log::info('workflow config data dose not set');
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getTokenInfo($input)
    {
        $host = env('CHT_HOST');
        if (isset($input['host'])) {
            if (!empty($input['host'])) {
                $host = $input['host'];
            }
        }
        $clientId = isset($input['CLIENT_ID']) ? $input['CLIENT_ID'] : env('CLIENT_ID');
        $clientSecret = isset($input['CLIENT_SECRET']) ? $input['CLIENT_SECRET'] : env('CLIENT_SECRET');
        $username = isset($input['username']) ? $input['username'] : 'rcms@rcms.com';
        $password = isset($input['password']) ? $input['password'] : 'Habib2020@';
        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password
        ];
        $url = '/api/v1/token';

        $baseUrl = $host . $url;
        Log::info('Application access token api url  = ' . $baseUrl);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $response1 = Http::withHeaders($headers)->withOptions(["verify" => false])->post($baseUrl, $data);

        $workflowConfigUrl = $host . '/api/v1/get-config/rcms-workflow-config';
        $token = isset($input['token']) ? $input['token'] : $response1['data']['access_token'];
        $response = Http::withOptions(["verify" => false])->withToken($token)->withHeaders($headers)->get($workflowConfigUrl, null);
        dd($response1->json(), $baseUrl, $data, $response->json());
    }

    /**
     * @param $frequency
     * @param $dueDate
     * @return string
     */

    /**
     * @param $frequency
     * @param $dueDate
     * @return string
     */
    public function getPeriodText($frequency, $dueDate)
    {
        $time = strtotime($dueDate);
        $day = date('d', $time);
        $month = date('m', $time);
        $monthString = date('M', $time);
        $year = date('Y', $time);
        $frequency = strtolower($frequency);
        if ($frequency == "yearly") {
            return "Year $year";
        } elseif ($frequency == "quarterly") {
            return $this->ordinal($this->getQuarterByMonth($month)) . " quarter " . $year;
        } elseif ($frequency == "monthly") {
            return $monthString . "-$year";
        } elseif ($frequency == "fortnightly") {
            return $this->getFortnightlyDueDate($day) . $monthString . " $year";
        } elseif ($frequency == "daily") {
            return $this->ordinal($day) . ' ' . $monthString . " $year";
        } elseif ($frequency == "weekly") {
            $weekNumber = date("W", $time);
            return $this->ordinal($weekNumber) . ' week' . " $year";
        } else {
            return "Invalid frequency";
        }
    }

    /**
     * @param $day
     * @return string
     */
    private function getFortnightlyDueDate($day)
    {
        if ($day <= 15) {
            return $this->ordinal(1) . ' half ';
        } else {
            return $this->ordinal(2) . ' half ';
        }
    }

    /**
     * @param $monthNumber
     * @return float|int
     */
    public static function getQuarterByMonth($monthNumber)
    {
        return floor(($monthNumber - 1) / 3) + 1;
    }

    /**
     * @param $number
     * @return string
     */
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

}
