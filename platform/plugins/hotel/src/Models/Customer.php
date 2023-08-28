<?php

namespace Botble\Hotel\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Customer extends BaseModel
{
    protected $table = 'ht_customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'phone',
        'dob',
    ];

    protected $casts = [
        'first_name' => SafeContent::class,
        'last_name' => SafeContent::class,
        'email' => SafeContent::class,
        'phone' => SafeContent::class,
        'dob' => SafeContent::class,
    ];

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }
}
