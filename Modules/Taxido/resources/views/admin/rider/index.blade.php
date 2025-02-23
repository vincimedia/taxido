@extends('admin.layouts.master')
@section('title', __('taxido::static.riders.riders'))
@section('content')
    @includeIf('inc.modal', [
        'export' => true,
        'routes' => 'admin.rider.export',
        'import' => true,
        'route' => 'admin.rider.import.csv',
        'instruction_file' => 'admin/import/riders',
        'example_file' => 'admin/import/example/riders.csv',
    ])
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ __('taxido::static.riders.riders') }}</h3>
                    <div class="subtitle-button-group">
                        @can('rider.create')
                            <button class="add-spinner btn btn-outline" data-url="{{ route('admin.rider.create') }}">
                                <i class="ri-add-line"></i> {{ __('taxido::static.riders.add_new') }}
                            </button>
                        @endcan
                        @can('rider.index')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="ri-download-line"></i> {{ __('static.export.export') }}
                            </button>
                        @endcan
                        @can('rider.create')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#importModal"
                                id="importButton" data-model="admin.rider.import.csv">
                                <i class="ri-upload-line"></i> {{ __('taxido::static.import.import') }}
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="rider-table">
                <x-table :columns="$tableConfig['columns']" :data="$tableConfig['data']" :filters="$tableConfig['filters']" :actions="$tableConfig['actions']" :actionButtons="$tableConfig['actionButtons']"
                    :total="$tableConfig['total']" :bulkactions="$tableConfig['bulkactions']" :search="true">
                </x-table>
            </div>
        </div>
    </div>
@endsection
