<?php

namespace App\Services;

use App\Repositories\FormFieldMappingRepository;
use App\Traits\CrudTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormFieldMappingService extends ApiBaseService
{

    use CrudTrait;

    /**
     * @var GroupRepository
     */
    protected $repository;

    /**
     * Compliance Entry Repository
     */
    public function __construct(FormFieldMappingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function checkForNewFields(array $requestData)
    {
        $storedFields = $this->getFormFieldMappings();

        foreach($requestData as $key=>$value)
        {
            $pascalCaseKey = $this->snakeCaseToPascalCase($key);
            if(!in_array($pascalCaseKey,$storedFields))
            {
                // add a column of this name
                if(!Schema::hasColumn('compliance_entry',$key))
                {
                    //todo:: insert form_field_id  as pascal case 
                    $this->repository->save(['form_field_id'=>$pascalCaseKey,'compliance_entry_table_column_reference'=>$key]);
                    Schema::table('compliance_entry', function (Blueprint $table) use($key) {
                        $table->string($key)->nullable();
                    });
                }
            }
        }
    }

    public function getFormFieldMappings(){
        return $this->repository->all()->pluck('form_field_id')->toArray();
    }
    public function getAllFormFieldMappings(){
        return $this->repository->all()->toArray();
    }
     /**
     * change pascal case to snake case 
     *
     * @param [string] $str
     * @return void
     */
    private function snakeCaseToPascalCase($str)
    {
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        return $str;
    }
}