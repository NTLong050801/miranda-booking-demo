<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;

class CheckoutRequest extends Request
{
    public function rules(): array
    {
        return [
            'room_id' => 'required|integer|min:1',
            'start_date' => 'date|required:date_format:d-m-Y',
            'end_date' => 'date|required:date_format:d-m-Y',
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'phone' => 'required|' . BaseHelper::getPhoneValidationRule(),
            'number_of_guests' => 'nullable|integer|min:1',
            'zip' => 'nullable|string|max:10',
            'services' => 'nullable|array',
            'terms_conditions' => 'accepted:1',
        ];
    }
}
