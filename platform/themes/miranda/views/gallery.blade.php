<!--====== BREADCRUMB PART START ======-->
<section class="breadcrumb-area" style="background-image: url({{ theme_option('news_banner') ? RvMedia::getImageUrl(theme_option('news_banner')) : Theme::asset()->url('img/bg/banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-text">
            <h2 class="page-title">{{ $gallery->name }}</h2>

            {!! Theme::partial('breadcrumb') !!}
        </div>
    </div>
</section>
<!--====== BREADCRUMB PART END ======-->

<section class="blog-section contact-part pt-80 pb-80">
    <div class="container">
        <div id="list-photo">
            @foreach (gallery_meta_data($gallery) as $image)
                @if ($image)
                    <div class="item single-gallery-image wow fadeInUp" data-wow-delay=".{{ $loop->index + 3 }}s" data-src="{{ RvMedia::getImageUrl(Arr::get($image, 'img')) }}" data-sub-html="{{ BaseHelper::clean(Arr::get($image, 'description')) }}">
                        <div class="photo-item">
                            <div class="thumb">
                                <a href="{{ BaseHelper::clean(Arr::get($image, 'description')) }}">
                                    <img src="{{ RvMedia::getImageUrl(Arr::get($image, 'img')) }}" alt="{{ BaseHelper::clean(Arr::get($image, 'description')) }}">
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, theme_option('facebook_comment_enabled_in_gallery', 'yes') == 'yes' ? Theme::partial('comments') : null) !!}
    </div>
</section>
