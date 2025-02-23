@extends('admin.layouts.master')
@section('title', __('taxido::static.rides.riderequests'))
@section('content')
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ __('taxido::static.rides.riderequests') }}</h3>
                    <div class="subtitle-button-group">
                        @can('ride_request.create')
                            <button class="add-spinner btn btn-outline" data-url="{{ route('admin.ride-request.create') }}">
                                <i class="ri-add-line"></i> {{ __('taxido::static.riders.add_new') }}
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="ride-request-table">
                <x-table :columns="$tableConfig['columns']" :data="$tableConfig['data']" :filters="$tableConfig['filters']" :actions="$tableConfig['actions']" :total="$tableConfig['total']"
                    :bulkactions="$tableConfig['bulkactions']" :actionButtons="$tableConfig['actionButtons']" :search="true">
                </x-table>
            </div>
        </div>
    </div>
@endsection
