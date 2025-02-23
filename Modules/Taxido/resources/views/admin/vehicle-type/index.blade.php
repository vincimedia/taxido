@extends('admin.layouts.master')
@section('title', __('taxido::static.vehicle_types.vehicles'))
@section('content')
    @includeIf('inc.modal', [
        'export' => true,
        'routes' => 'admin.vehicle-type.export',
        'import' => true,
        'route' => 'admin.vehicle-type.import.csv',
        'instruction_file' => 'admin/import/vehicleTypes',
        'example_file' => 'admin/import/example/vehicleTypes.csv',
    ])
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ __('taxido::static.vehicle_types.vehicles') }}</h3>
                    <div class="subtitle-button-group">
                        @can('vehicle_type.create')
                            <button class="add-spinner btn btn-outline" data-url="{{ route('admin.vehicle-type.create') }}">
                                <i class="ri-add-line"></i> {{ __('taxido::static.vehicle_types.add_new') }}
                            </button>
                        @endcan
                        @can('vehicle_type.index')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="ri-download-line"></i> {{ __('static.export.export') }}
                            </button>
                        @endcan
                        @can('vehicle_type.create')
                            <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#importModal"
                                id="importButton" data-model="vehicle-type">
                                <i class="ri-upload-line"></i> {{ __('taxido::static.import.import') }}
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="vehicle-table">
                <x-table :columns="$tableConfig['columns']" 
                         :data="$tableConfig['data']" 
                         :filters="$tableConfig['filters']" 
                         :actions="$tableConfig['actions']" 
                         :total="$tableConfig['total']"
                         :bulkactions="$tableConfig['bulkactions']" 
                         :search="true">
                </x-table>
            </div>
        </div>
    </div>
@endsection
