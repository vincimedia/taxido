<footer class="footer-section">
    <div class="top-footer">
        <div class="container">
            <div class="row justify-content-between gy-sm-0 gy-4">
                <div class="col-lg-4 col-md-8 col-sm-7">
                    <div class="logo-box">
                        <a href="#!" class="footer-logo wow fadeInUp">
                            @if(file_exists_public(@$content['footer']['footer_logo']))
                                <img class="img-fluid" alt="footer-logo"
                            src="{{ asset(@$content['footer']['footer_logo']) }}">@endif
                        </a>
                        <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $content['footer']['description'] }}</p>
                    </div>

                    <div class="footer-form wow fadeInUp" data-wow-delay="0.3s">
                        <form method="POST" action="{{ route('newsletter') }}">
                            @csrf
                            <label for="email"
                                class="form-label">{{ $content['footer']['newsletter']['label'] }}</label>
                            <div class="form-group">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="{{ $content['footer']['newsletter']['placeholder'] }}" required>
                                <button type="submit" class="btn gradient-bg-color">
                                    {{ $content['footer']['newsletter']['button_text'] }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <ul class="store-list">
                        <li class="wow fadeInUp" data-wow-delay="0.5s">
                            <a href="{{ $content['footer']['play_store_url'] }}" target="_blank">
                                <img class="img-fluid" alt="store-1" src="{{ asset('front/images/store/1.svg') }}">
                            </a>
                        </li>
                        <li class="wow fadeInUp" data-wow-delay="0.6s">
                            <a href="{{ $content['footer']['app_store_url'] }}" target="_blank">
                                <img class="img-fluid" alt="store-2" src="{{ asset('front/images/store/2.svg') }}">
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 col-sm-4">
                    <div class="footer-content wow fadeInUp" data-wow-delay="0.8s">
                        <div class="footer-title">
                            <h4>Quick Links</h4>
                        </div>
                        <ul class="content-list">
                            @isset($content['footer'])
                                @isset($content['footer']['quick_links'])
                                    @foreach ($content['footer']['quick_links'] as $quickLink)
                                        <li>
                                            <a href="{{ route('home') }}#{{ Str::slug($quickLink) }}">{{ $quickLink }}</a>
                                        </li>
                                    @endforeach
                                @endisset
                            @endisset
                        </ul>
                    </div>
                </div>

                <div class="col-lg-5 position-relative d-none d-lg-block">
                    <div class="footer-image">
                        @if(file_exists_public(@$content['footer']['right_image']))
                            <img class="img-fluid wow fadeInUp" alt="footer-phone"
                                src="{{ asset(@$content['footer']['right_image']) }}" data-wow-delay="0.98s">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sub-footer">
        <div class="container">
            <div class="row gy-md-0 gy-3">
                <div class="col-md-6">
                    <h6>{{ $content['footer']['copyright'] }} {{ date('Y') }}</h6>
                </div>
                <div class="col-md-6">
                    <ul class="social-list">
                        <li>
                            <a href="#!">
                                <i class="ri-facebook-line"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#!">
                                <i class="ri-google-line"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#!">
                                <i class="ri-instagram-line"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#!">
                                <i class="ri-twitter-x-line"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#!">
                                <i class="ri-whatsapp-line"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer end -->
