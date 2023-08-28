<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Gallery\Models\Gallery as GalleryModel;
use Botble\Gallery\Models\GalleryMeta;
use Botble\Language\Models\LanguageMeta;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class GallerySeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('galleries');

        GalleryModel::query()->truncate();
        GalleryMeta::query()->truncate();
        DB::table('galleries_translations')->truncate();
        Slug::query()->where('reference_type', GalleryModel::class)->delete();
        MetaBoxModel::query()->where('reference_type', GalleryModel::class)->delete();
        LanguageMeta::query()->where('reference_type', GalleryModel::class)->delete();

        $galleries = [
            [
                'name' => 'Duplex Restaurant',
            ],
            [
                'name' => 'Luxury room',
            ],
            [
                'name' => 'Pacific Room',
            ],
            [
                'name' => 'Family Room',
            ],
            [
                'name' => 'King Bed',
            ],
            [
                'name' => 'Special Foods',
            ],
        ];

        $images = [];
        for ($i = 0; $i < 10; $i++) {
            $images[] = [
                'img' => 'galleries/' . ($i + 1) . '.jpg',
                'description' => fake()->text(150),
            ];
        }

        foreach ($galleries as $index => $item) {
            $item['description'] = fake()->text(150);
            $item['image'] = 'galleries/0' . ($index + 1) . '.jpg';
            $item['user_id'] = 1;
            $item['is_featured'] = true;

            $gallery = GalleryModel::query()->create($item);

            Slug::query()->create([
                'reference_type' => GalleryModel::class,
                'reference_id' => $gallery->id,
                'key' => Str::slug($gallery->name),
                'prefix' => SlugHelper::getPrefix(GalleryModel::class),
            ]);

            GalleryMeta::query()->create([
                'images' => json_encode($images),
                'reference_id' => $gallery->id,
                'reference_type' => GalleryModel::class,
            ]);
        }

        $translations = [
            [
                'name' => 'Nhà hàng Duplex',
            ],
            [
                'name' => 'Phòng sang trọng',
            ],
            [
                'name' => 'Phòng Pacific',
            ],
            [
                'name' => 'Phòng gia đình',
            ],
            [
                'name' => 'Giường lớn',
            ],
            [
                'name' => 'Ẩm thực đặc biệt',
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['galleries_id'] = $index + 1;

            DB::table('galleries_translations')->insert($item);
        }
    }
}
