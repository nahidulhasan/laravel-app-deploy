<?php


namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CeoCxoReport;
use App\Repositories\UserRoleRepository;
use App\Services\CeoCxoReportService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Repositories\ReminderMailSettingRepository;
use App\Repositories\PeriodicTicketRepository;
use App\Repositories\CxoNotificationConfigRepository;
use App\Models\UserRole;
use App\Traits\RequestService;
use App\Http\Controllers\API\V1\DashboardController;

class MailReminderService extends ApiBaseService
{
    use RequestService;

    /**
     * @var $reminderMailSettingRepository
     */
    protected $reminderMailSettings;
    /**
     * @var PeriodicTicketRepository
     */
    protected $periodicTicketRepository;
    /**
     * @var $templateList
     */
    protected $templateList;
    /**
     * @var UserRoleRepository
     */
    protected $userRoleRepository;
    /**
     * @var CxoNotificationConfigRepository
     */
    protected $cxoNotificationSetting;
    /**
     * @var bool|mixed|string|null
     */
    protected $frontend;
    /**
     * @var string
     */
    protected $useList;
    /**
     * @var string
     */
    protected $dashboardReport;
    protected $ceoCxoReportService;

    private $systemConfigUrl;

    /**
     * MailReminderService constructor.
     * @param ReminderMailSettingRepository $reminderMailSettings
     * @param PeriodicTicketRepository $periodicTicketRepository
     * @param UserRoleRepository $userRoleRepository
     * @param CxoNotificationConfigRepository $cxoNotificationSetting
     */
    public function __construct(
        ReminderMailSettingRepository $reminderMailSettings,
        PeriodicTicketRepository $periodicTicketRepository,
        UserRoleRepository $userRoleRepository,
        CxoNotificationConfigRepository $cxoNotificationSetting,
        CeoCxoReportService $ceoCxoReportService
    ) {
        $this->reminderMailSettings = $reminderMailSettings;
        $this->periodicTicketRepository = $periodicTicketRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->cxoNotificationSetting = $cxoNotificationSetting;
        $this->ceoCxoReportService = $ceoCxoReportService;
        $this->frontend = env('CHT_FRONTEND');
        $macAddress = $this->getHost();

        $this->useList = $macAddress . '/api/v1/user/user-id-by-emails';
        $this->dashboardReport = $macAddress . '/api/v1/user/user-id-by-emails';
        $this->systemConfigUrl = $macAddress . '/api/v1/get-config';
    }

