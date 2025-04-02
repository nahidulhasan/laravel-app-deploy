<?php


namespace App\Services;

use App\Models\ActivityLog;
use App\Models\UserRole;
use App\Repositories\UserRoleRepository;
use Doctrine\DBAL\Schema\AbstractAsset;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserRoleService extends ApiBaseService
{
    protected $repository;
    protected $complianceEntryService;
    protected $periodicTicketService;

    protected $ticketCreationApiService;

    public function __construct(TicketCreationApiService $ticketCreationApiService, UserRoleRepository $userRoleRepository, ComplianceEntryService $complianceEntryService, PeriodicTicketService $periodicTicketService)
    {
        $this->repository = $userRoleRepository;
        $this->complianceEntryService = $complianceEntryService;
        $this->periodicTicketService = $periodicTicketService;
        $this->ticketCreationApiService = $ticketCreationApiService;
    }

    public function getUserRole(array $data)
    {
        $executorEmail = $data['email'];
        try {
            $data = $this->repository->findBy(['compliance_owner' => $executorEmail])->first();
            $output = [
                'line_manager' => $data->line_manager ?? null,
                'compliance_owner' => $data->compliance_owner ?? null,
                'emt' => $data->emt ?? null,
                'cxo' => $data->cxo ?? null,
                'ceo' => $data->ceo ?? null,
            ];
            return $this->sendSuccessResponse($output, 'User Role Data Fetched Successfully !');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoleTicketsType(array $data)
    {
        $complianceOwner = [];
        $role=$data['role'];
        try {
            if ($data['type'] == 'all') {
                $roleData = $this->repository->findAll()->toArray();
                $complianceOwner = array_column($roleData, 'compliance_owner');
            } elseif ($data['type'] == 'own') {
                $roleData = $this->repository->findIn('compliance_owner', $data['login_users'])->where('role',$role)->toArray();
                $complianceOwner = array_column($roleData, 'compliance_owner');
            } else {
                if (!empty($data['role'])) {
                    if ($role=='compliance_owner'|| $role=='FAP'){
                        if ($role=='FAP'){
                            $roleData = $this->repository->findIn('compliance_owner', $data['login_users'])->where('role',$role)->toArray();
                        }else{
                            $roleData = $this->repository->findIn($data['role'], $data['login_users'])->where('role',$role)->toArray();
                        }
                    }else{
                        $roleData = $this->repository->findIn($data['role'], $data['login_users'])->toArray();
                    }
                    if (!empty($roleData)) {
                        if ($data['type'] == 'group') {
                            if (!empty($roleData)) {
                                if ($role=='FAP'){
                                    $complianceOwner = array_column($roleData, 'compliance_owner');;
                                }else{
                                    $lineManagers = array_column($roleData, 'line_manager');
                                    $roleData = $this->repository->findIn('line_manager', $lineManagers)->toArray();
                                    $complianceOwner = array_column($roleData, 'compliance_owner');
                                }
                               
                            }
                        }
                        if ($data['type'] == 'department') {
                            if (!empty($roleData)) {
                                $emts = array_column($roleData, 'emt');
                                $roleData = $this->repository->findIn('emt', $emts)->toArray();
                                $complianceOwner = array_column($roleData, 'compliance_owner');
                            }

                        }
                        if ($data['type'] == 'division') {
                            if (!empty($roleData)) {
                                $cxos = array_column($roleData, 'cxo');
                                $roleData = $this->repository->findIn('cxo', $cxos)->toArray();
                                $complianceOwner = array_column($roleData, 'compliance_owner');
                            }
                        }
                    }
                }

            }
            $result = $this->complianceEntryService->getTicketIdByComplienceOwners($complianceOwner, $data);
            return $this->sendSuccessResponse($result, 'Role User Data Fetched Successfully !');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    public function index(array $requestData)
    {
        try {
            if (isset($requestData['search']) && !empty($requestData['search'])) {
                $search = trim($requestData['search']);
                $query = UserRole::query()
                    ->where(function ($query) use ($search) {
                        $query->where('compliance_owner', 'like', "%{$search}%")
                            ->orWhere('line_manager', 'like', "%{$search}%")
                            ->orWhere('emt', 'like', "%{$search}%")
                            ->orWhere('cxo', 'like', "%{$search}%")
                            ->orWhere('ceo', 'like', "%{$search}%");
                    })
                    ->orderByDesc('id');
                return $query->paginate(20);
            }else{
                return $this->repository->findAll(20, null, ['column' => 'created_at', 'direction' => 'desc']);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return [];
        }
    }

    /**
     * get user types
     *
     * @param  $input
     * @return string
     */
    private function mapUserType($input)
    {
        $types = [
            'compliance_owner' => 'compliance_owner',
            'line_manager' => 'line_manager',
            'emt' => 'emt',
            'cxo' => 'cxo',
            'ceo' => 'ceo'
        ];
        return $types[$input];
    }

    /**
     * store method
     * this method will return only data, not response
     * because it will be called from UI
     * @param array $data
     * @return mixed
     */
    public function store(array $data)
    {
        return $this->repository->save($data);
    }

    /**
     * update method
     *
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $model = $this->repository->findOne($id);
        return $model->update($data);
    }

    /**
     * get form data
     *
     * @param $id
     * @return array
     */
    public function getFormData($id = null): array
    {
        if (is_null($id)) {
            return [
                'compliance_owner' => null,
                'role' => null,
                'line_manager' => null,
                'emt' => null,
                'cxo' => null,
                'ceo' => null
            ];
        }
        $data = $this->repository->findOne($id);
        return [
            'compliance_owner' => $data->compliance_owner,
            'role' => $data->role,
            'line_manager' => $data->line_manager,
            'emt' => $data->emt,
            'cxo' => $data->cxo,
            'ceo' => $data->ceo
        ];
    }

    public function delete($id)
    {
        $model = $this->repository->findOne($id);
        return $model->delete();
    }

    public function sync()
    {
        return $this->syncProcess();
    }
    public function syncApi()
    {
        return $this->syncProcess();
    }

    /**
     * @param $configs
     * @return mixed
     */
    private function getXpertUsers($configs)
    {
        try {
            if (empty($configs['RCMS_GROUP_ID'])) {
                throw new \InvalidArgumentException('RCMS Group ID not found in configuration.');
            }

            $groupIds = explode(',', $configs['RCMS_GROUP_ID']);
            if (empty($groupIds) || count(array_filter($groupIds)) === 0) {
                throw new \InvalidArgumentException('RCMS Group ID is empty or invalid.');
            }

            $response = $this->ticketCreationApiService->getUserByGroupIds(['group_id' => $groupIds]);

            if (!isset($response['data'])) {
                throw new \UnexpectedValueException('Invalid response received from getUserByGroupIds.');
            }

            return $response['data'];
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException('Configuration Error: ' . $e->getMessage(), 400);
        } catch (\UnexpectedValueException $e) {
            throw new \RuntimeException('Service Error: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while fetching Xpert users: ' . $e->getMessage(), 500);
        }
    }

    private function syncProcess()
    {
        try {
            $key = 'rcms-user-role-sync';
            $configs = $this->ticketCreationApiService->getSystemConfig($key);
            if (isset($configs['data']) && empty($configs['data'])) {
                Log::error('System Config Not Found with -> '. $key);
                throw new Exception('System Config Not Found with -> '. $key);
            }
            $configs = $configs['data'];
            $oneGpLoginApiKey = $configs['EXTERNAL_LOGIN_API_KEY'];
            $oneGpEmployeeBulkApiKey = $configs['EXTERNAL_EMPLOYEE_API_KEY'];

            $oneGpLoginApiExternals = $this->ticketCreationApiService->getExternalApi($oneGpLoginApiKey);
            if (isset($oneGpLoginApiExternals['data']) && empty($oneGpLoginApiExternals['data'])) {
                Log::error('External Api Not Found with -> '. $oneGpLoginApiKey);
                throw new Exception('External Api Not Found with -> '. $oneGpLoginApiKey);
            }

            $oneGpEmployeeBulkApiExternals = $this->ticketCreationApiService->getExternalApi($oneGpEmployeeBulkApiKey);
            if (isset($oneGpEmployeeBulkApiExternals['data']) && empty($oneGpEmployeeBulkApiExternals['data'])) {
                Log::error('External Api Not Found with -> '. $oneGpEmployeeBulkApiKey);
                throw new Exception('External Api Not Found with -> '. $oneGpEmployeeBulkApiKey);
            }

            $baseUrl = $oneGpEmployeeBulkApiExternals['data']['url'];
            $oneGpLoginApiExternals = $oneGpLoginApiExternals['data'];

            $xpertUsers = $this->getXpertUsers($configs);
            $token = $this->getOneGpToken($oneGpLoginApiExternals);
            if (!$token) {
                throw new Exception('Token Not Found');
            }

            $rcmsFapGroupIdsSysConfig = $this->ticketCreationApiService->getSystemConfig($configs['RCMS_FAP_GROUP_IDS']);
            if (isset($rcmsFapGroupIdsSysConfig['data']) && empty($rcmsFapGroupIdsSysConfig['data'])) {
                Log::error('System Config Not Found with -> '. $configs['RCMS_FAP_GROUP_IDS']);
                throw new Exception('System Config Not Found with -> '. $configs['RCMS_FAP_GROUP_IDS']);
            }
            $rcmsFapGroupIds = explode(",", $rcmsFapGroupIdsSysConfig['data']);

            $title = 'User Role Sync';
            $result = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => $token,
            ])->withoutVerifying()->get($baseUrl, []);

            if ($result->successful()) {
                $data = $result->json();
                $res = $this->repository->sync($data['data'], $xpertUsers, $rcmsFapGroupIds);
                $logData = [
                    'title' => $title,
                    'type' => $title,
                    'message' => 'Sync Successfull',
                    'payload' => json_encode($data),
                    'response' => json_encode($res)
                ];
                ActivityLog::create($logData);
                return [
                    'success' => true,
                    'message' => 'Sync Successful',
                    'data' => $data['data']
                ];
            } else {
                $logData = [
                    'title' => $title,
                    'type' => $title,
                    'token' => $token,
                    'message' => 'Sync Failed',
                    'payload' => json_encode($result->json()),
                ];
                ActivityLog::create($logData);
                return response()->json([
                    'success' => false,
                    'message' => 'Sync Failed',
                    'token' => $token,
                    'payload' => json_encode($result->json()),
                ]);
            }
        } catch (Exception $e) {
            $logData = [
                'title' => 'User Role Sync',
                'type' => 'User Role Sync Failed',
                'message' => 'Sync Failed',
                'payload' => $e->getMessage().' :trace= '.$e->getTraceAsString(),
            ];
            ActivityLog::create($logData);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function getOneGpToken($oneGpLoginApiExternals)
    {
        $apiUrl = $oneGpLoginApiExternals['url'];
        $data = [
            'client_id' => $oneGpLoginApiExternals['api_key'],
            'client_secret' => $oneGpLoginApiExternals['secret'],
            'grant_type' => 'client_credentials',
        ];

        $result = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withoutVerifying()->post($apiUrl, $data);

        if ($result->successful()) {
            $logData = [
                'title' => 'One GP Access Token',
                'type' => 'ONE GP TOKEN',
                'message' => 'Token Fetch Successfull',
                'payload' => json_encode($data),
                'response' => json_encode($result->json())
            ];
            ActivityLog::create($logData);
            $data = json_decode($result->body());
            $token = $data->data->token_type . " " . trim($data->data->access_token);
            Log::info($token);
            return $token;
        } else {
            $logData = [
                'title' => 'ONE GP TOKEN API',
                'type' => 'ONE GP TOKEN',
                'message' => 'ONEGP TOKEN API FAILED',
                'payload' => json_encode($data),
                'response' => json_encode($result->json())
            ];
            ActivityLog::create($logData);
            return false;
        }
    }

    public function getLastLogEntry()
    {
        // Get today's date in the format Laravel uses for log files
        $logFile = storage_path('logs/laravel-' . date('Y-m-d') . '.log');

        // Check if the log file exists
        if (File::exists($logFile)) {
            // Get the contents of the log file
            $logContents = File::get($logFile);

            preg_match_all('/Bearer\s[A-Za-z0-9\-\._~\+\/]+=*/', $logContents, $matches);
            // Check if any matches were found
            if (!empty($matches[0])) {
                $lastIndex = count($matches[0]) - 2;
                // Get the last match (last Bearer token including the word "Bearer")
                $lastToken = $matches[0][$lastIndex];
                return $lastToken;
            } else {
                return 'No token found in the log.';
            }
        }
        return false;
    }
}
