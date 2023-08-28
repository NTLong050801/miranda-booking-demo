<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Hotel\Models\Amenity;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends BaseSeeder
{
    public function run(): void
    {
        Amenity::query()->truncate();

        $amenities = [
            [
                'name' => 'Air conditioner',
                'icon' => 'fal fa-bath',
            ],
            [
                'name' => 'High speed WiFi',
                'icon' => 'fal fa-wifi',
            ],
            [
                'name' => 'Strong Locker',
                'icon' => 'fal fa-key',
            ],
            [
                'name' => 'Breakfast',
                'icon' => 'fal fa-cut',
            ],
            [
                'name' => 'Kitchen',
                'icon' => 'fal fa-guitar',
            ],
            [
                'name' => 'Smart Security',
                'icon' => 'fal fa-lock',
            ],
            [
                'name' => 'Cleaning',
                'icon' => 'fal fa-broom',
            ],
            [
                'name' => 'Shower',
                'icon' => 'fal fa-shower',
            ],
            [
                'name' => '24/7 Online Support',
                'icon' => 'fal fa-headphones-alt',
            ],
            [
                'name' => 'Grocery',
                'icon' => 'fal fa-shopping-basket',
            ],
            [
                'name' => 'Single bed',
                'icon' => 'fal fa-bed',
            ],
            [
                'name' => 'Expert Team',
                'icon' => 'fal fa-users',
            ],
            [
                'name' => 'Shop near',
                'icon' => 'fal fa-shopping-cart',
            ],
            [
                'name' => 'Towels',
                'icon' => 'fal fa-bus',
            ],
        ];

        foreach ($amenities as $amenity) {
            Amenity::query()->create($amenity);
        }

        DB::table('ht_amenities_translations')->truncate();

        $translations = [
            [
                'name' => 'Điều hòa',
            ],
            [
                'name' => 'WiFi tốc độ cao',
            ],
            [
                'name' => 'Khóa chống trộm',
            ],
            [
                'name' => 'Bữa sáng',
            ],
            [
                'name' => 'Nhà bếp',
            ],
            [
                'name' => 'Bảo vệ thông minh',
            ],
            [
                'name' => 'Dọn dẹp',
            ],
            [
                'name' => 'Xông hơi',
            ],
            [
                'name' => 'Hỗ trợ 24/7',
            ],
            [
                'name' => 'Mua sắm',
            ],
            [
                'name' => 'Giường đơn',
            ],
            [
                'name' => 'Đội ngũ chuyên nghiệp',
            ],
            [
                'name' => 'Gần cửa hàng tiện lợi',
            ],
            [
                'name' => 'Vòi hoa sen',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ht_amenities_id'] = $index + 1;

            DB::table('ht_amenities_translations')->insert($item);
        }
    }
}
