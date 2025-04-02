<?php

namespace App\Repositories;

use App\Models\PeriodicTicket;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Traits\SystemTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Services\TicketCreationApiService;
use App\Traits\RequestService;

/**
 * Class GroupRepository
 * @package App\Repositories
 */
class ReminderMailSettingRepository extends BaseRepository
{
    protected $modelName = Setting::class;

    protected $periodicTicket = PeriodicTicket::class;

    protected $fitterTicket = ['completed', 'cancelled'];

    protected $ticketCreationApiService;
    public $groupUsers = [];

    public function __construct(PeriodicTicket $periodicTicket, Setting $model, TicketCreationApiService $ticketCreationApiService)
    {
        $this->model = $model;
        $this->periodicTicket = $periodicTicket;
        $this->ticketCreationApiService = $ticketCreationApiService;
    }

    /**
     * @param string $dueDate
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList($dueDate = '')
    {
        $resultInfo = $this->periodicTicket->with('complianceOwner')->whereNotIn('status', $this->fitterTicket)
            ->where('due_date', $dueDate)
            ->get()->toArray();
        $data = [];
        foreach ($resultInfo as $key => $info) {
            Log::info('group id = ' . $info['compliance_owner']['compliance_group_id']);
            if (!empty($info['compliance_owner']['compliance_group_id'])) {
                $complianceGroupId = $info['compliance_owner']['compliance_group_id'];
                $complianceOwner = $info['compliance_owner']['compliance_group_id'] ?? '';
                $groupUser = $this->getGroupUsers($complianceGroupId);
//                Log::info('$groupUser ticket= '.$info['periodic_ticket_id']);
//                Log::info('group id = '.$info['compliance_owner']['compliance_group_id']);
//                Log::info('$groupUsers = '.json_encode($groupUser));

                $groupUser = !empty($groupUser) ? $groupUser : [];
                $resultInfo[$key]['compliance_owner']['compliance_group_user'] = $groupUser;

                if (!empty($complianceOwner) && !empty($groupUser)) {
                    $data = array_unique(array_merge($data, $groupUser));
                }
//                $data = array_unique(array_merge($data, $groupUser));
            } else {
                if (empty($info['compliance_owner']['compliance_group_id']) && !empty($info['compliance_owner']['compliance_owner'])) {
                    $email = $info['compliance_owner']['compliance_owner'];
                    array_push($data, $email);
                }
                $resultInfo[$key]['compliance_owner']['compliance_group_user'] = [];
            }
        }
        return $result = ['data' => $resultInfo, 'user_list' => $data];
    }


    /**
     * @param string $dueDate
     * @param $fapGroupIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    /*public function getListWithFapOld($dueDate = '', $fapGroupIds)
    {
        $fapGroupIds = is_array($fapGroupIds) ? $fapGroupIds : explode(',', $fapGroupIds);

        $resultInfo = $this->periodicTicket->with('complianceOwner')
            ->whereNotIn('status', $this->fitterTicket)
            ->where('due_date', $dueDate)
            ->get()
            ->toArray();

        $ticketIds = array_column($resultInfo, 'ticket_id');
        $ticketList = $this->getTicketListByIds($ticketIds);
        
        $uniqueUsers = [];

        foreach ($resultInfo as $key => $info) {
            $complianceGroupId = $info['compliance_owner']['compliance_group_id'] ?? null;
            $complianceOwner = $info['compliance_owner']['compliance_owner'] ?? null;
            $ticketId = $info['ticket_id'];

            $fapTicket = $this->checkFapTicket($ticketList, $ticketId, $fapGroupIds);

            $resultInfo[$key]['compliance_owner']['compliance_group_user'] = [];

            if ($complianceGroupId) {
                $groupUser = $fapTicket
                    ? $this->getFapUser($fapGroupIds) ?? []
                    : $this->getGroupUsers($complianceGroupId) ?? [];

                $resultInfo[$key]['compliance_owner']['compliance_group_user'] = $groupUser;

                // Add unique group users to $uniqueUsers
                foreach ($groupUser as $user) {
                    $uniqueUsers[$user] = true;
                }

            } elseif ($complianceOwner) {
                if ($fapTicket) {
                    $fapUsers = $this->getFapUser($fapGroupIds) ?? [];
                    $resultInfo[$key]['compliance_owner']['compliance_group_user'] = $fapUsers;

                    foreach ($fapUsers as $user) {
                        $uniqueUsers[$user] = true;
                    }
                } else {
                    $uniqueUsers[$complianceOwner] = true;
                }
            }
        }

        $data = array_keys($uniqueUsers);

        return ['data' => $resultInfo, 'user_list' => $data];
    }*/


