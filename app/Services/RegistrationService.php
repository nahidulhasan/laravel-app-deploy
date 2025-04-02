<?php
namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Models\User;
use App\Models\ProfileLog;
use App\Models\VerifyUser;
use App\Mail\VerifyMail as VerifyEmail;
use App\Repositories\RepresentativeInformationRepository;
use App\Repositories\UserRoleRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Mail;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository as EmailVerifyRepository;
use App\Repositories\RepresentativeInformationRepository as RepresentativeRepository;
use App\Transformers\CandidateTransformer;
use App\Repositories\CandidateRepository;
use App\Repositories\ProfileLogRepository;
use DB;
use Symfony\Component\HttpFoundation\Response as FResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;


/**
 * Class RegistrationService
 * @package App\Services
 */
class RegistrationService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;
    protected $userRoleRepository;

    /**
     * @var TicketCreationApiService
     */
    protected $ticketCreationApiService;

    /**
     * UserService constructor.
     *
     * @param UserRepository $UserRepository
     * @param EmailVerifyRepository $emailVerifyRepository
     */
    public function __construct(
        UserRepository $UserRepository,
        TicketCreationApiService $ticketCreationApiService,
        UserRoleRepository $userRoleRepository
    ) {
        $this->userRepository = $UserRepository;
        $this->ticketCreationApiService = $ticketCreationApiService;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * this function use for user registration
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register($request)
    {
        try {
            $data = array();
            $inputData = $request->all();
            $inputData['password'] = Hash::make($request->get('password'));
            // email emailVerify bypass
            $inputData['is_verified'] = 1;
            $user = $this->userRepository->save($inputData);
            if ($user) {
                VerifyUser::create([
                    'user_id' => $user->id,
                    'token' => sha1(time()) . $user->id,
                ]);
                Mail::to($user->email)->send(new VerifyEmail($user, HttpStatusCode::WEB_DOMAIN));
                //                event(new Registered($user));
                self::authenticate($request);
                $expireTime = auth('api')->factory()->getTTL() * 60;
                $dateTime = Carbon::now()->addSeconds($expireTime);
                $token = JWTAuth::fromUser($user);
                $data['token'] = self::TokenFormater($token);
                $data['user'] = $user;
                return $this->sendSuccessResponse($data, 'User registration successfully completed', [], FResponse::HTTP_CREATED);
            } else {
                return $this->sendErrorResponse('Something went wrong. try again later', [], FResponse::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * This function use for user login by email and password
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $data = array();
        try {

            $userInfo = User::where('email', $request->input('email'))->first();
            if (empty($userInfo)) {
                return $this->sendErrorResponse(
                    'You are not a registered you should registration first ',
                    [],
                    HttpStatusCode::BAD_REQUEST
                );
                //                throw (new ModelNotFoundException)->setModel(get_class($this->userRepository->getModel()), $request['email']);
            }
            if ($userInfo->is_verified == 0) {
                return $this->sendErrorResponse(
                    'Please check your email to verify your account ( ' . $userInfo->email . ' )',
                    [],
                    HttpStatusCode::BAD_REQUEST
                );
            }

            if ($userInfo->status == 2) {
                //  status == 2 delete account
                return $this->sendErrorResponse(
                    'This account hase been deleted ( ' . $userInfo->email . ' )',
                    [],
                    HttpStatusCode::BAD_REQUEST
                );
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->sendErrorResponse(
                    'Invalid credentials',
                    ['detail' => 'Ensure that the email and password included in the request are correct'],
                    HttpStatusCode::BAD_REQUEST
                );
            } else {
                $data['token'] = self::TokenFormater($token);
                $data['user'] = $userInfo;
                return $this->sendSuccessResponse($data, 'Login successfully');
            }
        } catch (JWTException $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout($token)
    {
        if (empty($token)) {
            return $this->sendErrorResponse('Authorization token is empty', [], HttpStatusCode::VALIDATION_ERROR);
        }

        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->sendSuccessResponse([], 'User has been logged out');
        } catch (JWTException $exception) {
            return $this->sendErrorResponse('Sorry, user cannot be logged out', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function dropdown($query)
    {
        try {
            $data = $this->ticketCreationApiService->getUserDropdown($query);
            if(!isset($data['data'])){
                $data['data'] = [];
            }
            return $this->sendSuccessResponse($data['data'], 'User Dropdown Data Fetched Successfully');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function dropdownForEmail($query)
    {
        $output=[];
        try {
            $getUsers = $this->ticketCreationApiService->getUserDropdown($query);
            if(!isset($getUsers['data'])){
                $output['data'] = [];
            }
            if (!empty($getUsers['data'])){
                $users=array_column($getUsers['data'],'username');
                $data = $this->userRoleRepository->findBy(['compliance_owner' => $users[0]])->first();
                $output = [
                    'line_manager' => $data->line_manager ?? null,
                    'compliance_owner' => $data->compliance_owner ?? null,
                    'emt' => $data->emt ?? null,
                    'cxo' => $data->cxo ?? null,
                    'ceo' => $data->ceo ?? null,
                ];
            }

            return $this->sendSuccessResponse($output, 'User Role Data Fetched Successfully !');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
