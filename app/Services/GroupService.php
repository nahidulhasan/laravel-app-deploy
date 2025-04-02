<?php


namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\GroupResource;
use App\Repositories\GroupRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class GroupService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;


    /**
     * GroupService constructor.
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }


    public function addGroup($request)
    {
       return $this->groupRepository->save($request->all());
    }


    /**
     * @return JsonResponse
     */
    public function getGroupList()
    {
        try {

            $groups = $this->groupRepository->all();
            $data = GroupResource::collection($groups);
            return $this->sendSuccessResponse($data, 'Data fetched Successfully!');

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

}
