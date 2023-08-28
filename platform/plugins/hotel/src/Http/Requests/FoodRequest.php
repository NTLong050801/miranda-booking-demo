<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class FoodRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'food_type_id' => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
