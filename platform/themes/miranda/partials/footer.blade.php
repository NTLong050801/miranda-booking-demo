        <!--====== Back to Top ======-->
        <a href="#" class="back-to-top" id="backToTop">
            <i class="fal fa-angle-double-up"></i>
        </a>
        <!--====== FOOTER START ======-->
        <footer class="footer-two">
            <div class="footer-widget-area pt-100 pb-50">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 order-1">
                            <!-- Site Info Widget -->
                            <div class="widget site-info-widget mb-50">
                                <div class="footer-logo mb-50">
                                    <img src="{{ RvMedia::getImageUrl(theme_option('logo_white')) }}" alt="{{ theme_option('site_title') }}">
                                </div>
                                <p>{{ theme_option('about-us') }}</p>
                                @if (theme_option('social_links'))
                                    <div class="social-links mt-40">
                                        @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                                            @if (count($socialLink) == 3 && $socialLink[1]['value'] && $socialLink[2]['value'])
                                                <a href="{{ $socialLink[2]['value'] }}"
                                                   title="{{ $socialLink[0]['value'] }}" target="_blank">
                                                    <i class="{{ $socialLink[1]['value'] }}"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 order-3 order-lg-2">
                            <!-- Nav Widget -->
                            {!! dynamic_sidebar('footer_sidebar') !!}
                        </div>
                        <div class="col-lg-3 col-sm-6 order-2 order-lg-3">
                            <!-- Contact Widget -->
                            <div class="widget contact-widget mb-50">
                                <h4 class="widget-title">{{ __('Contact Us') }}.</h4>
                                <div class="contact-lists">
                                    <div class="contact-box">
                                        <div class="icon">
                                            <i class="flaticon-call"></i>
                                        </div>
                                        <div class="desc">
                                            <h6 class="title">{{ __('Phone Number') }}</h6>
                                            {{ theme_option('hotline') }}
                                        </div>
                                    </div>
                                    <div class="contact-box">
                                        <div class="icon">
                                            <i class="flaticon-message"></i>
                                        </div>
                                        <div class="desc">
                                            <h6 class="title">{{ __('Email Address') }}</h6>
                                            <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                                        </div>
                                    </div>
                                    <div class="contact-box">
                                        <div class="icon">
                                            <i class="flaticon-location-pin"></i>
                                        </div>
                                        <div class="desc">
                                            <h6 class="title">{{ __('Office Address') }}</h6>
                                            {{ theme_option('address') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-area pt-30 pb-30">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-5 order-2 order-md-1">
                            <p class="copyright-text copyright-two">{{ theme_option('copyright') }}</p>
                        </div>
                        <div class="col-lg-6 col-md-7 order-1 order-md-2">
                            <div class="footer-menu text-center text-md-right">
                                <ul>
                                    @if (theme_option('term_of_use_url'))
                                        <li><a href="{{ theme_option('term_of_use_url') }}">{{ __('Terms of use') }}</a></li>
                                    @endif
                                    @if (theme_option('privacy_policy_url'))
                                        <li><a href="{{ theme_option('privacy_policy_url') }}">{{ __('Privacy Environmental Policy') }}</a></li>
                                    @endif
                                </ul>
                                @if (is_plugin_active('language'))
                                    <div class="language-wrapper">
                                        {!! Theme::partial('language-switcher') !!}
                                    </div>
                                @endif

                                @if (is_plugin_active('hotel'))
                                    @php $currencies = get_all_currencies(); @endphp
                                    @if (count($currencies) > 1)
                                        <div class="language-wrapper choose-currency mr-3">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle btn-select-language" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    {{ get_application_currency()->title }}
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu language_bar_chooser">
                                                    @foreach ($currencies as $currency)
                                                        <li>
                                                            <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--====== FOOTER END ======-->

        {!! Theme::footer() !!}

        @if (session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
            <script type="text/javascript">
                $(document).ready(function () {
                    @if (session()->has('success_msg'))
                    window.showAlert('alert-success', '{{ session('success_msg') }}');
                    @endif

                    @if (session()->has('error_msg'))
                    window.showAlert('alert-danger', '{{ session('error_msg') }}');
                    @endif

                    @if (isset($error_msg))
                    window.showAlert('alert-danger', '{{ $error_msg }}');
                    @endif

                    @if (isset($errors))
                    @foreach ($errors->all() as $error)
                    window.showAlert('alert-danger', '{!! BaseHelper::clean($error) !!}');
                    @endforeach
                    @endif
                });
            </script>
        @endif
    </body>
</html>
