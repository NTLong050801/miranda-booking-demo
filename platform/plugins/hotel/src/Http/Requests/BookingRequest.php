<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Captcha\Facades\Captcha;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class BookingRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'status' => Rule::in(BookingStatusEnum::values()),
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();
        }

        return $rules;
    }

    public function attributes(): array
    {
        return is_plugin_active('captcha') ? Captcha::attributes() : [];
    }
}
