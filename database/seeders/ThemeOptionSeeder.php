<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Setting\Models\Setting as SettingModel;
use Botble\Theme\Facades\Theme;

class ThemeOptionSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('general');
        $this->uploadFiles('sliders');

        $theme = Theme::getThemeName();

        SettingModel::query()->insertOrIgnore([
            [
                'key' => 'show_admin_bar',
                'value' => '1',
            ],
            [
                'key' => 'theme',
                'value' => $theme,
            ],
            [
                'key' => 'admin_logo',
                'value' => 'general/logo-white.png',
            ],
            [
                'key' => 'admin_favicon',
                'value' => 'general/favicon.png',
            ],
        ]);

        $data = [
            [
                'key' => 'site_title',
                'value' => 'Hotel Miranda',
            ],
            [
                'key' => 'copyright',
                'value' => '©' . now()->format('Y') . ' Miranda. All right reserved.',
            ],
            [
                'key' => 'cookie_consent_message',
                'value' => 'Your experience on this site will be improved by allowing cookies ',
            ],
            [
                'key' => 'cookie_consent_learn_more_url',
                'value' => url('cookie-policy'),
            ],
            [
                'key' => 'cookie_consent_learn_more_text',
                'value' => 'Cookie Policy',
            ],
            [
                'key' => 'homepage_id',
                'value' => '1',
            ],
            [
                'key' => 'blog_page_id',
                'value' => '2',
            ],
            [
                'key' => 'logo',
                'value' => 'general/logo.png',
            ],
            [
                'key' => 'logo_white',
                'value' => 'general/logo-white.png',
            ],
            [
                'key' => 'favicon',
                'value' => 'general/favicon.png',
            ],
            [
                'key' => 'email',
                'value' => 'info@webmail.com',
            ],
            [
                'key' => 'address',
                'value' => '14/A, Miranda City, NYC',
            ],
            [
                'key' => 'hotline',
                'value' => '+908 987 877 09',
            ],
            [
                'key' => 'news_banner',
                'value' => 'general/banner-news.jpg',
            ],
            [
                'key' => 'rooms_banner',
                'value' => 'general/banner-news.jpg',
            ],
            [
                'key' => 'term_of_use_url',
                'value' => '#',
            ],
            [
                'key' => 'privacy_policy_url',
                'value' => '#',
            ],
            [
                'key' => 'preloader_enabled',
                'value' => 'no',
            ],
            [
                'key' => 'about-us',
                'value' => 'Lorem ipsum dolor sit amet, consect etur adipisicing elit, sed doing eius mod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat ion ullamco laboris nisi.',
            ],
            [
                'key' => 'hotel_rules',
                'value' => '<ul><li>No smoking, parties or events.</li><li>Check-in time from 2 PM, check-out by 10 AM.</li><li>Time to time car parking</li><li>Download Our minimal app</li><li>Browse regular our website</li></ul>',
            ],
            [
                'key' => 'cancellation',
                'value' => '<p>Phasellus volutpat neque a tellus venenatis, a euismod augue facilisis. Fusce ut metus mattis, consequat metus nec, luctus lectus. Pellentesque orci quis hendrerit sed eu dolor. <strong>Cancel up</strong> to <strong>14 days</strong> to get a full refund.</p>',
            ],
        ];

        SettingModel::query()->whereIn('key', collect($data)->pluck('key'))->delete();

        foreach ($data as $item) {
            $item['key'] = 'theme-' . $theme . '-' . $item['key'];

            SettingModel::query()->insertOrIgnore($item);
        }

        $socialLinks = [
            [
                [
                    'key' => 'social-name',
                    'value' => 'Facebook',
                ],
                [
                    'key' => 'social-icon',
                    'value' => 'fab fa-facebook-f',
                ],
                [
                    'key' => 'social-url',
                    'value' => 'https://www.facebook.com/',
                ],
            ],
            [
                [
                    'key' => 'social-name',
                    'value' => 'Twitter',
                ],
                [
                    'key' => 'social-icon',
                    'value' => 'fab fa-twitter',
                ],
                [
                    'key' => 'social-url',
                    'value' => 'https://www.twitter.com/',
                ],
            ],
            [
                [
                    'key' => 'social-name',
                    'value' => 'Youtube',
                ],
                [
                    'key' => 'social-icon',
                    'value' => 'fab fa-youtube',
                ],
                [
                    'key' => 'social-url',
                    'value' => 'https://www.youtube.com/',
                ],
            ],
            [
                [
                    'key' => 'social-name',
                    'value' => 'Behance',
                ],
                [
                    'key' => 'social-icon',
                    'value' => 'fab fa-behance',
                ],
                [
                    'key' => 'social-url',
                    'value' => 'https://www.behance.com/',
                ],
            ],
            [
                [
                    'key' => 'social-name',
                    'value' => 'Linkedin',
                ],
                [
                    'key' => 'social-icon',
                    'value' => 'fab fa-linkedin',
                ],
                [
                    'key' => 'social-url',
                    'value' => 'https://www.linkedin.com/',
                ],
            ],
        ];

        SettingModel::query()->insertOrIgnore([
            'key' => 'theme-' . $theme . '-social_links',
            'value' => json_encode($socialLinks),
        ]);

        SettingModel::query()->insertOrIgnore([
            [
                'key' => 'theme-' . $theme . '-vi-primary_font',
                'value' => 'Roboto Condensed',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-copyright',
                'value' => '© ' . now()->format('Y') . ' Miranda. Tất cả quyền đã được bảo hộ.',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-cookie_consent_message',
                'value' => 'Trải nghiệm của bạn trên trang web này sẽ được cải thiện bằng cách cho phép cookie ',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-cookie_consent_learn_more_url',
                'value' => url('cookie-policy'),
            ],
            [
                'key' => 'theme-' . $theme . '-vi-cookie_consent_learn_more_text',
                'value' => 'Chính sách cookie',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-homepage_id',
                'value' => '1',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-blog_page_id',
                'value' => '2',
            ],
        ]);

        SettingModel::query()->where('key', 'LIKE', 'theme-' . $theme . '-slider-%')->delete();

        SettingModel::query()->insertOrIgnore([
            [
                'key' => 'theme-' . $theme . '-slider-image-1',
                'value' => 'sliders/04.jpg',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-title-1',
                'value' => 'The ultimate luxury experience',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-description-1',
                'value' => '<p>The Perfect<br>Base For You</p>',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-primary-button-text-1',
                'value' => 'Take A tour',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-primary-button-url-1',
                'value' => '/rooms',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-secondary-button-text-1',
                'value' => 'Learn more',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-secondary-button-url-1',
                'value' => '/about-us',
            ],

            [
                'key' => 'theme-' . $theme . '-slider-image-2',
                'value' => 'sliders/05.jpg',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-title-2',
                'value' => 'The ultimate luxury experience',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-description-2',
                'value' => '<p>The Perfect<br>Base For You</p>',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-primary-button-text-2',
                'value' => 'Take A tour',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-primary-button-url-2',
                'value' => '/rooms',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-secondary-button-text-2',
                'value' => 'Learn more',
            ],
            [
                'key' => 'theme-' . $theme . '-slider-secondary-button-url-2',
                'value' => '/about-us',
            ],
        ]);

        SettingModel::query()->where('key', 'LIKE', 'theme-' . $theme . '-vi-slider-%')->delete();

        SettingModel::query()->insertOrIgnore([
            [
                'key' => 'theme-' . $theme . '-vi-slider-image-1',
                'value' => 'sliders/04.jpg',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-title-1',
                'value' => 'Trải nghiệm sang trọng cuối cùng',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-description-1',
                'value' => '<p>Nơi <br> hoàn hảo cho bạn </p>',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-primary-button-text-1',
                'value' => 'Tham quan',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-primary-button-url-1',
                'value' => '/rooms',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-secondary-button-text-1',
                'value' => 'Tìm hiểu thêm',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-secondary-button-url-1',
                'value' => '/about-us',
            ],

            [
                'key' => 'theme-' . $theme . '-vi-slider-image-2',
                'value' => 'sliders/05.jpg',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-title-2',
                'value' => 'Trải nghiệm sang trọng cuối cùng',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-description-2',
                'value' => '<p>Nơi <br> hoàn hảo cho bạn </p>',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-primary-button-text-2',
                'value' => 'Tham quan',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-primary-button-url-2',
                'value' => '/rooms',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-secondary-button-text-2',
                'value' => 'Tìm hiểu thêm',
            ],
            [
                'key' => 'theme-' . $theme . '-vi-slider-secondary-button-url-2',
                'value' => '/about-us',
            ],
        ]);
    }
}
