<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupResource
 * @package App\Http\Resources
 */
class GroupResource extends JsonResource
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
            'alias'          => $this->alias ?? null,
            'status'        => $this->status
        ];

    }

}
