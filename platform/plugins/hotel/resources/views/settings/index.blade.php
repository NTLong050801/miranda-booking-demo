@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['url' => route('hotel.settings'), 'class' => 'main-setting-form']) !!}
    <div class="max-width-1200">

        <x-core-setting::section
            :title="trans('plugins/hotel::currency.currencies')"
            :description="trans('plugins/hotel::currency.setting_description')"
        >
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="hotel_enable_auto_detect_visitor_currency">{{ trans('plugins/hotel::currency.enable_auto_detect_visitor_currency') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="hotel_enable_auto_detect_visitor_currency"
                           value="1"
                           @if (setting('hotel_enable_auto_detect_visitor_currency', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="hotel_enable_auto_detect_visitor_currency"
                           value="0"
                           @if (setting('hotel_enable_auto_detect_visitor_currency', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="hotel_add_space_between_price_and_currency">{{ trans('plugins/hotel::currency.add_space_between_price_and_currency') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="hotel_add_space_between_price_and_currency"
                           value="1"
                           @if (setting('hotel_add_space_between_price_and_currency', 0) == 1) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="hotel_add_space_between_price_and_currency"
                           value="0"
                           @if (setting('hotel_add_space_between_price_and_currency', 0) == 0) checked @endif>{{ trans('core/setting::setting.general.no') }}
                </label>
            </div>
            <div class="form-group row">
                <div class="col-sm-6">
                    <label class="text-title-field" for="hotel_thousands_separator">{{ trans('plugins/hotel::currency.thousands_separator') }}</label>
                    <div class="ui-select-wrapper">
                        <select class="ui-select" name="hotel_thousands_separator" id="hotel_thousands_separator">
                            <option value="," @if (setting('hotel_thousands_separator', ',') == ',') selected @endif>{{ trans('plugins/hotel::currency.separator_comma') }}</option>
                            <option value="." @if (setting('hotel_thousands_separator', ',') == '.') selected @endif>{{ trans('plugins/hotel::currency.separator_period') }}</option>
                            <option value="space" @if (setting('hotel_thousands_separator', ',') == 'space') selected @endif>{{ trans('plugins/hotel::currency.separator_space') }}</option>
                        </select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                        </svg>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="text-title-field" for="hotel_decimal_separator">{{ trans('plugins/hotel::currency.decimal_separator') }}</label>
                    <div class="ui-select-wrapper">
                        <select class="ui-select" name="hotel_decimal_separator" id="hotel_decimal_separator">
                            <option value="." @if (setting('hotel_decimal_separator', '.') == '.') selected @endif>{{ trans('plugins/hotel::currency.separator_period') }}</option>
                            <option value="," @if (setting('hotel_decimal_separator', '.') == ',') selected @endif>{{ trans('plugins/hotel::currency.separator_comma') }}</option>
                            <option value="space" @if (setting('hotel_decimal_separator', '.') == 'space') selected @endif>{{ trans('plugins/hotel::currency.separator_space') }}</option>
                        </select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                        </svg>
                    </div>
                </div>
            </div>

        <textarea name="currencies"
                  id="currencies"
                  class="hidden">{!! json_encode($currencies) !!}</textarea>
            <textarea name="deleted_currencies"
                      id="deleted_currencies"
                      class="hidden"></textarea>
            <div class="swatches-container">
                <div class="header clearfix">
                    <div class="swatch-item">
                        {{ trans('plugins/hotel::currency.name') }}
                    </div>
                    <div class="swatch-item">
                        {{ trans('plugins/hotel::currency.symbol') }}
                    </div>
                    <div class="swatch-item swatch-decimals">
                        {{ trans('plugins/hotel::currency.number_of_decimals') }}
                    </div>
                    <div class="swatch-item swatch-exchange-rate">
                        {{ trans('plugins/hotel::currency.exchange_rate') }}
                    </div>
                    <div class="swatch-item swatch-is-prefix-symbol">
                        {{ trans('plugins/hotel::currency.is_prefix_symbol') }}
                    </div>
                    <div class="swatch-is-default">
                        {{ trans('plugins/hotel::currency.is_default') }}
                    </div>
                    <div class="remove-item">{{ trans('plugins/hotel::currency.remove') }}</div>
                </div>
                <ul class="swatches-list">

                </ul>
                <div class="clearfix"></div>
                {!! Form::helper(trans('plugins/hotel::currency.instruction')) !!}
                <a href="#" class="js-add-new-attribute">
                    {{ trans('plugins/hotel::currency.new_currency') }}
                </a>
            </div>
        </x-core-setting::section>

        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('plugins/hotel::currency.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/hotel::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/hotel::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush
