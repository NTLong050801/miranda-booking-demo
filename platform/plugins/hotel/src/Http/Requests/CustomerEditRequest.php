<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CustomerEditRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:120|min:2',
            'email' => 'required|max:60|min:6|email|unique:ht_customers,email,' . $this->route('customer'),
        ];
    }
}
