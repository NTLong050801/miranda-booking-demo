<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Support\Arr;
use Botble\Menu\Facades\Menu;

class MenuSeeder extends BaseSeeder
{
    public function run(): void
    {
        $data = [
            'en_US' => [
                [
                    'name' => 'Header menu',
                    'slug' => 'header-menu',
                    'location' => 'header-menu',
                    'items' => [
                        [
                            'title' => 'Home',
                            'url' => '/',
                        ],
                        [
                            'title' => 'Rooms',
                            'url' => '/rooms',
                            'children' => [
                                [
                                    'title' => 'Luxury Hall Of Fame',
                                    'url' => '/rooms/luxury-hall-of-fame',
                                ],
                                [
                                    'title' => 'Pendora Fame',
                                    'url' => '/rooms/pendora-fame',
                                ],
                            ],
                        ],
                        [
                            'title' => 'News',
                            'reference_id' => 2,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Contact',
                            'reference_id' => 3,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name' => 'Our pages',
                    'slug' => 'our-pages',
                    'location' => 'side-menu',
                    'items' => [
                        [
                            'title' => 'About Us',
                            'reference_id' => 6,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Our Gallery',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                            'children' => [
                                [
                                    'title' => 'King Bed',
                                    'url' => '/galleries/king-bed',
                                    'parent_id' => 8,
                                ],
                                [
                                    'title' => 'Duplex Restaurant',
                                    'url' => '/galleries/duplex-restaurant',
                                    'parent_id' => 8,
                                ],
                            ],
                        ],
                        [
                            'title' => 'Restaurant',
                            'reference_id' => 4,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Places',
                            'reference_id' => 7,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Our Offers',
                            'reference_id' => 8,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name' => 'Services.',
                    'slug' => 'services',
                    'items' => [
                        [
                            'title' => 'Restaurant & Bar',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Swimming Pool',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Restaurant',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Conference Room',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Cocktail Party Houses',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Gaming Zone',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Marriage Party',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Party Planning',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Tour Consultancy',
                            'url' => '#',
                        ],
                    ],
                ],
            ],
            'vi' => [
                [
                    'name' => 'Menu trên cùng',
                    'slug' => 'menu-tren-cung',
                    'location' => 'header-menu',
                    'items' => [
                        [
                            'title' => 'Trang chủ',
                            'url' => '/',
                        ],
                        [
                            'title' => 'Phòng',
                            'url' => '/rooms',
                            'children' => [
                                [
                                    'title' => 'Đại sảnh danh vọng sang trọng',
                                    'url' => '/rooms/luxury-hall-of-fame',
                                ],
                                [
                                    'title' => 'Pendora Fame',
                                    'url' => 'vi//rooms/pendora-fame',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Tin tức',
                            'reference_id' => 2,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Liên hệ',
                            'reference_id' => 3,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name' => 'Các trang',
                    'slug' => 'menu-trang',
                    'location' => 'side-menu',
                    'items' => [
                        [
                            'title' => 'Về chúng tôi',
                            'reference_id' => 6,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Thư viện ảnh',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                            'children' => [
                                [
                                    'title' => 'King Bed',
                                    'url' => '/galleries/king-bed',
                                ],
                                [
                                    'title' => 'Duplex Restaurant',
                                    'url' => '/galleries/duplex-restaurant',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Nhà hàng',
                            'reference_id' => 4,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Địa điểm',
                            'reference_id' => 7,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Khuyến mãi',
                            'reference_id' => 8,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name' => 'Dịch vụ.',
                    'slug' => 'dich-vu',
                    'items' => [
                        [
                            'title' => 'Nhà hàng & quầy Bar',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Hồ bơi',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Nhà hàng',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Phòng họp',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Tiệc Cocktail',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Khu vực chơi game',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Tiệc cưới',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Kế hoạch tổ chức tiệc',
                            'url' => '#',
                        ],
                        [
                            'title' => 'Tư vấn du lịch',
                            'url' => '#',
                        ],
                    ],
                ],
            ],
        ];

        MenuModel::query()->truncate();
        MenuLocation::query()->truncate();
        MenuNode::query()->truncate();
        LanguageMeta::query()->where('reference_type', MenuModel::class)->delete();
        LanguageMeta::query()->where('reference_type', MenuLocation::class)->delete();

        foreach ($data as $locale => $menus) {
            foreach ($menus as $index => $item) {
                $menu = MenuModel::query()->create(Arr::except($item, ['items', 'location']));

                if (isset($item['location'])) {
                    $menuLocation = MenuLocation::query()->create([
                        'menu_id' => $menu->id,
                        'location' => $item['location'],
                    ]);

                    $originValue = LanguageMeta::query()->where([
                        'reference_id' => $locale == 'en_US' || ! is_int($menu->id) ? $menu->id : $menu->id + 3,
                        'reference_type' => MenuLocation::class,
                    ])->value('lang_meta_origin');

                    LanguageMeta::saveMetaData($menuLocation, $locale, $originValue);
                }

                foreach ($item['items'] as $menuNode) {
                    $this->createMenuNode($index, $menuNode, $locale, $menu->id);
                }

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::query()->where([
                        'reference_id' => $index + 1,
                        'reference_type' => MenuModel::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($menu, $locale, $originValue);
            }
        }

        Menu::clearCacheMenuItems();
    }

    protected function createMenuNode(int $index, array $menuNode, string $locale, int|string $menuId, int|string $parentId = 0): void
    {
        $menuNode['menu_id'] = $menuId;
        $menuNode['parent_id'] = $parentId;

        if (isset($menuNode['url'])) {
            $menuNode['url'] = str_replace(url(''), '', $menuNode['url']);
        }

        if (Arr::has($menuNode, 'children')) {
            $children = $menuNode['children'];
            $menuNode['has_child'] = true;

            unset($menuNode['children']);
        } else {
            $children = [];
            $menuNode['has_child'] = false;
        }

        $createdNode = MenuNode::query()->create($menuNode);

        if ($children) {
            foreach ($children as $child) {
                $this->createMenuNode($index, $child, $locale, $menuId, $createdNode->id);
            }
        }
    }
}
