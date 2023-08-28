<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Hotel\Models\Place;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class PlaceSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('places');

        Place::query()->truncate();

        $places = [
            [
                'name' => 'Duplex Restaurant',
                'distance' => '1,500m | 21 min. Walk',
                'content' => fake()->realText(1000),
                'image' => 'places/01.jpg',
            ],
            [
                'name' => 'Duplex Restaurant',
                'distance' => '1,500m | 21 min. Walk',
                'content' => fake()->realText(1000),
                'image' => 'places/02.jpg',
            ],
            [
                'name' => 'Duplex Restaurant',
                'distance' => '1,500m | 21 min. Walk',
                'content' => fake()->realText(1000),
                'image' => 'places/03.jpg',
            ],
            [
                'name' => 'Duplex Restaurant',
                'distance' => '1,500m | 21 min. Walk',
                'content' => fake()->realText(1000),
                'image' => 'places/04.jpg',
            ],
            [
                'name' => 'Duplex Restaurant',
                'distance' => '1,500m | 21 min. Walk',
                'content' => fake()->realText(1000),
                'image' => 'places/05.jpg',
            ],
            [
                'name' => 'Duplex Restaurant',
                'distance' => '1,500m | 21 min. Walk',
                'content' => fake()->realText(1000),
                'image' => 'places/06.jpg',
            ],
        ];

        Slug::query()->where(['reference_type' => Place::class])->delete();

        foreach ($places as $place) {
            $place = Place::query()->create($place);

            Slug::query()->create([
                'reference_type' => Place::class,
                'reference_id' => $place->id,
                'key' => Str::slug($place->name),
                'prefix' => SlugHelper::getPrefix(Place::class),
            ]);
        }

        DB::table('ht_places_translations')->truncate();

        $translations = [
            [
                'name' => 'Nhà hàng Duplex',
                'distance' => '1,500 mét | 21 phút đi bộ',
            ],
            [
                'name' => 'Nhà hàng Duplex',
                'distance' => '1,500 mét | 21 phút đi bộ',
            ],
            [
                'name' => 'Nhà hàng Duplex',
                'distance' => '1,500 mét | 21 phút đi bộ',
            ],
            [
                'name' => 'Nhà hàng Duplex',
                'distance' => '1,500 mét | 21 phút đi bộ',
            ],
            [
                'name' => 'Nhà hàng Duplex',
                'distance' => '1,500 mét | 21 phút đi bộ',
            ],
            [
                'name' => 'Nhà hàng Duplex',
                'distance' => '1,500 mét | 21 phút đi bộ',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['ht_places_id'] = $index + 1;

            DB::table('ht_places_translations')->insert($item);
        }
    }
}
