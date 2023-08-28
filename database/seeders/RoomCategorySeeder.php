<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Hotel\Models\RoomCategory;
use Illuminate\Support\Facades\DB;

class RoomCategorySeeder extends BaseSeeder
{
    public function run(): void
    {
        RoomCategory::query()->truncate();

        $roomCategories = [
            [
                'name' => 'Luxury',
            ],
            [
                'name' => 'Family',
            ],
            [
                'name' => 'Double Bed',
            ],
            [
                'name' => 'Relax',
            ],
        ];

        foreach ($roomCategories as $roomCategory) {
            $roomCategory['is_featured'] = true;

            RoomCategory::query()->create($roomCategory);
        }

        DB::table('ht_room_categories_translations')->truncate();

        $translations = [
            [
                'name' => 'Sang trọng',
            ],
            [
                'name' => 'Gia đình',
            ],
            [
                'name' => 'Giường đôi',
            ],
            [
                'name' => 'Thư giãn',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ht_room_categories_id'] = $index + 1;

            DB::table('ht_room_categories_translations')->insert($item);
        }
    }
}
