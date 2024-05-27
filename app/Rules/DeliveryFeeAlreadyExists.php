<?php

namespace App\Rules;

use App\Models\DeliveryFee;
use Illuminate\Contracts\Validation\Rule;

class DeliveryFeeAlreadyExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $query = DeliveryFee::where('region_id', request()->region_id)
            ->where('city', request()->city)
            ->where('fee', $value);

        if (request()->delivery_fee) $query = $query->where('id', '!=', request()->delivery_fee->id);

        $check = $query->exists();

        if ($check) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Delivery Fee already exists!.';
    }
}
