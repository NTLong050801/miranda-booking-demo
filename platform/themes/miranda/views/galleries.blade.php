@php
    Theme::layout('no-sidebar');
@endphp

<section class="breadcrumb-area" style="background-image: url({{ theme_option('news_banner') ? RvMedia::getImageUrl(theme_option('news_banner')) : Theme::asset()->url('img/bg/banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-text">
            <h2 class="page-title">{{ __('Gallery') }}</h2>

            {!! Theme::partial('breadcrumb') !!}
        </div>
    </div>
</section>

<section class="blog-section contact-part pt-120 pb-120">
    <div class="container">
        {!! Theme::partial('short-codes.all-galleries', [
            'title' => __('Gallery'),
            'sub_title' => __('Our Rooms'),
            'subtitle' => __('Our Rooms'),
            'galleries' => $galleries,
        ]) !!}
    </div>
</section>
