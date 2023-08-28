@if ($booking)
    <div class="row">
        <div class="col-md-6">
            <p>{{ __('Time') }}: <i>{{ $booking->created_at }}</i></p>
            <p>{{ __('Full Name') }}: <i>{{ $booking->address->first_name }} {{ $booking->address->last_name }}</i></p>
            <p>{{ __('Email') }}: <i><a href="mailto:{{ $booking->address->email }}">{{ $booking->address->email }}</a></i></p>
            <p>{{ __('Phone') }}: <i>@if ($booking->address->phone) <a href="tel:{{ $booking->address->phone }}">{{ $booking->address->phone }}</a> @else N/A @endif</i></p>
            <p>{{ __('Address') }}: <i>{{ $booking->address->id ? ($booking->address->address ? $booking->address->address . ', ': null) . ($booking->address->city ? $booking->address->city . ', ': null) . ($booking->address->state ? $booking->address->state . ', ' : null) . ($booking->address->country ? $booking->address->country . ', ' : null) . $booking->address->zip : null }}</i></p>
        </div>
        <div class="col-md-6">
            <p>{{ __('Room') }}: <i>@if ($booking->room->room->id) <a href="{{ $booking->room->room->url }}" target="_blank">{{ $booking->room->room->name }}</a> @else N/A @endif</i></p>
            <p><strong>{{ __('Start Date') }}</strong>: <i>{{ $booking->room->start_date }}</i></p>
            <p><strong>{{ __('End Date') }}</strong>: <i>{{ $booking->room->end_date }}</i></p>
            <p><strong>{{ __('Arrival Time') }}</strong>: <i>{{ $booking->arrival_time }}</i></p>
            @if ($booking->requests)
                <p><strong>{{ __('Requests') }}</strong>: <i class="text-warning">{{ $booking->requests }}</i></p>
            @endif
            @if ($booking->number_of_guests)
                <p><strong>{{ __('Number of guests') }}</strong>: <span>{{ $booking->number_of_guests }}</span></p>
            @endif
        </div>
    </div>
    <br>
    <p><strong>{{ __('Room') }}</strong>:</p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center">{{ __('Image') }}</th>
            <th>{{ __('Name') }}</th>
            <th class="text-center">{{ __('Checkin Date') }}</th>
            <th class="text-center">{{ __('Checkout Date') }}</th>
            <th class="text-center">{{ __('Number of rooms') }}</th>
            <th class="text-center">{{ __('Price') }}</th>
            <th class="text-center">{{ __('Tax') }}</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="width: 150px; vertical-align: middle">
                    <a href="{{ $booking->room->room->url }}" target="_blank">
                        <img src="{{ RvMedia::getImageUrl($booking->room->room->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $booking->room->room->name }}" width="140">
                    </a>
                </td>
                <td style="vertical-align: middle"><a href="{{ $booking->room->room->url }}" target="_blank">{{ $booking->room->room->name }}</a></td>
                <td class="text-center" style="vertical-align: middle">{{ $booking->room->start_date }}</td>
                <td class="text-center" style="vertical-align: middle">{{ $booking->room->end_date }}</td>
                <td class="text-center" style="vertical-align: middle">{{ $booking->room->number_of_rooms }}</td>
                <td class="text-center" style="vertical-align: middle"><strong>{{ format_price($booking->room->price) }}</strong></td>
                <td class="text-center" style="vertical-align: middle"><strong>{{ format_price($booking->tax_amount) }}</strong></td>
            </tr>
        </tbody>
    </table>
    <br>
    @if ($booking->services->count())
        <p><strong>{{ __('Services') }}</strong>:</p>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th class="text-center">{{ __('Price') }}</th>
                <th class="text-center">{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($booking->services->unique() as $service)
                <tr>
                    <td style="vertical-align: middle">
                        {{ $service->name }}
                    </td>
                    <td class="text-center" style="vertical-align: middle">{{ format_price($service->price) }} x {{ $booking->room->number_of_rooms }}</td>
                    <td class="text-center" style="vertical-align: middle">{{ format_price($service->price * $booking->room->number_of_rooms) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br>
    @endif
    <br>
    <p><strong>{{ __('Total Amount') }}</strong>: <span class="text-danger">{{ format_price($booking->amount) }}</span></p>
    @if ($booking->payment->id)
        @if (auth()->check())
            <p><strong>{{ __('Payment ID') }}</strong>: <a href="{{ route('payment.show', $booking->payment->id) }}" target="_blank">{{ $booking->payment->charge_id }} <i class="fas fa-external-link-alt"></i></a></p>
        @endif
        <p><strong>{{ __('Payment method') }}</strong>: {{ $booking->payment->payment_channel->label() }}</p>
        <p><strong>{{ __('Payment status') }}</strong>: {!! $booking->status->toHtml() !!}</p>

        @if (($booking->payment->payment_channel == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER && $booking->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING))
            <p><strong>{{ __('Payment info') }}</strong>: <span class="text-warning">{!! BaseHelper::clean(get_payment_setting('description', $booking->payment->payment_channel)) !!}</span></p>
        @endif
    @endif
@endif
