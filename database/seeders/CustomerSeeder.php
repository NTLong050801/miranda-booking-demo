<?php

namespace Database\Seeders;

use Botble\Hotel\Models\Customer;
use Botble\Base\Supports\BaseSeeder;

class CustomerSeeder extends BaseSeeder
{
    public function run(): void
    {
        Customer::query()->truncate();

        for ($i = 0; $i < 10; $i++) {
            Customer::query()->create([
                'first_name' => fake()->firstName,
                'last_name' => fake()->lastName,
                'email' => fake()->safeEmail,
                'phone' => fake()->e164PhoneNumber,
            ]);
        }
    }
}
