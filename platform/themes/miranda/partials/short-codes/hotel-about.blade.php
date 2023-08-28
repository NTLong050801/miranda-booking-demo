<!--====== ABOUT PART START ======-->
<section class="about-section pt-115 pb-115">
    <div class="container">
        <div class="down-arrow-wrap text-center">
            <a href="javascript:void(0)" class="down-arrow"><i class="fal fa-arrow-down"></i></a>
        </div>
        <div class="section-title about-title text-center mb-20">
            <span class="title-tag">{!! BaseHelper::clean($title) !!}</span>
            <h2>{!! BaseHelper::clean($description ?: $subtitle) !!}</h2>
        </div>
        <ul class="about-features masonry-layout">
            @for($i = 1; $i <= 5; $i++)
                <li class="wow fadeInUp" data-wow-delay="{{ (0.1 + $i * 0.2) }}s">
                    <a href="{{ ${'block_link_' . $i} }}">
                        <i class="{{ ${'block_icon_' . $i} }}"></i>
                        <i class="hover-icon {{ ${'block_icon_' . $i} }}"></i>
                        <span class="title">{{ ${'block_text_' . $i} }}</span>
                    </a>
                </li>
            @endfor
        </ul>
    </div>
</section>
<!--====== ABOUT PART END ======-->
