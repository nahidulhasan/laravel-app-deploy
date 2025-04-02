<?php
namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\ClientCredential;
use App\Repositories\ClientCredentialRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;


/**
 * Class ClientCredentialService
 * @package App\Services
 */
class ClientCredentialService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var ClientCredentialRepository
     */
    protected $clientCredentialRepository;

    /**
     * ClientCredentialService constructor.
     * @param ClientCredentialRepository $clientCredentialRepository
     */
    public function __construct(ClientCredentialRepository $clientCredentialRepository)
    {
        $this->clientCredentialRepository = $clientCredentialRepository;
    }


    /**
     * @return mixed
     */
    public function getList()
    {
       $data = $this->clientCredentialRepository->findAll();
       return $data->toArray();
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getCredentialById($id)
    {
        $data = $this->clientCredentialRepository->findOne($id);

        return $data;
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update($request, $id)
    {
        try {
            $credential = $this->clientCredentialRepository->findOne($id);
            if (!empty($credential->id)) {
                $credential->update($request->all());
            }

            return true;
        } catch (QueryException $ex) {
           return false;
        }
    }

    /**
     * @param $token
     * @return bool
     */
    public static function isValidToken($token)
    {
        $credential = ClientCredential::where('secret', '=', $token)->first();
        if($credential!= null){
            return true;
        }
        return false;
    }

}
