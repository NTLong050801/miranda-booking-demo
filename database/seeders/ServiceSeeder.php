<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Hotel\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends BaseSeeder
{
    public function run(): void
    {
        Service::query()->truncate();

        $services = [
            [
                'name' => 'Wifi',
                'price' => 100,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel molestie nisl. Duis ac mi leo.',
            ],
            [
                'name' => 'Car Rental',
                'price' => 30,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel molestie nisl. Duis ac mi leo.',
            ],
            [
                'name' => 'Satellite TV',
                'price' => 50,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel molestie nisl. Duis ac mi leo.',
            ],
            [
                'name' => 'Sea View',
                'price' => 10,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel molestie nisl. Duis ac mi leo.',
            ],
            [
                'name' => 'Laundry',
                'price' => 10,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel molestie nisl. Duis ac mi leo.',
            ],
            [
                'name' => 'Breakfast',
                'price' => 10,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer vel molestie nisl. Duis ac mi leo.',
            ],
        ];

        foreach ($services as $service) {
            Service::query()->create($service);
        }

        DB::table('ht_services_translations')->truncate();

        $translations = [
            [
                'name' => 'Wifi',
            ],
            [
                'name' => 'Thuê xe ôtô',
            ],
            [
                'name' => 'TV vệ tinh',
            ],
            [
                'name' => 'View biển',
            ],
            [
                'name' => 'Giặt là',
            ],
            [
                'name' => 'Bữa sáng',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ht_services_id'] = $index + 1;

            DB::table('ht_services_translations')->insert($item);
        }
    }
}
