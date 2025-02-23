@extends('admin.layouts.master')
@section('title', __('taxido::static.driver_documents.driver_documents'))
@section('content')
    @includeIf('inc.modal', [
        'export' => true,
        'routes' => 'admin.driver-document.export',
        'import' => true,
        'route' => 'admin.driver-document.import.csv',
        'instruction_file' => 'admin/import/DriverDocuments',
        'example_file' => 'admin/import/example/DriverDocuments.csv',
    ])
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ __('taxido::static.driver_documents.driver_documents') }}</h3>
                    <div class="subtitle-button-group">
                        @can('driver_document.create')
                            <button class="add-spinner btn btn-outline" data-url="{{ route('admin.driver-document.create') }}">
                                <i class="ri-add-line"></i> {{ __('taxido::static.driver_documents.add_new') }}
                            </button>
                        @endcan
                        @can('driver_document.index')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="ri-download-line"></i> {{ __('taxido::static.export.export') }}
                            </button>
                        @endcan
                        @can('driver_document.create')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#importModal"
                                id="importButton" data-model="driver-document">
                                <i class="ri-upload-line"></i> {{ __('taxido::static.import.import') }}
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="driverDocument-table">
                <x-table :columns="$tableConfig['columns']" :data="$tableConfig['data']" :filters="$tableConfig['filters']" :actions="$tableConfig['actions']" :total="$tableConfig['total']"
                    :bulkactions="$tableConfig['bulkactions']" :viewActionBox="$tableConfig['viewActionBox']" :search="true" />
            </div>
        </div>
    </div>
@endsection
