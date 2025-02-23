<!-- AOS JS -->
<script src="{{ asset('front/js/jquery.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('front/js/bootstrap.js') }}"></script>

<!-- Iconsax JS -->
<script src="{{ asset('front/js/wow.js') }}"></script>

<!-- Swiper JS -->
<script src="{{ asset('front/js/swiper.js') }}"></script>
<script src="{{ asset('front/js/custom-swiper.js') }}"></script>

<!-- Custom Script JS -->
<script src="{{ asset('front/js/script.js') }}"></script>
<script src="{{ asset('front/js/dark-mode.js') }}"></script>
<script>
    new WOW().init();

    $(window).on('load', function () {
        setTimeout(function () {
            $('#fullScreenLoader').fadeOut('slow', function () {
                $(this).remove();
            });
        }, 3500);
    });
</script>


@stack('js')