    /**
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index()
    {
        $data['before'] = $this->getlist('Before');
        $data['after'] = $this->getlist('After');
        return $this->sendSuccessResponse($data, 'Reminder email store successfully. In Notification service ');
    }


    /**
     * @param $type
     * @return array|JsonResponse|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getlist($type)
    {
        try {
            $mailOrder = $this->reminderMailSettings->getDayList($type);
            $this->templateList = $template = $this->reminderMailSettings->getTemplate();
            $searchData = [];
            $searchDataFap = [];
            $fapEmailSent = false; // Flag to ensure FAP-related emails are sent only once
            $fapUsersEmail = [];
            if (count($mailOrder) != 0) {
                $roleTableData = $this->userRoleRepository->all();
                foreach ($mailOrder as $key => $value) {
                    $receiverRole = $value['email_receiver'];
                    $emailReceiverCC = $value['email_receiver_cc'] ?? [];
                    $emailReceiverCCArray = [];
                    if (!empty($emailReceiverCC)) {
                        $emailReceiverCCArray = array_map('trim', explode(',', str_replace(' ', '', $emailReceiverCC)));
                    }

                    $makeDay = $this->reminderMailSettings->makeDate($value['day_count'], $type);
                    $dayCount = $this->reminderMailSettings->dayCount($makeDay);
                    if (in_array($dayCount, array_column($mailOrder, 'day_count'))) {
                        if (in_array($value['template_id'], array_column($this->templateList, 'id'))) {
                            $indexKey = array_search($value['template_id'], array_column($this->templateList, 'id'));
                            $templateInfo = $this->templateList[$indexKey];

                            $key = 'rcms-fap-group-ids';
                            $configs = $this->getSystemConfig($key);
                            $fapGroupIds = $configs['data'] ?? null;
                            $ticketList = $this->reminderMailSettings->getListWithFap($makeDay, $fapGroupIds);

                            $fapTicketList = $ticketList['fap_tickets'];
                            $fapUsersWithGroup = $ticketList['fap_users_groups'];

                            $ticketListData = $ticketList['data'];
                            $dataCollection = collect($ticketList['data']);
                            $from_name = (!empty($templateInfo['channel_info']) && count($templateInfo['channel_info']) == 2) ? $templateInfo['channel_info']['from_name'] : 'Regulatory Compliance Monitoring System';
                            $from_email = (!empty($templateInfo['channel_info']) && isset($templateInfo['channel_info']['from_email']) && !empty($templateInfo['channel_info']['from_email'])) ? $templateInfo['channel_info']['from_email'] : 'rcms@grameenphone.com';


                            foreach ($roleTableData as $key => $info) {
                                $findEmail = $info->$receiverRole;
                                $userType = $info['role'];

                                // Skip sending emails for line manager, EMT, CXO, and CEO for `FAP` users if already sent
                                if ($userType === 'FAP') {
                                    // if ($fapEmailSent) {
                                    continue;
                                    //  }
                                    //  $fapEmailSent = true; // Mark FAP-related email as sent
                                }

                                $OwnerEmails = $this->getComplianceOwnerList($findEmail, $receiverRole);

                                if (in_array($info->compliance_owner, $ticketList['user_list']) && !empty($ticketListData)) {
                                    if (!isset($searchData[$findEmail])) {
                                        $searchData[$findEmail] = [];
                                        $searchData[$findEmail]['smtp_info']['cc_email'] = [];
                                    }
                                    $userTicket = $dataCollection->filter(function ($ticketListData) use ($OwnerEmails) {
                                        $groupUserInfo = $ticketListData['compliance_owner']['compliance_group_user'];
                                        $ticketComplianceOwnerInfo = trim($ticketListData['compliance_owner']['compliance_owner']);
                                        if (!empty($groupUserInfo)) {
                                            $checkMatch = array_intersect($groupUserInfo, $OwnerEmails);
                                            return !empty($checkMatch);
                                        } elseif (empty($groupUserInfo) && !empty($ticketComplianceOwnerInfo) && in_array($ticketComplianceOwnerInfo, $OwnerEmails)) {
                                            return true;
                                        }
                                    })->all();

                                    $mergeArrayData = array_merge($searchData[$findEmail], $userTicket);
                                    $searchData[$findEmail] = array_unique($mergeArrayData, SORT_REGULAR);
                                    $ccMail = $this->filterCCEmails($searchData[$findEmail], $emailReceiverCCArray);
                                    $searchData[$findEmail]['smtp_info'] = [
                                        'from_name' => $from_name,
                                        'from_email' => $from_email,
                                        'email_type' => $type
                                    ];
                                    if (!empty($ccMail)) {
                                        $searchData[$findEmail]['smtp_info']['cc_email'] = array_unique($ccMail, SORT_REGULAR);
                                    }
                                    $searchData[$findEmail]['smtp_info']['to_email'] = $findEmail;
                                }
                            }   // end  1st loop

                            $managerEmail = null;
                            foreach ($fapUsersWithGroup as $group => $fapUsersEmail) {
                                $userTicket = collect($fapTicketList)->filter(function ($ticket) use ($group) {
                                    return $ticket['assign_group_id'] == $group;
                                })->all();

                                $periodicTicketIds = array_column($userTicket, 'periodic_ticket_id');

                                $this->setActivityLog("Fap Tickets with group Id - " . $group, "Periodic Ticket ID", $periodicTicketIds);
                                $this->setActivityLog("Fap Users with group Id - " . $group, "FAP User List", $fapUsersEmail);
                                $totalCCEmails = [];
                                $receiverEmails = [];

                                if (!empty($fapUsersEmail) && $receiverRole != "compliance_owner") {
                                    foreach ($fapUsersEmail as $key => $fapEmail) {
                                        $roleUserData = $this->userRoleRepository->findByProperties(['compliance_owner' => $fapEmail])->first();
                                        $managerEmail = $roleUserData->$receiverRole;
                                        $receiverEmails[] = $managerEmail;
                                    }

                                    $fapUsersEmail = array_unique($receiverEmails);
                                }

                                $this->setActivityLog("Fap Manager Email group Id - " . $group, "Email Id", $receiverEmails);


                                foreach ($fapUsersEmail as $findEmail) {
                                    if (empty($ticketListData)) {
                                        continue;
                                    }

                                    if (!isset($searchDataFap[$findEmail])) {
                                        $searchDataFap[$findEmail] = [
                                            'smtp_info' => [
                                                'cc_email' => []
                                            ]
                                        ];
                                    }


                                    $mergeArrayData = array_merge($searchDataFap[$findEmail], $userTicket);
                                    $searchDataFap[$findEmail] = array_unique($mergeArrayData, SORT_REGULAR);

                                    // $ccMail = $this->filterCCEmails($searchDataFap[$findEmail], $emailReceiverCCArray);
                                    $ccMail = $this->filterCCEmailsForFAP($searchDataFap[$findEmail], $emailReceiverCCArray, $fapUsersEmail);
                                    $this->setActivityLog("Fap Email Receiver with group Id - " . $group, "Email Receiver", $emailReceiverCCArray);
                                    $this->setActivityLog("Fap CC Email with group Id - " . $group, "CC Email List", $ccMail);
                                    Log::info('CC email ' . json_encode($ccMail) . ' for ' . $findEmail);

                                    if (!empty($ccMail)) {
                                        foreach ($ccMail as $key => $email) {
                                            if (!in_array($email, $totalCCEmails, true)) {
                                                $totalCCEmails[] = $email;
                                            } else {
                                                // Email already exists
                                                unset($ccMail[$key]);
                                            }
                                        }
                                    }

                                    Log::info('Total CC Email ' . json_encode($totalCCEmails));
                                    $searchDataFap[$findEmail]['smtp_info'] = [
                                        'from_name' => $from_name,
                                        'from_email' => $from_email,
                                        'email_type' => $type
                                    ];
                                    if (!empty($ccMail)) {
                                        $searchDataFap[$findEmail]['smtp_info']['cc_email'] = array_unique($ccMail, SORT_REGULAR);
                                    }

                                   /* if($receiverRole != "compliance_owner"){
                                        $roleUserData = $this->userRoleRepository->findByProperties(['compliance_owner' => $findEmail])->first();
                                        $managerEmail = $roleUserData->$receiverRole;
                                        $searchDataFap[$findEmail]['smtp_info']['to_email'] = $managerEmail;
                                    } else {
                                        $searchDataFap[$findEmail]['smtp_info']['to_email'] = $findEmail;
                                    }*/

