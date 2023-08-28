<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CustomerCreateRequest extends Request
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:60|min:2',
            'last_name' => 'required|max:60|min:2',
            'email' => 'required|max:60|min:6|email|unique:ht_customers',
        ];
    }
}
