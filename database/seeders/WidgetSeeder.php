<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Widget\Models\Widget as WidgetModel;
use Botble\Theme\Facades\Theme;

class WidgetSeeder extends BaseSeeder
{
    public function run(): void
    {
        WidgetModel::query()->truncate();

        $data = [
            'en_US' => [
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 0,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Services.',
                        'menu_id' => 'services',
                    ],
                ],
                [
                    'widget_id' => 'RecentPostsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position' => 0,
                    'data' => [
                        'id' => 'RecentPostsWidget',
                        'name' => 'Recent posts',
                        'number_display' => 5,
                    ],
                ],
                [
                    'widget_id' => 'BlogCategoriesWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position' => 1,
                    'data' => [
                        'id' => 'BlogCategoriesWidget',
                        'name' => 'Categories',
                        'number_display' => 5,
                    ],
                ],
                [
                    'widget_id' => 'BlogTagsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position' => 2,
                    'data' => [
                        'id' => 'BlogTagsWidget',
                        'name' => 'Popular Tags',
                        'number_display' => 5,
                    ],
                ],
            ],
            'vi' => [
                [
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'position' => 0,
                    'data' => [
                        'id' => 'CustomMenuWidget',
                        'name' => 'Dịch vụ.',
                        'menu_id' => 'dich-vu',
                    ],
                ],
                [
                    'widget_id' => 'RecentPostsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position' => 0,
                    'data' => [
                        'id' => 'RecentPostsWidget',
                        'name' => 'Bài viết gần đây',
                        'number_display' => 5,
                    ],
                ],
                [
                    'widget_id' => 'BlogCategoriesWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position' => 1,
                    'data' => [
                        'id' => 'BlogCategoriesWidget',
                        'name' => 'Danh mục',
                        'number_display' => 5,
                    ],
                ],
                [
                    'widget_id' => 'BlogTagsWidget',
                    'sidebar_id' => 'primary_sidebar',
                    'position' => 2,
                    'data' => [
                        'id' => 'BlogTagsWidget',
                        'name' => 'Tags phổ biến',
                        'number_display' => 5,
                    ],
                ],
            ],
        ];

        $theme = Theme::getThemeName();

        foreach ($data as $locale => $widgets) {
            foreach ($widgets as $item) {
                $item['theme'] = $locale == 'en_US' ? $theme : ($theme . '-' . $locale);
                WidgetModel::query()->create($item);
            }
        }
    }
}
