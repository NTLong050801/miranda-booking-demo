<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Hotel\Models\FoodType;
use Illuminate\Support\Facades\DB;

class FoodTypeSeeder extends BaseSeeder
{
    public function run(): void
    {
        FoodType::query()->truncate();

        $foodTypes = [
            [
                'name' => 'Chicken',
                'icon' => 'flaticon-boiled',
            ],
            [
                'name' => 'Italian',
                'icon' => 'flaticon-pizza',
            ],
            [
                'name' => 'Coffee',
                'icon' => 'flaticon-coffee',
            ],
            [
                'name' => 'Bake Cake',
                'icon' => 'flaticon-cake',
            ],
            [
                'name' => 'Cookies',
                'icon' => 'flaticon-cookie',
            ],
            [
                'name' => 'Cocktail',
                'icon' => 'flaticon-cocktail',
            ],
        ];

        foreach ($foodTypes as $foodType) {
            FoodType::query()->create($foodType);
        }

        DB::table('ht_food_types_translations')->truncate();

        $translations = [
            [
                'name' => 'Gà rán',
            ],
            [
                'name' => 'Món Ý',
            ],
            [
                'name' => 'Cà Phê',
            ],
            [
                'name' => 'Bánh Bake',
            ],
            [
                'name' => 'Bánh Cookies',
            ],
            [
                'name' => 'Cocktail',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ht_food_types_id'] = $index + 1;

            DB::table('ht_food_types_translations')->insert($item);
        }
    }
}
