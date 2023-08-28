@php
    Theme::asset()->usePath()->add('lightGallery-css', 'plugins/lightGallery/css/lightgallery.min.css');
    Theme::asset()->container('footer')->usePath()
        ->add('lightGallery-js', 'plugins/lightGallery/js/lightgallery.min.js', ['jquery']);
@endphp

<!--====== BREADCRUMB PART START ======-->
<section class="breadcrumb-area" style="background-image: url({{ theme_option('rooms_banner') ? RvMedia::getImageUrl(theme_option('rooms_banner')) : Theme::asset()->url('img/bg/banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-text">
            <h2 class="page-title">{{ __('Rooms') }}</h2>

            {!! Theme::partial('breadcrumb') !!}
        </div>
    </div>
</section>
<!--====== BREADCRUMB PART END ======-->

<section class="room-details pt-120 pb-90">
    <div class="container">
        <div class="row">
            <!-- details -->
            <div class="col-lg-8">
                <div class="deatils-box">
                    <div class="title-wrap">
                        <div class="title">
                            <div class="room-cat">{{ $room->category->name }}</div>
                            <h2>{{ $room->name }}</h2>
                        </div>
                        <div class="price">
                            {{ format_price($room->total_price) }}<span>/{{ __('Night') }}</span>
                        </div>
                    </div>
                    <div class="thumb">
                        <div class="room-details-slider">
                            @foreach ($room->images as $img)
                                <a href="{{ RvMedia::getImageUrl($img) }}">
                                    <img src="{{ RvMedia::getImageUrl($img, '770x460') }}" alt="{{ $room->name }}">
                                </a>
                            @endforeach
                        </div>
                        <div class="room-details-slider-nav">
                            @foreach ($room->images as $img)
                                <img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $room->name }}">
                            @endforeach
                        </div>
                    </div>
                    {!! BaseHelper::clean($room->description) !!}

                    @if (count($room->amenities) > 0)
                        <div class="room-fearures clearfix mt-60 mb-60">
                            <h3 class="subtitle">{{ __('Amenities') }}</h3>
                            <ul class="room-fearures-list">
                                @php
                                    $with = ['metadata'];

                                    if (is_plugin_active('language-advanced')) {
                                        $with[] = 'translations';
                                    }

                                    $room->amenities->loadMissing($with);
                                @endphp
                                @foreach($room->amenities as $amenity)
                                    <li>
                                        @if ($amenity->getMetaData('icon_image', true))
                                            <i><img src="{{ RvMedia::getImageUrl($amenity->getMetaData('icon_image', true)) }}" alt="{{ $amenity->name }}" width="18" height="18"></i>
                                        @else
                                            <i class="{{ $amenity->icon }}"></i>
                                        @endif
                                        {{ $amenity->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        {!! BaseHelper::clean($room->content) !!}
                    </div>

                    <div class="room-rules clearfix mt-40 mb-60">
                        <h3 class="subtitle">{{ __('Hotel Rules') }}</h3>
                        <div class="room-rules-list">
                            {!! BaseHelper::clean(theme_option('hotel_rules')) !!}
                        </div>
                    </div>
                    <div class="cancellation-box clearfix mb-60">
                        <h3 class="subtitle">{{ __('Cancellation') }}</h3>
                        {!! BaseHelper::clean(theme_option('cancellation')) !!}
                    </div>
                    <div class="related-room">
                        <h3 class="subtitle">{{ __('Related Rooms') }}</h3>
                        <div class="row room-gird-loop">
                            @foreach($relatedRooms as $relatedRoom)
                                <div class="col-lg-6 col-sm-6">
                                    @include(Theme::getThemeNamespace() . '::views.hotel.includes.room-item', ['room' => $relatedRoom])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- form -->
            <div class="col-lg-4">
                @include(Theme::getThemeNamespace() . '::views.hotel.includes.check-availability', ['room' => $room, 'availableForBooking' => $room->isAvailableAt([
                    'start_date' => request()->query('start_date', now()->format('d-m-Y')),
                    'end_date'   => request()->query('end_date', now()->addDay()->format('d-m-Y')),
                    'adults'     => request()->query('adults', 1),
                ])])
            </div>
        </div>
    </div>
</section>
