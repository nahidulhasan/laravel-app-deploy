<?php

namespace App\Services;

use App\Http\Resources\CeoCxoReportResource;
use App\Repositories\CeoCxoReportRepository;
use App\Traits\CrudTrait;
use App\Models\CeoCxoReport;
use Exception;


class CeoCxoReportService extends ApiBaseService
{

    use CrudTrait;

    protected $ceoCxoReportRepository;

    /**
     * CeoCxoReportService constructor.
     * @param CeoCxoReportRepository $ceoCxoReportRepository
     */
    public function __construct(CeoCxoReportRepository $ceoCxoReportRepository)
    {
        $this->ceoCxoReportRepository = $ceoCxoReportRepository;
    }

    /**
     * @param array $requestData
     * @return \App\Repositories\Collection|
     * \App\Repositories\Contracts\Collection|
     * \Illuminate\Contracts\Pagination\LengthAwarePaginator|
     * \Illuminate\Database\Eloquent\Builder[]|
     * \Illuminate\Database\Eloquent\Model[]
     */
    public function index(array $requestData)
    {
        if(isset($requestData['search_field']) && !empty($requestData['search_field'])){
            $inputValue = $requestData['search_field'];
            return CeoCxoReport::where('receiver','LIKE',"%{$inputValue}%")
                ->orWhere('report_type','LIKE',"%{$inputValue}%")
                ->orWhere('email_body','LIKE',"%{$inputValue}%")
                ->orWhere('status','LIKE',"%{$inputValue}%")
                ->orderBy('id', 'DESC')
                ->paginate(20);

        }else {
            return $this->ceoCxoReportRepository->findAll(20,[], array('column' => 'updated_at', 'direction' => 'desc'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($inputData)
    {
        try {
            if(!empty($inputData['receiver']) && !empty($inputData['email_body']) && !empty($inputData['subject'])){

                $saveStoreEmail = $this->ceoCxoReportRepository->save($inputData);

                if ($saveStoreEmail) {
                    return $this->sendSuccessResponse(['id'=>$saveStoreEmail->id], 'Email Store Successfully completed');
                } else {
                    return $this->sendErrorResponse([], 'Email Store Failed!');
                }
            }else{
                return $this->sendErrorResponse([], 'Data format is Wrong. [receiver,email_body,report_type]');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param array $requestData
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCeoCxoReport(array $requestData)
    {
        $query = CeoCxoReport::query()->orderBy('id', 'DESC');
        $limit = 20;
        if (isset($requestData['search_field'])) {
            $inputValue = $requestData['search_field'];
            $query->where('receiver', 'LIKE', "%{$inputValue}%");
            $query->orWhere('report_type', 'LIKE', "%{$inputValue}%");
            $query->orWhere('email_body', 'LIKE', "%{$inputValue}%");
            $query->orWhere('status', 'LIKE', "%{$inputValue}%");
        }

        if (isset($requestData['per_page']) && !empty($requestData['per_page'])) {
            $limit = $requestData['per_page'];
        }

        $result = $query->paginate($limit);
        $data['data'] = CeoCxoReportResource::collection($result);
        $data['pagination'] = $this->paginationResponse($result);

        return $this->sendSuccessResponse($data, 'Data Fetched Successfully!');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCeoCxoReportView($id)
    {
        if(!empty($id)) {
            $email_details = $this->ceoCxoReportRepository->findOne($id);
            if (!empty($email_details)) {
                $result = $email_details;
            } else {
                $result = [];
            }
            return $this->sendSuccessResponse($result, 'Data Fetched Successfully!');
        }else{
            return $this->sendErrorResponse('something went wrong');
        }
    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmailStatus($data)
    {
        if(isset($data['id']) && !empty($data['id'])) {
            $id = trim($data['id']);
            $email = $this->ceoCxoReportRepository->findOrFail($id);
            $email ->status = $data['status'];
            if ($email->save()) {
                return $this->sendSuccessResponse([], 'Data update Successfully!');
            } else {
                return $this->sendErrorResponse('something went wrong');
            }

        }else{
            return $this->sendErrorResponse('something went wrong');
        }
    }

}
