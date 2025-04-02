<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupResource
 * @package App\Http\Resources
 */
class CeoCxoReportResource extends JsonResource
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
            'id' => $this->id ?? null,
            'receiver' => $this->receiver ?? null,
            'subject' => $this->subject ?? null,
            'email_body' => $this->email_body ?? null,
            'report_type' => $this->report_type ?? null,
            'status' => $this->status ?? null,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];
    }

}
