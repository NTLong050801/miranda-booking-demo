<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\Page\Models\Page;
use Botble\Slug\Models\Slug;
use Botble\Base\Facades\Html;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class PageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $pages = [
            [
                'name' => 'Homepage',
                'content' =>
                    Html::tag('div', '[home-banner][/home-banner]') .
                    Html::tag('div', '[check-availability-form][/check-availability-form]') .
                    Html::tag(
                        'div',
                        '[hotel-about title="since 1994" subtitle="Situated In Prime Position At The Foot Of The Slopes Of Courchevel Moriond." block_icon_1="flaticon-coffee" block_text_1="Breakfast" block_link_1="#" block_icon_2="flaticon-air-freight" block_text_2="Airport Pickup" block_link_2="#" block_icon_3="flaticon-marker" block_text_3="City Guide" block_link_3="#" block_icon_4="flaticon-barbecue" block_text_4="BBQ Party" block_link_4="#" block_icon_5="flaticon-hotel" block_text_5="Luxury Room" block_link_5="#"][/hotel-about]'
                    ) .
                    Html::tag(
                        'div',
                        '[room-categories title="Room Type" subtitle="Inspired Loding" background_image="general/bg.jpg"][/room-categories]'
                    ) .
                    Html::tag(
                        'div',
                        '[hotel-featured-features title="The Thin Escape" subtitle="Miranda has everything for your trip & every single things." button_text="Take a tour" button_url="/rooms"][/hotel-featured-features]'
                    ) .
                    Html::tag('div', '[rooms][/rooms]') .
                    Html::tag(
                        'div',
                        '[video-introduction title="Take a tour" subtitle="Discover Our Underground." content="Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat." background_image="general/video-background-02.jpg" video_image="general/video-banner-01.jpg" video_url="https://www.youtube.com/watch?v=EEJFMdfraVY" button_text="Book Now" button_url="/rooms"][/video-introduction]'
                    ) .
                    Html::tag('div', '[testimonial title="testimonials" subtitle="Client Feedback"][/testimonial]') .
                    Html::tag(
                        'div',
                        '[rooms-introduction title="Our rooms" subtitle="Each of our nine lovely double guest rooms feature a private bath, wi-fi, cable television and include full breakfast." background_image="general/bg.jpg" first_image="general/01.jpg" second_image="general/02.jpg" third_image="general/03.jpg" button_text="Take a tour" button_url="/rooms"][/rooms-introduction]'
                    ) .
                    Html::tag('div', '[featured-news title="Blog" subtitle="News Feeds"][/featured-news]')
                ,
                'template' => 'homepage',
            ],
            [
                'name' => 'News',
                'content' => Html::tag('p', '--'),
            ],
            [
                'name' => 'Contact',
                'content' => Html::tag('div', '[contact-info][/contact-info]') . Html::tag(
                    'div',
                    '[google-map]19/A, Cirikon City hall Tower New York, NYC[/google-map]'
                ) . Html::tag(
                    'div',
                    '[contact-form][/contact-form]'
                ),
                'template' => 'no-sidebar',
            ],
            [
                'name' => 'Restaurant',
                'content' => Html::tag('div', '[food-types][/food-types]') . Html::tag(
                    'div',
                    '[foods title="Restaurant" subtitle="Trending Menu"][/foods]'
                ),
                'template' => 'no-sidebar',
            ],
            [
                'name' => 'Our Gallery',
                'content' => Html::tag('div', '[all-galleries title="Gallery" subtitle="Our Rooms"][/all-galleries]'),
                'template' => 'no-sidebar',
            ],
            [
                'name' => 'About us',
                'content' => Html::tag(
                    'div',
                    '[youtube-video url="https://www.youtube.com/watch?v=EEJFMdfraVY" background_image="general/04.jpg"][/youtube-video]'
                ) .
                    Html::tag(
                        'p',
                        'Hello. Our hotel has been present for over 20 years. We make the best or all our customers. Hello. Our hotel has been present for over 20 years. We make the best or all our customers. Hello. Our hotel has been present for over 20 years. We make the best or all our customers.'
                    ) .
                    Html::tag(
                        'div',
                        '[hotel-core-features title="Facilities" subtitle="Core Features"][/hotel-core-features]'
                    ) .
                    Html::tag('div', '[featured-news title="Blog" subtitle="News Feeds"][/featured-news]')
                ,
                'template' => 'no-sidebar',
            ],
            [
                'name' => 'Places',
                'content' => Html::tag('div', '[places][/places]'),
                'template' => 'no-sidebar',
            ],
            [
                'name' => 'Our Offers',
                'content' => Html::tag('div', '[our-offers][/our-offers]')
                ,
                'template' => 'no-sidebar',
            ],
            [
                'name' => 'Cookie Policy',
                'content' => Html::tag('h3', 'EU Cookie Consent') .
                    Html::tag(
                        'p',
                        'To use this website we are using Cookies and collecting some data. To be compliant with the EU GDPR we give you to choose if you allow us to use certain Cookies and to collect some Data.'
                    ) .
                    Html::tag('h4', 'Essential Data') .
                    Html::tag(
                        'p',
                        'The Essential Data is needed to run the Site you are visiting technically. You can not deactivate them.'
                    ) .
                    Html::tag(
                        'p',
                        '- Session Cookie: PHP uses a Cookie to identify user sessions. Without this Cookie the Website is not working.'
                    ) .
                    Html::tag(
                        'p',
                        '- XSRF-Token Cookie: Laravel automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the one actually making the requests to the application.'
                    ),
            ],
        ];

        Page::query()->truncate();
        DB::table('pages_translations')->truncate();
        Slug::query()->where('reference_type', Page::class)->delete();
        MetaBoxModel::query()->where('reference_type', Page::class)->delete();
        LanguageMeta::query()->where('reference_type', Page::class)->delete();

        foreach ($pages as $item) {
            $item['user_id'] = 1;

            if (! isset($item['template'])) {
                $item['template'] = 'default';
            }

            $page = Page::query()->create($item);

            Slug::query()->create([
                'reference_type' => Page::class,
                'reference_id' => $page->id,
                'key' => Str::slug($page->name),
                'prefix' => SlugHelper::getPrefix(Page::class),
            ]);
        }

        $translations = [
            [
                'name' => 'Homepage',
                'content' =>
                    Html::tag('div', '[home-banner][/home-banner]') .
                    Html::tag('div', '[check-availability-form][/check-availability-form]') .
                    Html::tag(
                        'div',
                        '[hotel-about title="since 1994" subtitle="Situated In Prime Position At The Foot Of The Slopes Of Courchevel Moriond." block_icon_1="flaticon-coffee" block_text_1="Breakfast" block_link_1="#" block_icon_2="flaticon-air-freight" block_text_2="Airport Pickup" block_link_2="#" block_icon_3="flaticon-marker" block_text_3="City Guide" block_link_3="#" block_icon_4="flaticon-barbecue" block_text_4="BBQ Party" block_link_4="#" block_icon_5="flaticon-hotel" block_text_5="Luxury Room" block_link_5="#"][/hotel-about]'
                    ) .
                    Html::tag(
                        'div',
                        '[room-categories title="Room Type" subtitle="Inspired Loding" background_image="general/bg.jpg"][/room-categories]'
                    ) .
                    Html::tag(
                        'div',
                        '[hotel-featured-features title="The Thin Escape" subtitle="Miranda has everything for your trip & every single things." button_text="Take a tour" button_url="/rooms"][/hotel-featured-features]'
                    ) .
                    Html::tag('div', '[rooms][/rooms]') .
                    Html::tag(
                        'div',
                        '[video-introduction title="Take a tour" subtitle="Discover Our Underground." content="Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat." background_image="general/video-background-02.jpg" video_image="general/video-banner-01.jpg" video_url="https://www.youtube.com/watch?v=EEJFMdfraVY" button_text="Book Now" button_url="/rooms"][/video-introduction]'
                    ) .
                    Html::tag('div', '[testimonial title="testimonials" subtitle="Client Feedback"][/testimonial]') .
                    Html::tag(
                        'div',
                        '[rooms-introduction title="Our rooms" subtitle="Each of our nine lovely double guest rooms feature a private bath, wi-fi, cable television and include full breakfast." background_image="general/bg.jpg" first_image="general/01.jpg" second_image="general/02.jpg" third_image="general/03.jpg" button_text="Take a tour" button_url="/rooms"][/rooms-introduction]'
                    ) .
                    Html::tag('div', '[featured-news title="Blog" subtitle="News Feeds"][/featured-news]')
                ,
            ],
            [
                'name' => 'Tin tức',
                'content' => Html::tag('p', '--'),
            ],
            [
                'name' => 'Liên hệ',
                'content' => Html::tag('div', '[contact-info][/contact-info]') . Html::tag(
                    'div',
                    '[google-map]19/A, Cirikon City hall Tower New York, NYC[/google-map]'
                ) . Html::tag(
                    'div',
                    '[contact-form][/contact-form]'
                ),
            ],
            [
                'name' => 'Nhà hàng',
                'content' => Html::tag('div', '[food-types][/food-types]') . Html::tag(
                    'div',
                    '[foods title="Restaurant" subtitle="Trending Menu"][/foods]'
                ),
            ],
            [
                'name' => 'Thư viện ảnh',
                'content' => Html::tag('div', '[all-galleries title="Gallery" subtitle="Our Rooms"][/all-galleries]'),
            ],
            [
                'name' => 'Về chúng tôi',
                'content' => Html::tag(
                    'div',
                    '[youtube-video url="https://www.youtube.com/watch?v=EEJFMdfraVY" background_image="general/04.jpg"][/youtube-video]'
                ) .
                    Html::tag(
                        'p',
                        'Hello. Our hotel has been present for over 20 years. We make the best or all our customers. Hello. Our hotel has been present for over 20 years. We make the best or all our customers. Hello. Our hotel has been present for over 20 years. We make the best or all our customers.'
                    ) .
                    Html::tag(
                        'div',
                        '[hotel-core-features title="Facilities" subtitle="Core Features"][/hotel-core-features]'
                    ) .
                    Html::tag('div', '[featured-news title="Blog" subtitle="News Feeds"][/featured-news]')
                ,
            ],
            [
                'name' => 'Địa điểm',
                'content' => Html::tag('div', '[places][/places]'),
            ],
            [
                'name' => 'Khuyến mãi',
                'content' => Html::tag('div', '[our-offers][/our-offers]')
                ,
            ],
            [
                'name' => 'Chính sách cookie',
                'content' => Html::tag('h3', 'EU Cookie Consent') .
                    Html::tag(
                        'p',
                        'To use this website we are using Cookies and collecting some data. To be compliant with the EU GDPR we give you to choose if you allow us to use certain Cookies and to collect some Data.'
                    ) .
                    Html::tag('h4', 'Essential Data') .
                    Html::tag(
                        'p',
                        'The Essential Data is needed to run the Site you are visiting technically. You can not deactivate them.'
                    ) .
                    Html::tag(
                        'p',
                        '- Session Cookie: PHP uses a Cookie to identify user sessions. Without this Cookie the Website is not working.'
                    ) .
                    Html::tag(
                        'p',
                        '- XSRF-Token Cookie: Laravel automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the one actually making the requests to the application.'
                    ),
            ],
        ];

        foreach ($translations as $index => $item) {
            $item['lang_code'] = 'vi';
            $item['pages_id'] = $index + 1;

            DB::table('pages_translations')->insert($item);
        }
    }
}
