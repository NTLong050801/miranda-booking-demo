<!--====== TESTIMONIAL SLIDER START ======-->
<section class="testimonial-section pb-115 pt-115">
    <div class="container">
        <div class="section-title text-center mb-80">
            <span class="title-tag">{!! BaseHelper::clean($title) !!}</span>
            <h2>{!! BaseHelper::clean($description ?: $subtitle) !!}</h2>
        </div>
        <!-- testimonials loop  -->
        <div class="row testimonial-slider">
            @foreach($testimonials as $testimonial)
                <div class="col-lg-4">
                    <div class="testimonial-box">
                        <div class="client-img">
                            <img src="{{ RvMedia::getImageUrl($testimonial->image)  }}" alt="{{ $testimonial->name }}">
                            <span class="check"><i class="fal fa-check"></i></span>
                        </div>
                        <h3>{{ $testimonial->name }}</h3>
                        <span class="clinet-post">{{ $testimonial->company }}</span>
                        <p>{!! BaseHelper::clean($testimonial->content) !!}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!--====== TESTIMONIAL SLIDER END ======-->
