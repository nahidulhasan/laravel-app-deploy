<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupResource
 * @package App\Http\Resources
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'            => $this->id ?? null,
            'name'          => $this->name ?? null,
            'sub_category'          =>  $this->subCategory?SubCategoryResource::collection($this->subCategory): null
        ];

    }

}
