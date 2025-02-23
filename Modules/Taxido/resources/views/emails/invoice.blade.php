
@php
$symbol = getDefaultCurrencySymbol();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME')}} - {{__('taxido::static.rides.invoice') }}</title>
    <style>
        body {
            font-family: Inter, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #1F1F1F;
        }
        .text-primary {
            color: #199675;
            font-weight: 700;
        }
        .dark-color {
            color: #1F1F1F;
        }
        .common-color {
            color: #8F8F8F;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 70px;
            color: #199675;
        }

        .invoice-header div {
            text-align: right;
            font-size: 18px;
        }
        .all-details {
            display: flex;
            align-items: center;
            justify-content: space-between
        }
        .invoice-id {
            background-color: #EEEEEE;
            padding: 10px 40px 10px 30px;
            margin-right: -80px;
            border-radius: 50px;
            transform: translate(50%, 0);
        }
        .invoice-id p{
            padding-bottom: 0;
            text-align: left;
            margin-bottom: 5px
        }
        .invoice-id p span{
            margin-left: 20px;
        }
        .invoice-data {
            margin-top: -60px;
        }
        .invoice-data td {
            border: unset;
            background-color: unset;
            text-align: right;
            padding: 0;
        }
        .section-title {
            margin-top: 15px;
            margin-bottom: 5px;
            color: #199675;
        }

        p {
            margin: 0;
            padding-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: unset;
        }
        .table-details td {
            padding: 0;
            border: unset;
            background-color: unset;
            text-align: start !important;
        }
        th, td {
            padding: 10px;
            border: 1px solid #EEEEEE;
        }

        th {
            background: #ECECEC;
        }
        .table-Description td {
            text-align: center
        }
        tbody td {
            background-color: #FAFAFA;
            color: #8F8F8F;
        }
        tfoot td {
            font-weight: bold;
            text-align: right;
            background: #ECECEC;
            border: unset;
            text-align: center;
        }

        .total {
            background: #f5f5f5;
        }
        .footer-content .section-title {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <table class="invoice-data">
            <tbody class="invoice-header">
                <tr>
                    <td></td>
                    <td>
                        <h3>{{ env('APP_NAME')}} - {{__('taxido::static.rides.invoice') }}</h3>
                        <div class="invoice-id common-color">
                            <p> {{__('taxido::static.rides.ride_number')}}: {{$ride->ride_number}}<span>{{ __('taxido::static.invoice.date') }}: {{ $ride->created_at->format('d/m/Y') }}</span></p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table-details">
            <tbody>
                <tr>
                    <td>
                        <div class="invoice-data">
                            <div class="invoice-to">
                                <p class="common-color">{{ __('taxido::static.invoice.invoice_to') }}</p>
                                <p class="dark-color">{{ __('taxido::static.invoice.name') }}: <span class="text-primary">{{$ride->rider['name']}}</span> | <span class="dark-color">{{ __('taxido::static.invoice.rider_contact') }}:</span><span class="common-color">{{ $ride->rider['country_code'] }}-{{$ride->rider['phone']}}</span></span></p>
                            </div>

                            <div class="ride-details">
                                <h3 class="section-title">{{ __('taxido::static.invoice.ride_details') }}:</h3>
                                <p class="dark-color">{{ __('taxido::static.invoice.service') }}: <span class="common-color">{{$ride->service['name']}} | <span class="dark-color">Service Category:</span> <span class="common-color">{{$ride->service_category['name']}}</span></span></p>
                                <p class="dark-color">{{ __('taxido::static.invoice.pickup_time') }}: <span class="common-color">{{$ride?->start_time}} | <span class="dark-color">Drop-off Time:</span> <span class="common-color">{{$ride->end_time}}</span></span></p>
                                <p class="dark-color">{{ __('taxido::static.invoice.pickup_location') }}: <span class="common-color">{{$ride->locations[0]}}</span></p>
                                <p class="dark-color">{{ __('taxido::static.invoice.drop_off_location') }}: <span class="common-color">{{$ride->locations[1]}}</span></p>
                            </div>

                            <div class="vehicle">
                                <h3 class="section-title">{{ __('taxido::static.invoice.vehicle_driver_info') }}:</h3>
                                <p class="dark-color">{{ __('taxido::static.invoice.vehicle_type') }}: <span class="common-color">{{ $ride->vehicle_info->vehicle->name }} |</span> <span class="dark-color">Vehicle Model:</span> <span class="common-color">{{$ride->vehicle_info['model']}}</span></p>
                                <p class="dark-color">{{ __('taxido::static.invoice.vehicle_number') }}: <span class="common-color">{{$ride->vehicle_info['plate_number']}}</span></p>
                                <p class="dark-color">{{ __('taxido::static.invoice.drop_off_location') }}: <span class="common-color">{{$ride?->driver['name']}}</span></p>
                                <p class="dark-color">{{ __('taxido::static.invoice.drop_off_location') }}: <span class="common-color">(+{{ $ride?->driver['country_code'] }})-{{$ride->driver['phone']}}</span></p>
                            </div>
                        </div>
                    </td>
                    <td>
                </tr>
            </tbody>
        </table>
        <table class="table-Description">
            <thead>
                <tr>
                    <th>{{ __('taxido::static.invoice.description') }}</th>
                    <th>{{ __('taxido::static.invoice.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('taxido::static.invoice.base_fare') }}</td>
                    <td>{{$symbol}}{{$ride->ride_fare}}</td>
                </tr>
                <tr>
                    <td>{{ __('taxido::static.invoice.driver_tips') }}</td>
                    <td>@if($ride->driver_tips){{$symbol}}{{round($ride->driver_tips, 2)}}@else N/A @endif</td>
                </tr>
                <tr>
                    <td>{{ __('taxido::static.invoice.tax') }}</td>
                    <td>@if($ride->tax){{$symbol}}{{ round($ride->tax,2)}}@else N/A @endif</td>
                </tr>
                <tr>
                    <td>{{ __('taxido::static.invoice.platform_fee') }}</td>
                    <td>@if($ride->platform_fees){{$symbol}}{{round($ride->platform_fees,2)}} @else N/A @endif</td>
                </tr>
                <tr>
                    <td>{{ __('taxido::static.invoice.processing_fee') }}</td>
                    <td>@if($ride->processing_fee){{$symbol}}{{round($ride->processing_fee,2)}} @else N/A @endif</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total">
                    <td colspan="1">{{ __('taxido::static.invoice.total') }}</td>
                    <td>{{$symbol}}{{$ride->total}}</td>
                </tr>
            </tfoot>
        </table>

        <div class="payment-method">
            <h3 class="section-title">{{ __('taxido::static.invoice.payment_details') }}:</h3>
            <p>{{ __('taxido::static.invoice.payment_method') }}: <span class="common-color">{{$ride->payment_method}} |</span> <span class="dark-color">Transaction ID:</span> <span class="common-color">TXN123456789</span></p>
        </div>

        <div class="footer-content">
            <h3 class="section-title">{{ __('taxido::static.invoice.thank_you') }}</h3>
            <p class="common-color">{{ __('taxido::static.invoice.thank_you_msg') }}</p>
        </div>
    </div>
</body>

</html>