    /**
     * @param string $dueDate
     * @param $fapGroupIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getListWithFap($dueDate = '', $fapGroupIds)
    {
        $fapGroupIds = is_array($fapGroupIds) ? $fapGroupIds : explode(',', $fapGroupIds);

        $fapUsersWithGroup = $this->getFapUserWithGroup($fapGroupIds);

        $resultInfo = $this->periodicTicket->with('complianceOwner')
            ->whereNotIn('status', $this->fitterTicket)
            ->where('due_date', $dueDate)
            ->get()
            ->toArray();

        $ticketIds = array_column($resultInfo, 'ticket_id');
        $ticketList = $this->getTicketListByIds($ticketIds);

        $fapTicketList = []; // To store only FAP tickets
        $uniqueUsers = [];

        foreach ($resultInfo as $key => $info) {
            $complianceGroupId = $info['compliance_owner']['compliance_group_id'] ?? null;
            $complianceOwner = $info['compliance_owner']['compliance_owner'] ?? null;

            $ticketId = $info['ticket_id'];
            $fapTicket = $this->checkFapTicket($ticketList, $ticketId, $fapGroupIds);

            $resultInfo[$key]['compliance_owner']['compliance_group_user'] = [];

            // Add ticket to FAP ticket list if applicable
            if ($fapTicket) {
                $info['assign_group_id'] = $fapTicket;
                $fapTicketList[] = $info;
            }

            if ($complianceGroupId) {
                $groupUser = $fapTicket
                    ? $this->getFapUser($fapGroupIds) ?? []
                    : $this->getGroupUsers($complianceGroupId) ?? [];

                $resultInfo[$key]['compliance_owner']['compliance_group_user'] = $groupUser;

                // Add unique group users to $uniqueUsers
                foreach ($groupUser as $user) {
                    $uniqueUsers[$user] = true;
                }

            } elseif ($complianceOwner) {
                if ($fapTicket) {
                    $fapUsers = $this->getFapUser($fapGroupIds) ?? [];
                    $resultInfo[$key]['compliance_owner']['compliance_group_user'] = $fapUsers;

                    foreach ($fapUsers as $user) {
                        $uniqueUsers[$user] = true;
                    }
                } else {
                    $uniqueUsers[$complianceOwner] = true;
                }
            }
        }

        $data = array_keys($uniqueUsers);

        return ['data' => $resultInfo,
                'fap_tickets' => $fapTicketList,
                'fap_users_groups'=>$fapUsersWithGroup,
                'user_list' => $data
               ];
    }

    /**
     * @param $ticketList
     * @param $ticketId
     * @param $fapGroupIds
     * @return bool
     */
    protected function checkFapTicket($ticketList, $ticketId, $fapGroupIds)
    {
        foreach ($ticketList as $ticket) {
            if ($ticket['id'] == $ticketId) {
                if (in_array((string)$ticket['assign_group_id'], $fapGroupIds)) {
                   return $ticket['assign_group_id'];
                }
            }
        }

        return false ;
    }

