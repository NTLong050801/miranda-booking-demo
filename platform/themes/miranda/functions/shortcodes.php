<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\Hotel\Repositories\Interfaces\FeatureInterface;
use Botble\Hotel\Repositories\Interfaces\FoodInterface;
use Botble\Hotel\Repositories\Interfaces\FoodTypeInterface;
use Botble\Hotel\Repositories\Interfaces\PlaceInterface;
use Botble\Hotel\Repositories\Interfaces\RoomCategoryInterface;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Testimonial\Repositories\Interfaces\TestimonialInterface;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Supports\Youtube;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;

app()->booted(function () {
    ThemeSupport::registerGoogleMapsShortcode();
    ThemeSupport::registerYoutubeShortcode();

    add_shortcode('youtube-video', __('Youtube video'), __('Add youtube video'), function ($shortCode) {
        Theme::asset()->usePath()->add('magnific-popup-css', 'css/magnific-popup.css');
        Theme::asset()->container('footer')->usePath()->add('jquery.magnific-popup', 'js/jquery.magnific-popup.min.js');

        return Theme::partial('short-codes.video', [
            'url' => Youtube::getYoutubeWatchURL($shortCode->url),
            'background_image' => $shortCode->background_image,
        ]);
    });

    if (is_plugin_active('testimonial')) {
        add_shortcode('testimonial', __('Testimonial'), __('Testimonial'), function ($shortCode) {
            $testimonials = app(TestimonialInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

            return Theme::partial('short-codes.testimonial', [
                'title' => $shortCode->title,
                'description' => $shortCode->description,
                'subtitle' => $shortCode->subtitle,
                'testimonials' => $testimonials,
            ]);
        });

        shortcode()->setAdminConfig('testimonial', function ($attributes) {
            return Theme::partial('short-codes.testimonial-admin-config', compact('attributes'));
        });
    }

    if (is_plugin_active('blog')) {
        add_shortcode('featured-news', __('Featured News'), __('Featured News'), function ($shortCode) {
            $posts = get_featured_posts(6, ['author']);

            return Theme::partial('short-codes.featured-news', [
                'title' => $shortCode->title,
                'subtitle' => $shortCode->subtitle,
                'description' => $shortCode->description,
                'posts' => $posts,
            ]);
        });

        shortcode()->setAdminConfig('featured-news', function ($attributes) {
            return Theme::partial('short-codes.featured-news-admin-config', compact('attributes'));
        });
    }

    add_shortcode('video-introduction', __('Video Introduction'), __('Video Introduction'), function ($shortCode) {
        Theme::asset()->usePath()->add('magnific-popup-css', 'css/magnific-popup.css');
        Theme::asset()->container('footer')->usePath()->add('jquery.magnific-popup', 'js/jquery.magnific-popup.min.js');

        return Theme::partial('short-codes.video-introduction', [
            'title' => $shortCode->title,
            'sub_title' => $shortCode->sub_title,
            'subtitle' => $shortCode->subtitle,
            'description' => $shortCode->description,
            'content' => $shortCode->content,
            'background_image' => $shortCode->background_image,
            'video_image' => $shortCode->video_image,
            'video_url' => $shortCode->video_url,
            'button_text' => $shortCode->button_text,
            'button_url' => $shortCode->button_url,
        ]);
    });

    shortcode()->setAdminConfig('video-introduction', function ($attributes) {
        return Theme::partial('short-codes.video-introduction-admin-config', compact('attributes'));
    });

    add_shortcode('rooms-introduction', __('Rooms Introduction'), __('Rooms Introduction'), function ($shortCode) {
        return Theme::partial('short-codes.rooms-introduction', [
            'title' => $shortCode->title,
            'description' => $shortCode->description,
            'subtitle' => $shortCode->subtitle,
            'background_image' => $shortCode->background_image,
            'first_image' => $shortCode->first_image,
            'second_image' => $shortCode->second_image,
            'third_image' => $shortCode->third_image,
            'button_text' => $shortCode->button_text,
            'button_url' => $shortCode->button_url,
        ]);
    });

    shortcode()->setAdminConfig('rooms-introduction', function ($attributes) {
        return Theme::partial('short-codes.rooms-introduction-admin-config', compact('attributes'));
    });

    add_shortcode('hotel-about', __('Hotel About'), __('Hotel About'), function ($shortCode) {
        return Theme::partial('short-codes.hotel-about', [
            'title' => $shortCode->title,
            'description' => $shortCode->description,
            'subtitle' => $shortCode->subtitle,
            'block_icon_1' => $shortCode->block_icon_1,
            'block_text_1' => $shortCode->block_text_1,
            'block_link_1' => $shortCode->block_link_1,
            'block_icon_2' => $shortCode->block_icon_2,
            'block_text_2' => $shortCode->block_text_2,
            'block_link_2' => $shortCode->block_link_2,
            'block_icon_3' => $shortCode->block_icon_3,
            'block_text_3' => $shortCode->block_text_3,
            'block_link_3' => $shortCode->block_link_3,
            'block_icon_4' => $shortCode->block_icon_4,
            'block_text_4' => $shortCode->block_text_4,
            'block_link_4' => $shortCode->block_link_4,
            'block_icon_5' => $shortCode->block_icon_5,
            'block_text_5' => $shortCode->block_text_5,
            'block_link_5' => $shortCode->block_link_5,
        ]);
    });

    shortcode()->setAdminConfig('hotel-about', function ($attributes) {
        return Theme::partial('short-codes.hotel-about-admin-config', compact('attributes'));
    });

    add_shortcode('contact-info', __('Contact information'), __('Contact information'), function () {
        return Theme::partial('short-codes.contact-info');
    });

    shortcode()->setAdminConfig('youtube-video', function ($attributes) {
        return Theme::partial('short-codes.video-admin-config', compact('attributes'));
    });

    add_shortcode('home-banner', 'Home Banner', 'Home Banner', function () {
        return Theme::partial('short-codes.home-banner');
    });

    if (is_plugin_active('hotel')) {
        add_shortcode(
            'hotel-featured-features',
            __('Hotel Featured Features'),
            __('Hotel Featured Features'),
            function ($shortCode) {
                $features = app(FeatureInterface::class)->allBy([
                    'status' => BaseStatusEnum::PUBLISHED,
                    'is_featured' => true,
                ]);

                return Theme::partial('short-codes.hotel-featured-features', [
                    'title' => $shortCode->title,
                    'description' => $shortCode->description,
                    'subtitle' => $shortCode->subtitle,
                    'button_text' => $shortCode->button_text,
                    'button_url' => $shortCode->button_url,
                    'features' => $features,
                ]);
            }
        );

        shortcode()->setAdminConfig('hotel-featured-features', function ($attributes) {
            return Theme::partial('short-codes.hotel-featured-features-admin-config', compact('attributes'));
        });

        add_shortcode('rooms', __('Rooms'), __('Rooms'), function () {
            $rooms = app(RoomInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED], ['slugable']);

            return Theme::partial('short-codes.rooms', compact('rooms'));
        });

        add_shortcode('room-categories', __('Room Categories'), __('Room Categories'), function ($shortCode) {
            $categories = app(RoomCategoryInterface::class)->advancedGet([
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                    'is_featured' => 1,
                ],
                'with' => [
                    'rooms' => function ($query) {
                        $query
                            ->latest()
                            ->with(['slugable'])
                            ->where('status', BaseStatusEnum::PUBLISHED);
                    },
                    'rooms.slugable',
                ],
                'order_by' => [
                    'order' => 'ASC',
                ],
            ]);

            return Theme::partial('short-codes.room-categories', [
                'title' => $shortCode->title,
                'sub_title' => $shortCode->sub_title,
                'subtitle' => $shortCode->subtitle,
                'background_image' => $shortCode->background_image,
                'categories' => $categories,
            ]);
        });

        shortcode()->setAdminConfig('room-categories', function ($attributes) {
            return Theme::partial('short-codes.room-categories-admin-config', compact('attributes'));
        });

        add_shortcode('food-types', __('Food Types'), __('Food Types'), function () {
            $foodTypes = app(FoodTypeInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED], ['foods']);
            ;

            return Theme::partial('short-codes.food-types', compact('foodTypes'));
        });

        add_shortcode('foods', __('Foods'), __('Foods'), function ($shortCode) {
            $foods = app(FoodInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED], ['type']);

            return Theme::partial('short-codes.foods', [
                'title' => $shortCode->title,
                'sub_title' => $shortCode->sub_title,
                'subtitle' => $shortCode->subtitle,
                'foods' => $foods,
            ]);
        });

        shortcode()->setAdminConfig('foods', function ($attributes) {
            return Theme::partial('short-codes.foods-admin-config', compact('attributes'));
        });

        add_shortcode(
            'hotel-core-features',
            __('Hotel Core Features'),
            __('Hotel Core Features'),
            function ($shortCode) {
                $features = app(FeatureInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

                return Theme::partial('short-codes.hotel-core-features', [
                    'title' => $shortCode->title,
                    'sub_title' => $shortCode->sub_title,
                    'subtitle' => $shortCode->subtitle,
                    'features' => $features,
                ]);
            }
        );

        shortcode()->setAdminConfig('hotel-featured-features', function ($attributes) {
            return Theme::partial('short-codes.hotel-core-features-admin-config', compact('attributes'));
        });

        add_shortcode(
            'check-availability-form',
            __('Check Availability Form'),
            __('Check Availability Form'),
            function () {
                return Theme::partial('short-codes.check-availability-form');
            }
        );

        $dateFormat = 'Y-m-d';

        add_shortcode('our-offers', __('Our offers'), __('Our offers'), function () use ($dateFormat) {
            $condition = [
                'start_date' => Carbon::now()->format($dateFormat),
                'end_date' => Carbon::now()->addDay()->format($dateFormat),
                'adults' => 1,
            ];

            $allRooms = app(RoomInterface::class)->allBy(
                ['status' => BaseStatusEnum::PUBLISHED, 'is_featured' => true],
                [
                    'slugable',
                    'amenities',
                    'category',
                    'activeBookingRooms' => function ($query) use ($condition, $dateFormat) {
                        return $query
                            ->whereDate('start_date', '<=', date($dateFormat, strtotime($condition['start_date'])))
                            ->whereDate('end_date', '>=', date($dateFormat, strtotime($condition['end_date'])));
                    },
                    'activeRoomDates' => function ($query) use ($condition, $dateFormat) {
                        return $query
                            ->whereDate('start_date', '>=', date($dateFormat, strtotime($condition['start_date'])))
                            ->whereDate('end_date', '<=', date($dateFormat, strtotime($condition['end_date'])))
                            ->take(40);
                    },
                ]
            );

            $nights = 1;

            $rooms = [];
            foreach ($allRooms as $allRoom) {
                if ($allRoom->isAvailableAt($condition)) {
                    $allRoom->total_price = $allRoom->getRoomTotalPrice(
                        $condition['start_date'],
                        $condition['end_date'],
                        $nights
                    );
                    $rooms[] = $allRoom;
                }
            }

            return Theme::partial('short-codes.our-offers', compact('rooms'));
        });

        add_shortcode('all-rooms', __('All Rooms'), __('Display all rooms'), function () use ($dateFormat) {
            try {
                if (Request::input('start_date') && Request::input('end_date')) {
                    $startDate = Carbon::createFromFormat('d-m-Y', Request::input('start_date'));
                    $endDate = Carbon::createFromFormat('d-m-Y', Request::input('end_date'));
                } else {
                    $startDate = Carbon::now();
                    $endDate = Carbon::now()->addDay();
                }
            } catch (Exception) {
                $startDate = Carbon::now();
                $endDate = Carbon::now()->addDay();
            }

            $filters = [
                'keyword' => Request::query('q'),
            ];

            $condition = [
                'start_date' => $startDate->format($dateFormat),
                'end_date' => $endDate->format($dateFormat),
                'adults' => Request::input('adults', 1),
            ];

            $params = [
                'paginate' => [
                    'per_page' => 100,
                    'current_paged' => (int)Request::input('page', 1),
                ],
                'with' => [
                    'amenities',
                    'amenities.metadata',
                    'slugable',
                    'activeBookingRooms' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->where(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '>=', $startDate)
                                    ->whereDate('start_date', '<=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('end_date', '>=', $startDate)
                                    ->whereDate('end_date', '<=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '<=', $startDate)
                                    ->whereDate('end_date', '>=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '>=', $startDate)
                                    ->whereDate('end_date', '<=', $endDate);
                            });
                    },
                    'activeRoomDates' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('start_date', '>=', $startDate)
                            ->whereDate('end_date', '<=', $endDate)
                            ->take(40);
                    },
                ],
            ];

            $queriedRooms = app(RoomInterface::class)->getRooms($filters, $params);

            $nights = $endDate->diffInDays($startDate);

            $rooms = [];

            foreach ($queriedRooms as &$room) {
                if ($room->isAvailableAt($condition)) {
                    $room->total_price = $room->getRoomTotalPrice($condition['start_date'], $condition['end_date'], $nights);

                    $rooms[] = $room;
                }
            }

            $rooms = new LengthAwarePaginator($rooms, count($rooms), 100, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);

            return Theme::partial('short-codes.all-rooms', compact('rooms', 'nights'));
        });
    }

    if (is_plugin_active('gallery')) {
        add_shortcode('all-galleries', __('All Galleries'), __('All Galleries'), function ($shortCode) {
            $galleries = app(GalleryInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED], ['slugable']);

            return Theme::partial('short-codes.all-galleries', [
                'title' => $shortCode->title,
                'sub_title' => $shortCode->sub_title,
                'subtitle' => $shortCode->subtitle,
                'galleries' => $galleries,
            ]);
        });

        shortcode()->setAdminConfig('all-galleries', function ($attributes) {
            return Theme::partial('short-codes.all-galleries-admin-config', compact('attributes'));
        });

        add_shortcode('places', __('Places'), __('Places'), function () {
            $places = app(PlaceInterface::class)->allBy(['status' => BaseStatusEnum::PUBLISHED]);

            return Theme::partial('short-codes.places', [
                'places' => $places,
            ]);
        });
    }

    if (is_plugin_active('contact')) {
        add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
            return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
        }, 120);
    }
});
