<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'departemen_id' => $this->departemen_id,
            'kondisi_awal' => $this->kondisi_awal,
            'saran_usulan' => $this->saran_usulan,
            'file' => $this->file,
        ];
    }
}