    /**
     * Retrieve FAP
     * @param $fapGroupIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getFapUser($fapGroupIds)
    {
        $fapUsers = [];
        $uniqueUsers = [];

        foreach ($fapGroupIds as $fapGroupId) {
            $groupUsers = $this->getGroupUsers($fapGroupId);
            foreach ($groupUsers as $user) {
                if (!isset($uniqueUsers[$user])) {
                    $fapUsers[] = $user;
                    $uniqueUsers[$user] = true;
                }
            }
        }
        return $fapUsers;
    }


    protected function getFapUserWithGroup($fapGroupIds)
    {
        $groupedUsers = [];

        foreach ($fapGroupIds as $fapGroupId) {
            $groupUsers = $this->getGroupUsers($fapGroupId);

            if (!isset($groupedUsers[$fapGroupId])) {
                $groupedUsers[$fapGroupId] = [];
            }

            foreach ($groupUsers as $user) {
                if (!in_array($user, $groupedUsers[$fapGroupId], true)) {
                    $groupedUsers[$fapGroupId][] = $user;
                }
            }
        }

        return $groupedUsers;
    }


    /**
     * @param $ticketIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTicketListByIds($ticketIds)
    {
        if (empty($ticketIds)) {
            return [];
        }

        $url = env('CHT_HOST') . '/api/v1/ticket/tickets-list-by-ids';
        $data = ['ticketIds' => $ticketIds];

        // Make API request
        $result = $this->ticketCreationApiService->post($url, $data);

        if (!empty($result) && isset($result['status_code']) && (int)$result['status_code'] === 200) {
            return $result['data'] ?? [];
        }

        return [];
    }



    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplate()
    {
        $url = env('CHT_HOST') . '/api/v1/notification/email-template';
        $result = $this->ticketCreationApiService->get($url);
        $tamplate = [];
        if (isset($result['status_code']) && $result['status_code'] && !empty($result['data'])) {
            $tamplate = $result['data']['data'];
        }
        return $tamplate;
    }

    /**
     * @param $ticketList
     * @param string $type
     * @param $emailReceiver
     * @param $templateInfo
     * @param $dayCount
     * @param null $emailReceiverCC
     * @return array
     */
    public function getEmailReceiver($ticketList, $type = 'Before', $emailReceiver, $templateInfo, $dayCount, $emailReceiverCC = null)
    {
        $emailReceiver = str_replace(' ', '', $emailReceiver);
        $emailReceiverGroups = array_map('trim', explode(',', $emailReceiver));
        $emailReceiverCCArray = array_map('trim', explode(',', str_replace(' ', '', $emailReceiverCC)));
        if (!empty($ticketList) && is_array($ticketList) && !empty($emailReceiver) && !empty($templateInfo)) {
            $email_sender = $row = [];
            $email_sender['from_name'] = $row['from_name'] = (!empty($templateInfo['channel_info']) && count($templateInfo['channel_info']) == 2) ? $templateInfo['channel_info']['from_name'] : 'Regulatory Compliance Monitoring System';
            $email_sender['from_email'] = $row['from_email'] = (!empty($templateInfo['channel_info']) && isset($templateInfo['channel_info']['from_email']) && !empty($templateInfo['channel_info']['from_email'])) ? $templateInfo['channel_info']['from_email'] : 'rcms@grameenphone.com';
            $complianceGroupIdField = 'compliance_group_id';
            $complianceGroupField = 'compliance_group';
            $ticket_list = [];
            foreach ($ticketList as $key => $value) {
                $ticketInfo = [
                    'ticket_id' => $value['ticket_id'],
                    'periodic_ticket_id' => $value['periodic_ticket_id'],
                    'regulatory_body' => '',
                    'compliance_point_no' => '',
                    'compliance_point_description' => '',
                    'compliance_owner' => '',
                    'compliance_group' => '',
                    'due_date' => $value['due_date'],
                    'compliance_point_id' => '',
                    $complianceGroupIdField => '',
                    'to_email' => [],
                    'cc_email' => [],
                    'group' => []
                ];
                if (isset($value['compliance_owner']['compliance_owner']) && !empty($value['compliance_owner']['compliance_owner'])) {
                    $ticketInfo['compliance_owner'] = $value['compliance_owner']['compliance_owner'];
                }

                if (isset($value['compliance_owner']['compliance_point_description']) && !empty($value['compliance_owner']['compliance_point_description'])) {
                    $ticketInfo['compliance_point_description'] = $value['compliance_owner']['compliance_point_description'];
                }

                if (isset($value['compliance_owner']['regulatory_body']) && !empty($value['compliance_owner']['regulatory_body'])) {
                    $ticketInfo['regulatory_body'] = $value['compliance_owner']['regulatory_body'];
                }

                if (isset($value['compliance_owner']['compliance_point_no']) && !empty($value['compliance_owner']['compliance_point_no'])) {
                    $ticketInfo['compliance_point_no'] = $value['compliance_owner']['compliance_point_no'];
                }

                if (isset($value['compliance_owner'][$complianceGroupField]) && !empty($value['compliance_owner'][$complianceGroupField])) {
                    $ticketInfo['compliance_group'] = $value['compliance_owner'][$complianceGroupField];
                }

                if (!empty($value['compliance_owner'])) {
                    $complianceOwnerEmail = $value['compliance_owner']['compliance_owner'];
//                    dd($value['compliance_owner'],$complianceGroupIdField);

                    $ticketInfo['compliance_point_id'] = $value['compliance_owner']['ticket_id'] ?? '';

                    if (!empty($value['compliance_owner'][$complianceGroupIdField])) {
                        $ticketInfo[$complianceGroupIdField] = trim($value['compliance_owner'][$complianceGroupIdField]);
                    }


                    $receiverType = '';
                    $receiverGroupId = trim($value['compliance_owner'][$complianceGroupIdField]);

                    if (!empty($value['compliance_owner'][$complianceGroupIdField])) {
                        $receiverType = 'group';
                        $ticket_list[$receiverType][$receiverGroupId][] = $ticketInfo;
                        $ticket_list[$receiverType][$receiverGroupId]['email_sender'] = $email_sender;
                    } else {
                        $receiverType = 'owner';
                        if (!empty($complianceOwnerEmail)) {
//                            dd($complianceOwnerEmail);
                            $ticket_list[$receiverType][$complianceOwnerEmail][] = $ticketInfo;
                            $ticket_list[$receiverType][$complianceOwnerEmail]['email_sender'] = $email_sender;
                        }
                    }

//                    if (!empty($receiverType) && empty($ticket_list[$receiverType][$complianceOwnerEmail]['email_sender']['to_email']) && !empty($complianceOwnerEmail)) {
//                        $ticket_list[$receiverType][$complianceOwnerEmail]['email_sender']['to_email'] = [];
//                    }
//
//                    if (empty($ticket_list[$receiverType][$receiverGroupId]['email_sender']['cc_email'])
//                        && !empty($emailReceiverCCArray) && count($emailReceiverCCArray) > 0  && !empty($receiverGroupId)) {
//                        $ticket_list[$receiverType][$receiverGroupId]['email_sender']['cc_email'] = [];
//                    }
//                    if (empty($ticket_list[$receiverType][$complianceOwnerEmail]['email_sender']['cc_email'])
//                        && !empty($emailReceiverCCArray) && count($emailReceiverCCArray) > 0  && !empty($complianceOwnerEmail)) {
//                        $ticket_list[$receiverType][$complianceOwnerEmail]['email_sender']['cc_email'] = [];
//                    }

                    if (count($emailReceiverGroups) > 0) {
                        $roleInfo = UserRole::where('compliance_owner', $complianceOwnerEmail)->first();
                        if (!empty($value['compliance_owner'][$complianceGroupIdField])) {
                            $groupSlKey = count($ticket_list[$receiverType][$receiverGroupId]);
                            if ($groupSlKey >= 2) {
                                $groupSlKey = $groupSlKey - 2;
                            }
                            array_push($ticket_list[$receiverType][$receiverGroupId][$groupSlKey]['group'], trim($receiverGroupId));
                        } else {

                            if (!empty($value['compliance_owner']['compliance_owner']) && in_array('compliance_owner', $emailReceiverGroups) && !empty($roleInfo->compliance_owner)) {
                                $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, 'compliance_owner');
                            }

                            if (!empty($value['compliance_owner']['compliance_owner']) && in_array('line_manager', $emailReceiverGroups) && !empty($roleInfo->line_manager)) {
                                $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, 'line_manager');
                            }

                            if (!empty($value['compliance_owner']['compliance_owner']) && in_array('emt', $emailReceiverGroups) && !empty($roleInfo->emt)) {
                                $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, 'emt');
                            }

                            if (!empty($value['compliance_owner']['compliance_owner']) && in_array('cxo', $emailReceiverGroups) && !empty($roleInfo->cxo)) {
                                $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, 'cxo');
                            }

                            if (!empty($value['compliance_owner']['compliance_owner']) && in_array('ceo', $emailReceiverGroups) && !empty($roleInfo->ceo)) {
                                $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, 'ceo');
                            }
                        }
//                        =========================CC=========================
//
                        if (!empty($emailReceiverCCArray)
                            && count($emailReceiverCCArray) > 0
                            && in_array('compliance_owner', $emailReceiverCCArray)
                        ) {
                            if ($receiverType == 'owner' && !empty($roleInfo->compliance_owner)) {
                                $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverCCArray, $roleInfo, 'compliance_owner', 'cc');
                            }
                        }
                        if (!empty($emailReceiverCCArray)
                            && count($emailReceiverCCArray) > 0
                            && in_array('line_manager', $emailReceiverCCArray)
                            && !empty($roleInfo->line_manager)
                        ) {
                            $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverCCArray, $roleInfo, 'line_manager', 'cc');
                        }
