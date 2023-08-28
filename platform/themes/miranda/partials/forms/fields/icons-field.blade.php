@php
    Arr::set($attributes, 'class', Arr::get($attributes, 'class') . ' icon-select');
@endphp

{!! Form::customSelect($name, [$value => $value], $value, $attributes) !!}

@once
    @if (request()->ajax())
        <link media="all" type="text/css" rel="stylesheet" href="{{ Theme::asset()->url('css/flaticon.css') }}">
        <link media="all" type="text/css" rel="stylesheet" href="{{ Theme::asset()->url('css/font-awesome.min.css') }}">
        <script src="{{ Theme::asset()->url('js/icons-field.js') }}?v=1.0.0"></script>
    @else
        @push('header')
            <link media="all" type="text/css" rel="stylesheet" href="{{ Theme::asset()->url('css/flaticon.css') }}">
            <link media="all" type="text/css" rel="stylesheet" href="{{ Theme::asset()->url('css/font-awesome.min.css') }}">
            <script src="{{ Theme::asset()->url('js/icons-field.js') }}?v=1.0.0"></script>
        @endpush
    @endif
@endonce
