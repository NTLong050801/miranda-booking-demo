<!--====== CALL TO ACTION END ======-->
<section class="cta-section pt-115 pb-160">
    <div class="container">
        <div class="cta-inner">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-8 col-sm-9 col-10 order-2 order-lg-1">
                    <div class="cta-text">
                        <div class="section-title mb-20">
                            <h2>{!! BaseHelper::clean($title) !!}</h2>
                        </div>
                        <p>{!! BaseHelper::clean($description ?: $subtitle) !!}</p>
                        @if ($button_text)
                            <a href="{{ $button_url }}" class="main-btn btn-filled">{{ $button_text }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 col-md-10 col-sm-11 col-10 order-1 order-lg-2">
                    <!-- feature loop -->
                    <div class="cta-features">
                        @php $features->loadMissing('metadata'); @endphp
                        @foreach($features as $feature)
                            <!-- feature box -->
                            <div class="single-feature wow fadeInUp" data-wow-delay=".3s">
                                <div class="icon">
                                    @if ($feature->getMetaData('icon_image', true))
                                        <img src="{{ RvMedia::getImageUrl($feature->getMetaData('icon_image', true)) }}" alt="{{ $feature->name }}" height="60">
                                    @else
                                        <i class="{{ $feature->icon }}"></i>
                                    @endif
                                </div>
                                <div class="cta-desc">
                                    <h3>{{ $feature->name }}</h3>
                                    <p>{{ $feature->description }}</p>
                                    <span class="count">0{{ $loop->index + 1 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== CALL TO ACTION END ======-->
