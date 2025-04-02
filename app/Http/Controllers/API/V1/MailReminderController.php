<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\MailReminderService;

/**
 * Class GroupController
 * @package App\Http\Controllers\API\V1
 */
class MailReminderController extends Controller
{

    /**
     * @var MailReminderService
     */
    protected $mailReminderService;


    /**
     * GroupController constructor.
     * @param GroupService $groupService
     */
    public function __construct(MailReminderService $mailReminderService)
    {
        $this->mailReminderService = $mailReminderService;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index()
    {
        return  $this->mailReminderService->index();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ceoEmailReminder()
    {
        return  $this->mailReminderService->cxoCeoEmailReminder('ceo');
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cxoEmailReminder()
    {
        return  $this->mailReminderService->cxoCeoEmailReminder('cxo');
    }



    /**
     * @return mixed
     */
    public function emtEmailReminder()
    {
        return  $this->mailReminderService->emtEmailReminder('emt');
    }


}
