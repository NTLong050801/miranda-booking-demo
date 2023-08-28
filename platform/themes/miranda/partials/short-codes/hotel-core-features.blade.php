<section class="core-feature-section pt-40 pb-40">
    <div class="container">
        <div class="section-title text-center mb-50">
            <span class="title-tag">{!! BaseHelper::clean($title) !!}</span>
            <h2>{!! BaseHelper::clean($sub_title ?: $subtitle) !!}</h2>
        </div>
        <!-- Feature Loop -->
        <div class="row features-loop">
            @php $features->loadMissing('metadata'); @endphp
            @foreach($features as $feature)
                <div class="col-lg-4 col-sm-6 order-1">
                    <div class="feature-box dark-box wow fadeInLeft" data-wow-delay=".3s">
                        <div class="icon">
                            @if ($feature->getMetaData('icon_image', true))
                                <img src="{{ RvMedia::getImageUrl($feature->getMetaData('icon_image', true)) }}" alt="{{ $feature->name }}" height="70">
                            @else
                                <i class="{{ $feature->icon }}"></i>
                            @endif
                        </div>
                        <h3>{{ $feature->name }}</h3>
                        <p>{{ $feature->description }}</p>
                        <span class="count">{{ ($loop->index < 9 ? '0' : '') . ($loop->index + 1) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
