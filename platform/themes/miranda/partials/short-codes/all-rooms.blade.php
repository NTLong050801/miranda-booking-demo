<section class="room-section room-grid-style">
    <div class="container">
        <h3>{{ __(':count rooms available', ['count' => $rooms->total()]) }}</h3>
        <br>
        <div class="row">
            <!-- details -->
            <div class="col-lg-8">
                <div class="row justify-content-center room-gird-loop">
                    @foreach($rooms as $room)
                        <div class="col-sm-6">
                            @include(Theme::getThemeNamespace() . '::views.hotel.includes.room-item', compact('room', 'nights'))
                        </div>
                    @endforeach
                </div>
                <div class="pagination-wrap">
                    {!! $rooms->links() !!}
                </div>
            </div>
            <!-- form -->
            <div class="col-lg-4">
                <div class="room-details">
                    @include(Theme::getThemeNamespace() . '::views.hotel.includes.check-availability', ['availableForBooking' => false])
                </div>
            </div>
        </div>
    </div>
</section>
