<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ComplianceEntryRequest;
use App\Services\CategoryService;
use App\Services\ComplianceEntryService;
use App\Services\PeriodicTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{

    protected $categoryService;


    /**
     * PeriodicTicketController constructor.
     * @param PeriodicTicketService $periodicTicketService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->categoryService->index();
    }
}
