<?php

namespace App\Http\Requests;

use App\Rules\DeliveryFeeAlreadyExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryFeeRequest extends FormRequest
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
            'region_id' => 'required|exists:regions,id',
            'city'      => 'required|max:255',
            'fee'       => ['required', 'numeric', new DeliveryFeeAlreadyExists()],
        ];
        
        return $rules;
    }
}
