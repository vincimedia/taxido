@extends('admin.layouts.master')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mobiscroll/mobiscroll.css') }}">
@endpush
@section('title', __('static.dashboard'))
@section('content')
    <div class="row dashboard-default">
        <div class="col-12">
            <div class="default-sorting mt-0">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="welcome-box">
                            <div class="d-flex">
                                <h2>{{ __('static.widgets.hello') }}, {{ getCurrentUser()->name }}</h2>
                                <img src="{{ asset('images/dashboard/hand.gif') }}" alt="">
                            </div>
                            <div class="animation-slides">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <form action="{{ route('admin.dashboard.index') }}" method="GET" id="sort-form">
                            <div class="support-title sorting m-0">
                                <div class="select-sorting">
                                    <label for="">
                                        {{ __('static.sort_by') }}
                                    </label>
                                    <div class="select-form">
                                     
                                        <select class="select-2 form-control sort" id="sort" name="sort">
                                            <option class="select-placeholder" value="today"
                                            {{ request('sort') == 'today' ? 'selected' : '' }}>
                                            {{ __('static.today') }}
                                        </option>
                                        <option class="select-placeholder" value="this_week"
                                        {{ request('sort') == 'this_week' ? 'selected' : '' }}>
                                        {{ __('static.this_week') }}
                                    </option>
                                    <option class="select-placeholder" value="this_month"
                                    {{ request('sort') == 'this_month' ? 'selected' : '' }}>
                                    {{ __('static.this_month') }}
                                </option>
                                <option class="select-placeholder" value="this_year"
                                    {{ request('sort') == 'this_year'  || !request('sort') ? 'selected' : ''}}>
                                    {{ __('static.this_year') }}
                                </option>
                                <option class="select-placeholder" value="custom"
                                                {{ request('sort') == 'custom' ? 'selected' : '' }}>
                                                {{ __('static.custom_range') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group d-none" id="custom-date-range">
                                <input type="text" class="form-control filter-dropdown" id="start_end_date"
                                    name="start_end_date" placeholder="{{ __('taxido::static.reports.select_date') }}">
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        @foreach ($widgets as $widget)
            {!! $widget['callback']($widget['data']) !!}
        @endforeach
    </div>
@endsection

@push('scripts')
    <!-- Chart Js -->
    <script src="{{ asset('js/apex-chart.js') }}"></script>
    <script src="{{ asset('js/custom-apexchart.js') }}"></script>
    <script src="{{ asset('js/mobiscroll/mobiscroll.js') }}"></script>
    <script src="{{ asset('js/mobiscroll/custom-mobiscroll.js') }}"></script>
    <script>
        $(document).ready(function() {
            const filterVal = $('#sort').val();
            if (filterVal === 'custom') {
                $('#custom-date-range').removeClass('d-none');
            } else {
                $('#custom-date-range').addClass('d-none');
            }

            function formatDate(date) {
                const parts = date.split('/');
                if (parts.length === 3) {
                    return `${parts[0]}-${parts[1]}-${parts[2]}`;
                }
                return date;
            }

            $('#start_end_date').on('change', function() {
                const selectedDateRange = $(this).val();
                if (selectedDateRange) {
                    const dateRange = selectedDateRange.split(' - ');
                    if (dateRange.length === 2) {
                        const startDate = formatDate(dateRange[0]);
                        const endDate = formatDate(dateRange[1]);


                        const urlParams = new URLSearchParams(window.location.search);
                        urlParams.set('sort', 'custom');
                        urlParams.set('start', startDate);
                        urlParams.set('end', endDate);


                        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
                    }
                }
            });


            $('#start_end_date').mobiscroll().datepicker({
                controls: ['calendar'],
                select: 'range',
                touchUi: false
            });

            $('#sort').on('change', function() {

                const selectedSort = $(this).val();

                if (selectedSort === 'custom') {
                    $('#custom-date-range').removeClass('d-none');
                } else {
                    window.history.replaceState(null, null, location.pathname);
                    $('#custom-date-range').addClass('d-none');
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('sort', selectedSort);
                    window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
                }
            });

            $(".animation-slides").slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
                vertical: true,
                variableWidth: false,
                autoplay: true,
                autoplaySpeed: 1000,
                arrows: false,
            });


            const additionalSlides = [
                "<p>👑 You’re in control—let’s do this! 💼</p>",
                "<p>🌈 Let’s make today productive and successful! 🏆</p>",
                "<p>🧠 Let’s brainstorm and create something awesome! 💡</p>",
            ];

            additionalSlides.forEach(slide => {
                $(".animation-slides").slick('slickAdd', slide);
            });


        });
    </script>
@endpush
