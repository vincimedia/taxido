@use('Modules\Taxido\Enums\RideStatusEnum')
@use('Modules\Taxido\Enums\ServiceCategoryEnum')
@use('Modules\Taxido\Enums\ServicesEnum')
@php
    $dateRange = getStartAndEndDate(request('sort'), request('start'), request('end'));

    $start_date = $dateRange['start'] ?? null;
    $end_date = $dateRange['end'] ?? null;

    $intercityRides = getTotalRidesByServiceCategory(ServiceCategoryEnum::INTERCITY, $start_date, $end_date);
    $rideRides = getTotalRidesByServiceCategory(ServiceCategoryEnum::RIDE, $start_date, $end_date);
    $rentalRides = getTotalRidesByServiceCategory(ServiceCategoryEnum::RENTAL, $start_date, $end_date);
    $scheduledRides = getTotalRidesByServiceCategory(ServiceCategoryEnum::SCHEDULE, $start_date, $end_date);
    $packageRides = getTotalRidesByServiceCategory(ServiceCategoryEnum::PACKAGE, $start_date, $end_date);
    $totalRides = getTotalRides($start_date, $end_date);
@endphp

@isset($rideStatusOverview)
    @can('ride.index')
        <div class="col-xxl-9">
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body bg-image">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('images/dashboard/details/request.svg') }}" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <span>{{ __('taxido::static.rides.requested') }}</span>
                                    <h4>{{ getTotalRidesByStatus(RideStatusEnum::REQUESTED, $start_date, $end_date) ?? 0 }}</h4>
                                </div>
                            </div>
                            <a href="{{ route('admin.ride.requested-rides') }}"
                                class="btn">{{ __('taxido::static.see_details') }}
                                <img src="{{ asset('images/dashboard/details/arrow-right.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body bg-image">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('images/dashboard/details/accept.svg') }}" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <span>{{ __('taxido::static.rides.accepted') }}</span>
                                    <h4>{{ getTotalRidesByStatus(RideStatusEnum::ACCEPTED, $start_date, $end_date) ?? 0 }}</h4>
                                </div>
                            </div>
                            <a href="{{ route('admin.ride.accepted-rides') }}"
                                class="btn">{{ __('taxido::static.see_details') }}
                                <img src="{{ asset('images/dashboard/details/arrow-right.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body bg-image">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('images/dashboard/details/car-travel.svg') }}" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <span>{{ __('taxido::static.rides.started') }}</span>
                                    <h4>{{ getTotalRidesByStatus(RideStatusEnum::STARTED, $start_date, $end_date) ?? 0 }}</h4>
                                </div>
                            </div>
                            <a href="{{ route('admin.ride.started-rides') }}"
                                class="btn">{{ __('taxido::static.see_details') }}
                                <img src="{{ asset('images/dashboard/details/arrow-right.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body bg-image">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('images/dashboard/details/event.svg') }}" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <span>{{ __('taxido::static.rides.scheduled') }}</span>
                                    <h4>{{ getTotalRidesByStatus(RideStatusEnum::SCHEDULED, $start_date, $end_date) ?? 0 }}
                                    </h4>
                                </div>
                            </div>
                            <a href="{{ route('admin.ride.scheduled-rides') }}"
                                class="btn">{{ __('taxido::static.see_details') }}
                                <img src="{{ asset('images/dashboard/details/arrow-right.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body bg-image">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('images/dashboard/details/cancel.svg') }}" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <span>{{ __('taxido::static.rides.cancelled') }}</span>
                                    <h4>{{ getTotalRidesByStatus(RideStatusEnum::CANCELLED, $start_date, $end_date) ?? 0 }}
                                    </h4>
                                </div>
                            </div>
                            <a href="{{ route('admin.ride.cancelled-rides') }}"
                                class="btn">{{ __('taxido::static.see_details') }}
                                <img src="{{ asset('images/dashboard/details/arrow-right.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body bg-image">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('images/dashboard/details/check.svg') }}" alt="">
                                </div>
                                <div class="flex-grow-1">
                                    <span>{{ __('taxido::static.rides.completed') }}</span>
                                    <h4>{{ getTotalRidesByStatus(RideStatusEnum::COMPLETED, $start_date, $end_date) ?? 0 }}
                                    </h4>
                                </div>
                            </div>
                            <a href="{{ route('admin.ride.completed-rides') }}"
                                class="btn">{{ __('taxido::static.see_details') }}
                                <img src="{{ asset('images/dashboard/details/arrow-right.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-xl-4">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <div>
                            <h5 class="m-0">{{ __('taxido::static.widget.service_categories') }}</h5>
                        </div>
                        <div class="card-header-right-icon">
                            <div class="dropdown icon-dropdown">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body categories-chart">
                    @if ($intercityRides === 0 && $rideRides === 0 && $rentalRides === 0 && $scheduledRides === 0 && $packageRides === 0)
                        <div id="not-found-image" class="no-data-found">
                            <img src="{{ asset('images/dashboard/chart-not-found.svg') }}" class="img-fluid"
                                alt="No Data Available">
                            <span colspan="5" class="text-center">{{ __('taxido::static.widget.no_data_available') }}</span>
                        </div>
                    @else
                        <div id="Categories-chart"></div>
                    @endif

                </div>
            </div>
        </div>
    @endcan

@endisset

@push('scripts')
    <script src="{{ asset('js/apex-chart.js') }}"></script>
    <script src="{{ asset('js/custom-apexchart.js') }}"></script>
    <script>
        var intercityRides = <?php echo (int) $intercityRides; ?>;
        var rideRides = <?php echo (int) $rideRides; ?>;
        var rentalRides = <?php echo (int) $rentalRides; ?>;
        var scheduledRides = <?php echo (int) $scheduledRides; ?>;
        var packageRides = <?php echo (int) $packageRides; ?>;
        var totalRides = <?php echo (int) $totalRides; ?>;


        var options1 = {
            series: [intercityRides, rideRides, rentalRides, scheduledRides, packageRides],
            labels: [
                "Intercity",
                "Ride",
                "Rental",
                "Schedule",
                "Package",
            ],
            chart: {
                type: 'donut',
                height: 250,
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: "14px",
                fontFamily: "Outfit', sans-serif",
                fontWeight: 500,
            },
            responsive: [{
                    breakpoint: 1441,
                    options: {
                        chart: {
                            height: 275,
                        },
                    },
                },
                {
                    breakpoint: 421,
                    options: {
                        chart: {
                            height: 170,
                        },
                    },
                }
            ],
            plotOptions: {
                pie: {
                    expandOnClick: false,
                    donut: {
                        size: "68%",
                        labels: {
                            show: true,
                            value: {
                                offsetY: 5,
                            },
                            total: {
                                show: true,
                                fontSize: "15px",
                                color: "#8D8D8D",
                                fontFamily: "Outfit', sans-serif",
                                fontWeight: 500,
                                label: "Total Rides",
                                formatter: () => totalRides,
                            },
                        },
                    },
                },
            },
            colors: ['#199675', '#ECB238', '#F39159', '#86909C', '#47A1E5'],
        };

        var chart1 = new ApexCharts(document.querySelector("#Categories-chart"), options1);
        chart1.render();
    </script>
@endpush
