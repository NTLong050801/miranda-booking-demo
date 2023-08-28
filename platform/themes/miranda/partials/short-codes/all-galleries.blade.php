<section class="places-wrapper">
    <div class="container">
        <div class="places-boxes">
            <div class="section-title text-center mb-50">
                <span class="title-tag">{!! BaseHelper::clean($title) !!}</span>
                <h2>{!! BaseHelper::clean($sub_title ?? $subtitle) !!}</h2>
            </div>
            <div class="row justify-content-center">
                @foreach($galleries as $gallery)
                    <div class="col-lg-4 col-md-4 col-sm-6 col-10">
                        <div class="place-box mt-30">
                            <div class="place-bg-wrap">
                                <div class="place-bg" style="background-image: url({{ RvMedia::getImageUrl($gallery->image, '380x280', false, RvMedia::getDefaultImage()) }});"></div>
                            </div>
                            <div class="desc">
                                <h4><a href="{{ $gallery->url }}">{{ $gallery->name }}</a></h4>
                                <span class="time">{{ $gallery->created_at->translatedFormat('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
