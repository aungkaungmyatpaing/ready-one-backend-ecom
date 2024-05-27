<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeSubCategoryRequest extends FormRequest
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
//            'name' => 'required|max:255|unique:sub_categories,name,'
        'name'=>'required|max:255|unique:sub_categories,name',
        ];

        if (request()->category) {
            $rules = [
                'name' => 'required|max:255|unique:sub_categories,name,' . request()->category->id
            ];
        }

        return $rules;
    }
}