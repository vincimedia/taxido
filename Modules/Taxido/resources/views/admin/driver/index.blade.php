@extends('admin.layouts.master')
@section('title', __('taxido::static.drivers.drivers'))
@section('content')
    @includeIf('inc.modal', [
        'export' => true,
        'import' => true,
        'routes' => 'admin.driver.export',
        'route' => 'admin.driver.import.csv',
        'instruction_file' => 'admin/import/drivers',
        'example_file' => 'admin/import/example/drivers.csv',
    ])
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ $title }}</h3>
                    <div class="subtitle-button-group">
                        @can('driver.create')
                            <button class="add-spinner btn btn-outline" data-url="{{ route('admin.driver.create') }}">
                                <i class="ri-add-line"></i> {{ __('taxido::static.drivers.add_new') }}
                            </button>
                        @endcan
                        @can('driver.index')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="ri-download-line"></i> {{ __('taxido::static.export.export_drivers') }}
                            </button>
                        @endcan
                        @can('driver.create')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#importModal"
                                id="importButton" data-model="driver">
                                <i class="ri-upload-line"></i> {{ __('taxido::static.import.import') }}
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="driver-table">
                <x-table :columns="$tableConfig['columns']" :data="$tableConfig['data']" :filters="$tableConfig['filters']" :actions="$tableConfig['actions']" :actionButtons="$tableConfig['actionButtons']"
                    :total="$tableConfig['total']" :bulkactions="$tableConfig['bulkactions']" :search="true">
                </x-table>
            </div>
        </div>
    </div>
@endsection
