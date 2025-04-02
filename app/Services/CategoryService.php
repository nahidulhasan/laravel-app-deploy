<?php

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;
use App\Repositories\ComplianceEntryRepository;
use App\Repositories\PeriodicTicketRepository;
use App\Traits\CrudTrait;
use Carbon\Carbon;
use Exception;

class CategoryService extends ApiBaseService
{

    use CrudTrait;

    protected $categoryRepository;


    /**
     * Compliance Entry Repository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get category
     */
    public function index()
    {

        //get compliance entry for creating Periodic Ticket.
        try {
            $categories = $this->categoryRepository->getCategory();
            $categorySubcategory=[];
            $count=0;
            foreach ($categories as $key=> $category) {
                foreach ($category->subCategory as $keySub=> $subCat) {
                    $categorySubcategory[$count]['category']=$category->name;
                    $categorySubcategory[$count]['sub_category']=$subCat->name;
                    $count++;
                }
            }
            return $this->sendSuccessResponse($categorySubcategory, 'Data fetched Successfully!');

        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }




}