//
                        if (!empty($emailReceiverCCArray)
                            && count($emailReceiverCCArray) > 0
                            && in_array('emt', $emailReceiverCCArray)
                            && !empty($roleInfo->emt)
                        ) {
                            $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverCCArray, $roleInfo, 'emt', 'cc');
                        }
//
                        if (!empty($emailReceiverCCArray)
                            && count($emailReceiverCCArray) > 0
                            && in_array('cxo', $emailReceiverCCArray)
                            && !empty($roleInfo->cxo)
                        ) {
                            $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverCCArray, $roleInfo, 'cxo', 'cc');
                        }
//
                        if (!empty($emailReceiverCCArray)
                            && count($emailReceiverCCArray) > 0
                            && in_array('ceo', $emailReceiverCCArray)
                            && !empty($roleInfo->ceo)
                        ) {
                            $ticket_list = $this->ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverCCArray, $roleInfo, 'ceo', 'cc');
                        }
                    }
                }
            }
            return $ticket_list;
        } else {
            return [];
        }

        return $ticket_list;
    }

    /**
     * @param $value
     * @param $ticket_list
     * @param $receiverType
     * @param $complianceOwnerEmail
     * @param $emailReceiverGroups
     * @param $roleInfo
     * @param $search_role
     * @param string $type
     * @return mixed
     */
    private function ownerMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, $search_role, $type = 'to')
    {
        if (!empty($value['compliance_owner']['compliance_owner']) && in_array($search_role, $emailReceiverGroups) && !empty($roleInfo->$search_role)) {
            if (array_key_exists($complianceOwnerEmail, $ticket_list[$receiverType])) {
                $groupSlKey = count($ticket_list[$receiverType][$complianceOwnerEmail]);
                if ($groupSlKey >= 2) {
                    $groupSlKey = $groupSlKey - 2;
                }
                if ($type == 'to') {
                    array_push($ticket_list[$receiverType][$complianceOwnerEmail][$groupSlKey]['to_email'], trim($roleInfo->$search_role));
                }
                if ($type == 'cc') {
                    array_push($ticket_list[$receiverType][$complianceOwnerEmail][$groupSlKey]['cc_email'], trim($roleInfo->$search_role));
                }
            } elseif (!empty($ticket_list[$receiverType] && !empty($value['compliance_owner']['compliance_group_id']))) {
                $groupIdIndex = $value['compliance_owner']['compliance_group_id'];
                $groupSlKey = count($ticket_list[$receiverType][$groupIdIndex]);
                if ($groupSlKey >= 2) {
                    $groupSlKey = $groupSlKey - 2;
                }
                if ($type == 'to') {
                    array_push($ticket_list[$receiverType][$groupIdIndex][$groupSlKey]['to_email'], trim($roleInfo->$search_role));
                }
                if ($type == 'cc') {
                    array_push($ticket_list[$receiverType][$groupIdIndex][$groupSlKey]['cc_email'], trim($roleInfo->$search_role));
                }
            }
        }

        return $ticket_list;
    }

    /**
     * @param $value
     * @param $ticket_list
     * @param $receiverType
     * @param $complianceOwnerEmail
     * @param $emailReceiverGroups
     * @param $roleInfo
     * @param $search_role
     * @param string $type
     * @return mixed
     */
    private function groupMailFormat($value, $ticket_list, $receiverType, $complianceOwnerEmail, $emailReceiverGroups, $roleInfo, $search_role, $type = 'to')
    {
        if (!empty($value['compliance_owner']['compliance_owner']) && in_array($search_role, $emailReceiverGroups) && !empty($roleInfo->$search_role)) {
            $groupSlKey = count($ticket_list[$receiverType][$complianceOwnerEmail]);
            if ($groupSlKey >= 2) {
                $groupSlKey = $groupSlKey - 2;
            }
            if ($type == 'to') {
                array_push($ticket_list[$receiverType][$complianceOwnerEmail][$groupSlKey]['to_email'], trim($roleInfo->$search_role));
            }
            if ($type == 'cc') {
                array_push($ticket_list[$receiverType][$complianceOwnerEmail][$groupSlKey]['cc_email'], trim($roleInfo->$search_role));
            }
        }

        return $ticket_list;
    }

    /**
     * @param $templateInfo
     * @param $toEmail
     * @param string $type
     * @return array
     */
    public function emailDataFormat($templateInfo, $toEmail, $ticketInfo, $ccEmail = [], $smtp = '')
    {
//        dd($this->dynamicFieldReplace($templateInfo['body'], $ticketInfo));
        $data['email'] = [
            'email' => $toEmail,
            'cc_email' => $ccEmail,
            'subject' => $this->dynamicFieldReplace($templateInfo['subject'], $ticketInfo),
            'content' => $this->dynamicFieldReplace($templateInfo['body'], $ticketInfo),
            'attachments' => $templateInfo['Attachment'] ?? [],
            'order' => $templateInfo['order'],
            'status' => $templateInfo['status']
        ];
        return $data;
    }

    /**
     * @param $message
     * @param $ticketInfo
     * @return mixed
     */
    private function dynamicFieldReplace($message, $ticketInfo)
    {
        $newMessage = str_replace(
            array('{{periodic_ticket_id}}', '{{due_date}}', '{{compliance_owner}}'),
            array($ticketInfo['periodic_ticket_id'] ?? '', $ticketInfo['due_date'] ?? '', $ticketInfo['compliance_owner'] ?? ''),
            $message
        );
        return $newMessage;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getReMaxDay($type = 'Before')
    {
        return $this->model->where('type', $type)->max('day_count');
    }

    /**
     * THis method use to get day list form setting table by use type filter
     * @param string $type
     * @return mixed
     */
    public function getDayList($type = 'Before')
    {
        return $this->model->where('type', $type)
            ->where('status', 'active')
            ->orderBy('day_count', 'desc')
            ->select('id', 'day_count', 'template_id', 'email_receiver', 'email_receiver_cc')->get()->toArray();

    }

    /**
     * this method user  to make targeted date
     * @param int $day
     * @param string $type
     * @return string
     */
    public function makeDate($day = 0, $type = 'Before')
    {
        if ($type == 'Before') {
            $toDay = Carbon::now('Asia/Dhaka')->addDays($day);
        } else {
            $toDay = Carbon::now('Asia/Dhaka')->subDay($day);
        }
        return $toDay->format('Y-m-d');
    }

    /**
     * @param $endDate
     * @return int
     */
    public function dayCount($endDate)
    {
        $dueDate = Carbon::now('Asia/Dhaka')->format('Y-m-d');
        if (!empty($endDate)) {
            return Carbon::parse($endDate)->diffInDays($dueDate);
        } else {
            return 0;
        }
    }

    /**
     * @param $formattedEmailList
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveEmail($formattedEmailList)
    {
        Log::info('Email store api start calling ');
        $url = env('CHT_HOST') . '/api/v1/notification/send-transition-emails';
        $result = $this->ticketCreationApiService->post($url, $formattedEmailList);
        Log::info('store email : ' . json_encode($formattedEmailList));
//        if($result['status_code']==200){
//            Log::info('Email store api response success ');
//        }

        Log::info('Email store api end calling');
    }

    /**
     * @param $groupIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupUsers($groupIds)
    {
        $groupUsers = $this->groupUsers;
        $url = env('CHT_HOST') . '/api/v1/user/user-list-by-group-ids';
        $data['group_id'] = $groupIds;
        if (empty($groupIds)){
            return [];
        }
        if (isset($groupUsers[$groupIds]) && !empty($groupUsers[$groupIds])) {
            return $groupUsers[$groupIds];
        }
        $result = $this->ticketCreationApiService->post($url, $data);
        if (!empty($result) && isset($result['status_code']) && trim($result['status_code']) == 200) {
            if (!empty($result['data'])) {
                $emails = array_column($result['data'], 'email');
                $this->groupUsers[$groupIds] = $emails;
                return $this->groupUsers[$groupIds];
            } else {
                return [];
            }
        }
        return [];
    }


}