                                    $searchDataFap[$findEmail]['smtp_info']['to_email'] = $findEmail;

                                }
                            }
                        }
                    }
                }

                //$searchData = array_merge($searchData, $searchDataFap);

                if (!empty($searchData)) {
                    foreach ($searchData as $key => $value) {
                        if (!empty($value)) {
                            $result = $this->remainderEmailBodyTableContent($templateInfo, $value);
                            $this->saveEmail($result);
                        }
                    }
                }

                if (!empty($searchDataFap)) {
                    foreach ($searchDataFap as $key => $value) {
                        if (!empty($value)) {
                            $result = $this->remainderEmailBodyTableContent($templateInfo, $value);
                            $this->saveEmail($result);
                        }
                    }
                }

                return $searchData;
            } else {
                return 'Please add template setting. Right now it`s near by zero';
            }
        } catch (Exception $exception) {
            Log::info('Catch log =' . json_encode($exception));
            Log::info('Catch log 2=' . $exception->getMessage());
            Log::info('Trace: ' . $exception->getTraceAsString());
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * @param $title
     * @param $data
     */
    public function setActivityLog($title, $message, $data)
    {
        $logData = [
            'title' => $title,
            'type' => $title,
            'message' => $message,
            'payload' => json_encode($data),
            'response' => json_encode($data)
        ];
        ActivityLog::create($logData);
    }

    /**
     * @param $type
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    /*public function getlist($type)
    {
        try{
        $mailOrder = $this->reminderMailSettings->getDayList($type);
        $this->templateList = $template = $this->reminderMailSettings->getTemplate();
        $searchData = [];
        if (count($mailOrder) != 0) {
            $roleTableData = $this->userRoleRepository->all();
            foreach ($mailOrder as $key => $value) {
                $receiverRole = $value['email_receiver'];
                $emailReceiverCC = $value['email_receiver_cc'] ?? [];
                $emailReceiverCCArray = [];
                if (!empty($emailReceiverCC)) {
                    $emailReceiverCCArray = array_map('trim', explode(',', str_replace(' ', '', $emailReceiverCC)));
                }

                $makeDay = $this->reminderMailSettings->makeDate($value['day_count'], $type);
                $dayCount = $this->reminderMailSettings->dayCount($makeDay);
                if (in_array($dayCount, array_column($mailOrder, 'day_count'))) {
                    if (in_array($value['template_id'], array_column($this->templateList, 'id'))) {
                        $indexKey = array_search($value['template_id'], array_column($this->templateList, 'id'));
                        $templateInfo = $this->templateList[$indexKey];

                        $ticketList = $this->reminderMailSettings->getList($makeDay);

                        $ticketListdata = $ticketList['data'];
                        $dataCollection = collect($ticketList['data']);
                        $from_name = (!empty($templateInfo['channel_info']) && count($templateInfo['channel_info']) == 2) ? $templateInfo['channel_info']['from_name'] : 'Regulatory Compliance Monitoring System';
                        $from_email = (!empty($templateInfo['channel_info']) && isset($templateInfo['channel_info']['from_email']) && !empty($templateInfo['channel_info']['from_email'])) ? $templateInfo['channel_info']['from_email'] : 'rcms@grameenphone.com';

                        foreach ($roleTableData as $key => $info) {
                            try{
                                $findEmail = $info->$receiverRole;
                                $OwnerEmails = $this->getComplianceOwnerList($findEmail, $receiverRole);
                            } catch (Exception $exception){
                               // Log::info('Compliance Owner list  = '.json_encode($OwnerEmails). PHP_EOL);
                            }
                            Log::info('Compliance Owner list  search = '.$findEmail.' = receive role'.json_encode($receiverRole). PHP_EOL);
                            Log::info('Compliance Owner list  = '.json_encode($OwnerEmails). PHP_EOL);
                            if (in_array($info->compliance_owner, $ticketList['user_list']) && !empty($ticketListdata)) {
                                if (!isset($searchData[$findEmail])) {
                                    $searchData[$findEmail] = [];
                                    $searchData[$findEmail]['smtp_info']['cc_email'] = [];
                                }
                                $userTicket = $dataCollection->filter(function ($ticketListdata) use ($OwnerEmails) {
                                    $groupUserInfo = $ticketListdata['compliance_owner']['compliance_group_user'];
                                    $ticketComplianceOwnerInfo = trim($ticketListdata['compliance_owner']['compliance_owner']);
                                    if (!empty($groupUserInfo)) {
                                        $checkMatch = array_intersect($groupUserInfo, $OwnerEmails);
                                        if (!empty($checkMatch)) {
                                            // at least one of $OwnerEmails is in $groupUserInfo
                                            Log::info('group ticket checking condition in collection filter =  true'. PHP_EOL);
                                            return true;
                                        }

                                    } elseif (empty($groupUserInfo) && !empty($ticketComplianceOwnerInfo) && in_array($ticketComplianceOwnerInfo, $OwnerEmails)) {
                                        Log::info('compliance owner ticket checking condition in collection filter = true'. PHP_EOL);
                                        return true;
                                    }

                                })->all();

                                Log::info('ticket list after collection filter  = '.json_encode($userTicket). PHP_EOL);
                                $mergeArrayData = array_merge($searchData[$findEmail], $userTicket);
                                Log::info('ticket list after merge = '.json_encode($mergeArrayData). PHP_EOL);
                                $searchData[$findEmail] = $mergeArrayData;
                                $searchData[$findEmail] = array_unique($searchData[$findEmail], SORT_REGULAR);
                                $ccMail = $this->filterCCEmails($searchData[$findEmail],$emailReceiverCCArray);
                                Log::info('Email = '.$findEmail.' CC email list = '.json_encode($ccMail). PHP_EOL);
                                $searchData[$findEmail]['smtp_info'] = ['from_name' => $from_name, 'from_email' => $from_email,'email_type'=>$type];
                                if (!empty($ccMail)) {
                                    $searchData[$findEmail]['smtp_info']['cc_email'] = array_unique($ccMail, SORT_REGULAR);
                                }
                                $searchData[$findEmail]['smtp_info']['to_email'] = $findEmail;
                            }
                        }
                    }
                }
            }
            if(!empty($searchData)) {
                foreach ($searchData as $key => $value) {
                    if(!empty($value)) {
                        $result = $this->remainderEmailBodyTableContent($templateInfo, $value);

                        $this->saveEmail($result);
                    }
                }
            }
            return $searchData;
        } else {
            return 'Please add template setting. right now it`s near by zero';
        }

        } catch (Exception $exception) {
            Log::info('catch log ='.json_encode($exception));
            Log::info('catch log 2='.$exception->getMessage());
            return $this->sendErrorResponse($exception->getMessage());
        }
    }*/

    /**
     * @param $emailReceiverCCArray
     * @param $receiverRole
     * @param $findEmail
     * @param $OwnerEmails
     * @return array
     */
    public function filterCCEmails($ticketList, $emailReceiverCCArray)
    {
        unset($ticketList['smtp_info']);
        $result = [];
        $complianceOwnerList = [];
        if (!empty($ticketList) && !empty($emailReceiverCCArray)) {
            foreach ($ticketList as $index => $value) {
                if (!empty($value['compliance_owner']['compliance_owner'])) {
                    array_push($complianceOwnerList, $value['compliance_owner']['compliance_owner']);
                }
                if (!empty($value['compliance_owner']['compliance_group_user'])) {
                    $complianceOwnerList = array_merge($value['compliance_owner']['compliance_group_user'], $complianceOwnerList);
                }
            }

            if (!empty($complianceOwnerList)) {
                Log::info('filterCCEmails =  CC email list = ' . json_encode($complianceOwnerList) . PHP_EOL);
                $result = $this->getCCMailWithChecking($complianceOwnerList, $emailReceiverCCArray);
            }
            return $result;

        }

    }



    /**
     * @param $complianceOwnerList
     * @param $emailReceiverCCArray
     * @return array
     */
    private function getCCMailWithChecking($complianceOwnerList, $emailReceiverCCArray)
    {
        $ccList = [];
        if (!empty($complianceOwnerList) && !empty($emailReceiverCCArray)) {
            foreach ($complianceOwnerList as $key => $value) {
                $roleUserData = $this->userRoleRepository->findByProperties(['compliance_owner' => $value])->first();
                if (!empty($roleUserData->id)) {
                    foreach ($emailReceiverCCArray as $key => $roleInfo) {
                        if (!empty($roleUserData->$roleInfo)):
                            array_push($ccList, $roleUserData->$roleInfo);
                        endif;
                    }
                }
            }

            return $ccList;
        } else {
            return $ccList;
        }
    }



    /**
     * @param $emailReceiverCCArray
     * @param $receiverRole
     * @param $findEmail
     * @param $OwnerEmails
     * @return array
     */
    public function filterCCEmailsForFAP($ticketList, $emailReceiverCCArray, $fapUsersEmail)
    {
        unset($ticketList['smtp_info']);
        $result = [];
        $complianceOwnerList = $fapUsersEmail;
        if (!empty($ticketList) && !empty($emailReceiverCCArray)) {
            /* foreach ($ticketList as $index => $value) {
                 if (!empty($value['compliance_owner']['compliance_owner'])) {
                     array_push($complianceOwnerList, $value['compliance_owner']['compliance_owner']);
                 }
                 if (!empty($value['compliance_owner']['compliance_group_user'])) {
                     $complianceOwnerList = array_merge($value['compliance_owner']['compliance_group_user'], $complianceOwnerList);
                 }
             }*/

            if (!empty($complianceOwnerList)) {
                Log::info('filterCCEmails =  CC email list = ' . json_encode($complianceOwnerList) . PHP_EOL);
                $result = $this->getCCMailWithCheckingForFAP($fapUsersEmail, $emailReceiverCCArray);
            }
            return $result;

        }

    }


    /**
     * @param $complianceOwnerList
     * @param $emailReceiverCCArray
     * @return array
     */
    private function getCCMailWithCheckingForFAP($complianceOwnerList, $emailReceiverCCArray)
    {
        $ccList = [];
        if (!empty($complianceOwnerList) && !empty($emailReceiverCCArray)) {
            foreach ($complianceOwnerList as $key => $value) {
                $roleUserData = $this->userRoleRepository->findByProperties(['compliance_owner' => $value, 'role' => 'FAP'])->first();
                if (!empty($roleUserData->id)) {
                    foreach ($emailReceiverCCArray as $key => $roleInfo) {
                        if (!empty($roleUserData->$roleInfo)):
                            array_push($ccList, $roleUserData->$roleInfo);
                        endif;
                    }
                }
            }
            return $ccList;
        } else {
            return $ccList;
        }
    }

    /**
     * @param $emailReceiverCCArray
     * @param $receiverRole
     * @param $findEmail
     * @param $OwnerEmails
     * @return array
     */
    public function filterCCEmails2($emailReceiverCCArray, $receiverRole, $findEmail, $OwnerEmails)
    {
        if (!empty($emailReceiverCCArray)) {
            $where = [$receiverRole => $findEmail];
            $whereIn = $OwnerEmails;
            $roleTableData = $this->userRoleRepository->getUsers($where, $whereIn);
            $receiver = [];
            foreach ($emailReceiverCCArray as $key => $info) {
                $ccUniqUser = array_unique(array_column($roleTableData->toArray(), $info));
                if (!empty($ccUniqUser)) {
                    $receiver = array_merge($receiver, $ccUniqUser);
                }
            }
            return $receiver;
        } else {
            return [];
        }

    }

    /**
     * @param $email
     * @param $role
     * @return array
     */
    public function getComplianceOwnerList($email, $role)
    {
        $user = $roleTableData = $this->userRoleRepository->findBy([$role => $email]);
        if (!empty($user)) {
            return $user->pluck('compliance_owner')->toArray();

        } else {
            return [];
        }

    }


    /**
     * @param $email
     * @param $role
     * @return array
     */
    public function getFAPList($email, $role)
    {
        $user = $roleTableData = $this->userRoleRepository->findBy([$role => $email, "role" => "FAP"]);
        if (!empty($user)) {
            return $user->pluck('compliance_owner')->toArray();

        } else {
            return [];
        }

    }


    /**
     * @param $templateInfo
     * @param $emailReceiver
     * @param $emailReceiverCC
     * @param $key
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function remainderEmailBodyTableContent($templateInfo, $data)
    {
        $row = [];
        $row['from_name'] = '';
        $row['from_email'] = '';
        $row['email_type'] = '';
        $row['ceo_cxo_report_id'] = '';
        if (!empty($data)) {
            $globalSender = [];
            $globalSender = $data['smtp_info'];
            if (
                !empty($globalSender['from_email'])
                && empty($row['from_email'])
                && isset($globalSender['from_email'])
                && !empty($globalSender['from_email'])
            ) {
                $row['from_email'] = $globalSender['from_email'];
            }

            if (
                !empty($globalSender['from_name']) && empty($row['from_name'])
                && isset($globalSender['from_name'])
                && !empty($globalSender['from_name'])
            ) {
                $row['from_name'] = $globalSender['from_name'];
            }

            if (
                isset($globalSender['email_type']) && !empty($globalSender['email_type'])
            ) {
                $row['email_type'] = $globalSender['email_type'];
            }
            $smtpInfo = $data['smtp_info'];
            unset($data['smtp_info']);
            if (count($data) >= 1) {
                $table = '<br><table  border="1px" style="min-width: 400px; border-collapse: collapse; border-top: 1px #000 solid !important;">';
                $table .= '<tr style="border: 1px #000 solid !important;"><th style="padding: 10px; border: 1px #000 solid !important;">Periodic ticket ID</th>';
                $table .= '<th style="padding: 10px; border: 1px solid #000 !important;">Regulatory body</th>';
                $table .= '<th style="padding: 10px; border: 1px solid #000 !important;">Compliance point number</th>';
                $table .= '<th style="padding: 10px; border: 1px solid #000 !important;">Compliance owner</th>';
                $table .= '<th style="padding: 10px; border: 1px solid #000 !important;">Due date</th>';
                $due_date = '';
                foreach ($data as $key => $value) {
                    $ticketId = $value['ticket_id'];
                    if (empty($ticketId)) {
                        $ticketId = $value['periodic_ticket_id'];
                    }
                    if (empty($due_date) && !empty($value['due_date'])) {
                        $due_date = $value['due_date'];
                    }
                    $regulatory_body = $value['compliance_owner']['regulatory_body'];
                    $compliance_point_no = $value['compliance_owner']['compliance_point_no'];
                    $periodicTicketId = '<a href="' . $this->frontend . '/tickets/view/' . $ticketId . '">' . $value['periodic_ticket_id'] . '</a>';
                    $compliancePointId = '<a href="' . $this->frontend . '/tickets/view/' . $ticketId . '">' . $compliance_point_no . '</a>';
                    $table .= '<tr><td style="padding: 10px; border: 1px solid #000 !important;">' . $periodicTicketId . '</td>';
                    $table .= '<td style="padding: 10px; border: 1px solid #000 !important;">' . $regulatory_body . '</td>';
                    $table .= '<td style="padding: 10px; border: 1px solid #000 !important;">' . $compliance_point_no . '</td>';

                    if (!empty($value['compliance_owner']['compliance_group'])) {
                        $table .= '<td style="padding: 10px; border: 1px solid #000 !important;">' . $value['compliance_owner']['compliance_group'] . '</td>';
                    } else {
                        $table .= '<td style="padding: 10px; border: 1px solid #000 !important;">' . $value['compliance_owner']['compliance_owner'] . '</td>';
                    }

                    $table .= '<th style="padding: 10px; border: 1px solid #000 !important;">' . $value['due_date'] . '</th>';
                    $regulatory_body = $value['compliance_owner']['regulatory_body'];
                }

                $table .= '</table>';
                $tableContent = "<br>" . $table . "<br>";
                $newMessage = str_replace(
                    array('{{tableContent}}', '{{ticket_id_with_anchor}}'),
                    array($tableContent ?? '', ''),
                    $templateInfo['body']
                );
                $ccEmail = isset($smtpInfo['cc_email']) ? $smtpInfo['cc_email'] : [];
                $templateInfo['body'] = $newMessage;
                $toEmail = (isset($globalSender['to_email']) && !empty($globalSender['to_email'])) ? $globalSender['to_email'] : [];
                //                $ccEmail =[];
//                if(isset($globalSender['cc_email']) && !empty($globalSender['cc_email'])){
//                    $ccEmail = $globalSender['cc_email'];
//                }
                $ticketInfo = ['due_date' => $due_date];
                $result = $this->reminderMailSettings->emailDataFormat($templateInfo, $toEmail, $ticketInfo, $ccEmail, $smtpInfo);
                if (!empty($result)) {
                    $result['email']['cc_email'] = (isset($globalSender['cc_email']) && !empty($globalSender['cc_email'])) ? $globalSender['cc_email'] : [];
                    $row[] = $result;
                }
            }
        }
        if (!empty($row)) {
            $emailStore = $this->storeEmail($row);

            /*if($emailStore->status_code == 200 && !empty($emailStore->data->id)){
                $row['ceo_cxo_report_id'] = $emailStore->data->id;
            }*/

            if ($emailStore instanceof JsonResponse) {
                $emailStoreData = $emailStore->getData(true);
            } else {
                $emailStoreData = $emailStore;
            }

            if (
                ($emailStoreData->status_code ?? $emailStoreData['status_code']) == 200 &&
                !empty($emailStoreData->data->id ?? $emailStoreData['data']['id'])
            ) {
                $row['ceo_cxo_report_id'] = $emailStoreData->data->id ?? $emailStoreData['data']['id'];
            }
        }
        return $row;
    }

    /**
     * @param $data
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function storeEmail($data)
    {
        if (!empty($data) && isset($data[0]) && !empty($data[0]['email'])) {
            $type = '';
            $inputData = $data[0]['email'];
            $toEmail = $inputData['email'] ?? '';
            $ccEmail = $inputData['cc_email'] ?? '';
            $sendData['receiver'] = json_encode(['to_email' => $toEmail, 'cc_email' => $ccEmail]);
            $sendData['subject'] = $inputData['subject'] ?? '';
            $sendData['email_body'] = $inputData['content'] ? json_encode($inputData['content']) : [];
            if ($data['email_type'] == 'After') {
                $type = "Escalation";
            }
            if ($data['email_type'] == 'Before') {
                $type = "Reminder";
            }
            $sendData['report_type'] = $type;

            if (empty($type) and !empty($data['email_type'])) {
                $sendData['report_type'] = $data['email_type'];
            }

            $result = $this->ceoCxoReportService->create($sendData);
            if ($result->status() == 200) {
                return $result->getData();
            } else {
                return $this->sendErrorResponse([], 'Something went wrong.');
            }
        } else {
            return $this->sendErrorResponse([], 'Data format is Wrong.');
        }

    }


    /**
     * @param $ownerEmail
     * @param $CCRole
     * @return string
     */
    private function getEmailReceiverCCByRole($ownerEmail, $CCRole, $type = 'compliance_owner')
    {
        if (!empty($CCRole)) {
            $roleInfo = UserRole::where($type, $ownerEmail)->first();
            $CCRole = explode(',', $CCRole);
            if (!empty($roleInfo)) {
                if (count($CCRole) == 1) {
                    $role = trim($CCRole[0]);
                    return $roleInfo->$role;
                } else {
                    $result = [];
                    foreach ($CCRole as $key => $value) {
                        $role = trim($CCRole[$key]);
                        array_push($result, $roleInfo->$role);
                    }
                    return $result;
                }

            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * @param $formattedEmailList
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveEmail($formattedEmailList)
    {
        if (!empty($formattedEmailList)) {
            $this->reminderMailSettings->saveEmail($formattedEmailList);
        }
    }

    /**
     * @param string $role
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function emtEmailReminder($role = 'emt')
    {
        $emailRecipient = $this->emailRecipient($role);
        $emailSetting = $this->getNofificationSetting($role);
        $mailContent = '';
        $subject = '';
        $emailReceiverCC = '';
        if (!empty($emailSetting) && !empty($emailSetting->email_body)) {
            $mailContent = $emailSetting->email_body;
        }
        if (!empty($emailSetting) && !empty($emailSetting->email_subject)) {
            $subject = $emailSetting->email_subject;
        }

        if (!empty($emailSetting) && !empty($emailSetting->email_receiver_cc)) {
            $emailReceiverCC = $emailSetting->email_receiver_cc;
        }

        foreach ($emailRecipient as $key => $info) {

            $data['cc_email'] = $this->getEmailReceiverCCByRole($info, $emailReceiverCC, 'emt');
            $data['toEmail'] = $info;
            $data['massage'] = $mailContent;
            $data['subject'] = $subject;
            $ComplianceOwner = $this->getComplianceOwner($role, $info)->toArray();
            if (!empty($ComplianceOwner)) {
                Carbon::setWeekStartsAt(Carbon::SUNDAY);
                $data['startDate'] = Carbon::now()->startOfWeek()->format('Y-m-d');
                $data['endDate'] = Carbon::now()->endOfWeek()->subDays(1)->format('Y-m-d');

                /*
                 * All individual tickets having due date in this week (Sunday to Saturday)
                 */
                $data['periodicCompliance'] = $this->periodicTicketRepository->emtTotalPeriodicComplianceTicket($data['startDate'], $data['endDate'], $ComplianceOwner);
                $this->emtEmail($data);

            }
        }
        return $this->sendSuccessResponse([], $role . ' Reminder email store successfully. In Notification service ');
    }

    /**
     * @param $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function emtEmail($data)
    {
        $message = $data['massage'] ?? '';
        $subject = $data['subject'] ?? '';
        $periodicCompliance = $data['periodicCompliance'];
        if (!empty($periodicCompliance) && count($periodicCompliance) > 0) {
            $table = '<br><table  border="1px" style="min-width: 400px; border-collapse: collapse; border-color: #000000; ">';
            $table .= '<tr><th style="padding: 10px">Ticket number</th><th style="padding: 10px">Compliance Point Description</th><th style="padding: 10px">Compliance Owner</th>';
            $table .= '<th style="padding: 10px">Due date</th></tr>';
            foreach ($periodicCompliance as $key => $value) {
                $table .= '<tr><td style="padding: 10px">' . $value->periodic_ticket_id . '</td>';
                $table .= '<td style="padding: 10px">' . $value->complianceOwner->compliance_point_description . '</td>';
                $table .= '<td style="padding: 10px">' . $value->complianceOwner->compliance_owner . '</td>';
                $table .= '<th style="padding: 10px">' . $value->due_date . '</th></tr>';
            }

            $table .= '</table>';
            $tableContent = "<br>" . $table . "<br>";
            $startDate = $data['startDate'];
            $monthYear = $date = Carbon::createFromFormat('Y-m-d', $startDate)->format('F Y');
            $year = Carbon::createFromFormat('Y-m-d', $startDate)->format('Y');
            $content = $this->replaceDynamicVariable($message, $monthYear, $year, $tableContent);
            $row['from_name'] = 'Regulatory Compliance Monitoring System';
            $row['from_email'] = 'rcms@grameenphone.com';
            $row[]['email'] = [
                'email' => $data['toEmail'],
                'cc_email' => $data['cc_email'],
                'subject' => $subject,
                'content' => $content,
                'attachments' => [],
            ];
            $this->reminderMailSettings->saveEmail($row);
        }
    }

    /**
     * @param $role
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function getNofificationSetting($role)
    {
        return $this->cxoNotificationSetting->findOneBy(['email_receiver' => $role]);
    }

    /**
     * @param string $role
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cxoCeoEmailReminder($role = 'cxo')
    {
        $uniqueUserList = $this->getUserListByRole($role);
        $emailSetting = $this->getNofificationSetting($role);
        $mailContent = '';
        $subject = '';
        $emailReceiverCC = '';
        $validEmail = [];
        $errorEmail = [];

        if (!empty($emailSetting) && !empty($emailSetting->email_body)) {
            $mailContent = $emailSetting->email_body;
        }

        if (!empty($emailSetting) && !empty($emailSetting->email_subject)) {
            $subject = $emailSetting->email_subject;
        }

        if (!empty($emailSetting) && !empty($emailSetting->email_receiver_cc)) {
            $emailReceiverCC = $emailSetting->email_receiver_cc;
        }

        if (!empty($uniqueUserList)) {
            $userId = array_keys($uniqueUserList);
            $dashBoardInfo = $this->getDashboardReport($userId);
            if (!empty($dashBoardInfo->data) && !empty($uniqueUserList)) {
                $dashboardData = json_decode(json_encode($dashBoardInfo->data), True);
                foreach ($uniqueUserList as $key => $info) {
                    if (array_key_exists($key, $dashboardData)) {
                        if (!empty($dashboardData[$key]['total']['all'])) {
                            $data['startDate'] = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
                            $data['endDate'] = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
                            $data['cc_email'] = $this->getEmailReceiverCCByRole($info, $emailReceiverCC, $role);
                            $data['toEmail'] = $info;
                            $data['massage'] = $mailContent;
                            $data['subject'] = $subject;
                            $data['for_the_month']['compliance'] = $dashboardData[$key]['total']['all']['assured']['month'];
                            $data['for_the_month']['unassured'] = $dashboardData[$key]['total']['all']['unassured']['month'];
                            $data['for_the_month']['conscious_non_compliance'] = $dashboardData[$key]['total']['all']['conscious_non_compliance']['month'];
                            $data['total']['compliance'] = $dashboardData[$key]['total']['all']['assured']['total'];
                            $data['total']['unassured'] = $dashboardData[$key]['total']['all']['unassured']['total'];
                            $data['total']['conscious_non_compliance'] = $dashboardData[$key]['total']['all']['conscious_non_compliance']['total'];
                            $data['total']['new'] = $dashboardData[$key]['total']['all']['new']['total'];
                            $this->cxoCeoEmail($data, $role);
                            array_push($validEmail, $info);
                        }
                    } else {
                        array_push($errorEmail, $info);
                    }

                }

            } else {
                //ToDo:: have to implement if not fund logic
            }
        } else {
            /*  What does 424 Failed Dependency mean?
             *  The 424 Failed Dependency status code means that the request failed due to the failure of a previous request.
             */

            return $this->sendErrorResponse('CXO user mapping api response empty ', [], 424);
        }

        return $this->sendSuccessResponse(['valid' => $validEmail, 'error' => $errorEmail], $role . ' Reminder email store successfully. In Notification service ');
    }

    /**
     * @param $role
     * @return mixed
     */
    public function emailRecipient($role)
    {
        return $this->userRoleRepository->uniqueRecipientByRole($role);
    }

    /**
     * @param $roleField
     * @param $email
     * @return UserRoleRepository|\Illuminate\Support\Collection|null
     */
    public function getComplianceOwner($roleField, $email)
    {
        return $this->userRoleRepository->findBy([$roleField => $email])->pluck('compliance_owner');
    }

    /**
     * @param $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function cxoCeoEmail($data, $role = '')
    {
        $message = $data['massage'] ?? '';
        $subject = $data['subject'] ?? '';
        $forTheMonthCompliance = $data['for_the_month']['compliance'] ?? '';
        $forTheMonthUnassured = $data['for_the_month']['unassured'] ?? '';
        $totalCompliance = $data['total']['compliance'] ?? '';
        $totalUnassured = $data['total']['unassured'] ?? '';
        $totalNonCompliance = $data['total']['conscious_non_compliance'] ?? '';
        $totalNewCompliance = $data['total']['new'] ?? '';
        $startDate = date('Y-m-d');
        $monthName = $date = Carbon::createFromFormat('Y-m-d', $startDate)->format('F Y');
        $table = '<h4>For the month (' . $monthName . ')</h4>';
        $table .= '<br><table style=" min-width: 400px; border-collapse: collapse; border-top: 1px #000 solid !important;">';
        $table .= '<tr style="border-top:2px solid #000;"><th style="border: 1px solid #000; padding: 10px; border-top:1px #000 solid">Assured Compliance</th><td style="min-width: 200px; border: 1px solid #000; padding: 10px;">' . $forTheMonthCompliance . '</td></tr>';
        $table .= '<tr style="color: red;"><th style="border: 1px solid #000; padding: 10px;">Unassured Compliance</th><td style="border: 1px solid #000; padding: 10px;">' . $forTheMonthUnassured . '</td></tr>';
        $table .= '</table>';
        $table .= '<br>';
        $table .= '<h4>Total</h4>';
        $table .= '<br><table border="1px" style="min-width: 400px; border-collapse: collapse; border: 1px #000 solid !important;">';
        $table .= '<tr style="border-top:2px solid #000;"><th style="border: 1px solid #000; padding: 10px;">Assured Compliance</th><td style="min-width: 200px; border: 1px solid #000; padding: 10px;">' . $totalCompliance . '</td></tr>';
        $table .= '<tr style="color: red;"><th style="border: 1px solid #000; padding: 10px;">Unassured Compliance</th><td style="border: 1px #000 solid; padding: 10px;">' . $totalUnassured . '</td></tr>';
        $table .= '<tr style="color: red;  padding: 10px;"><th style="border: 1px #000 solid; padding: 15px;">Conscious Non-Compliance</th><td style="border: 1px #000 solid; padding: 10px;">' . $totalNonCompliance . '</td></tr>';
        $table .= '<tr style="color: black;  padding: 10px;"><th style="border: 1px #000 solid; padding: 15px;">New Compliance</th><td style="border: 1px #000 solid; padding: 10px;">' . $totalNewCompliance . '</td></tr>';
        $table .= '</table>';
        $tableContent = "<br>" . $table . "<br>";
        $monthYear = $monthName;
        $year = Carbon::createFromFormat('Y-m-d', $startDate)->format('Y');

        $content = $this->replaceDynamicVariable($message, $monthYear, $year, $tableContent);
        $row['from_name'] = 'Regulatory Compliance Monitoring System';
        $row['from_email'] = 'rcms@grameenphone.com';
        $row['email_type'] = strtoupper($role) . ' Report';

        $row[]['email'] = [
            'email' => $data['toEmail'],
            'cc_email' => $data['cc_email'],
            'subject' => $subject,
            'content' => $content,
            'attachments' => [],
            'report_type' => 'CXO Report'
        ];
        $emailStore = $this->storeEmail($row);
        if ($emailStore->status_code == 200 && !empty($emailStore->data->id)) {
            $row['ceo_cxo_report_id'] = $emailStore->data->id;
        }
        $this->reminderMailSettings->saveEmail($row);

    }

    /**
     * @param $message
     * @param $monthYear
     * @param $year
     * @param $tableContent
     * @return mixed
     */
    private function replaceDynamicVariable($message, $monthYear, $year, $tableContent)
    {
        $newMessage = str_replace(
            array('{{monthYear}}', '{{year}}', '{{tableContent}}'),
            array($monthYear ?? '', $year ?? '', $tableContent ?? ''),
            $message
        );
        return $newMessage;
    }

    /**
     * @return bool|mixed|string|null
     */
    public function getHost()
    {
        return env('CHT_HOST');
    }


    public function getSystemConfig($key)
    {
        $url = $this->systemConfigUrl . '/' . $key;
        return $this->request('GET', $url, []);
    }

    /**
     * This method use for take uniq user list by role(cxo,ceo) also map with user table data with user id
     * @param $role
     * @return array
     * @throws \Exception
     */
    public function getUserListByRole($role)
    {
        $uniqueUserList = $this->userRoleRepository->uniqueRecipientByRole($role)->toArray();
        $userIds = $this->request('POST', $this->useList, $uniqueUserList);
        if (isset($userIds['status_code']) && $userIds['status_code'] == 200) {
            Log::info('CXO role user id map API response success');
            if (!empty($userIds['data'])) {
                return $userIds['data'];
            } else {
                Log::info('CXO role user id map API response : response data object is empty');
                return [];
            }
        } else {
            Log::info('CXO role user id map API response fail');
        }
        return [];
    }

    /**
     * @param $userIds
     * @return array
     */
    public function getDashboardReport($userIds)
    {
        $obj = app(DashboardService::class);
        $result = $obj->getDashboardReports(['user_ids' => $userIds]);
        $data = $result->getData();
        if ($data->status_code == 200) {
            if (!empty($data->data)) {
                return $data;
            } else {
                Log::info('Dashboard Data Fetch APi give empty return data is = ' . json_encode($data));
                return [];
            }
        } else {
            Log::info('Dashboard Data Fetch APi give empty data request = ' . json_encode($userIds));
            return [];
        }
        return [];
    }


}
