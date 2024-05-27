<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255|unique:products,name,' . request()->product->id,
            'weight' => 'required|max:255' ,
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'required|exists:sub_categories,id',
        ];

        if ($this->instock) {
            $rules['instock'] = 'in:1,0';
        }

        return $rules;
    }

}
