<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "category" => $this->category_id ? new CategoryResource($this->category) : '',
            "sub_category" => $this->sub_category_id ? new CategoryResource($this->category) : '',
            "weight" => $this->weight,
            "status" => $this->status,
            "images" => ProductImageResource::collection($this->images),
            'instock' => $this->instock,
            "created_at" => $this->created_at,
        ];
    }
}
