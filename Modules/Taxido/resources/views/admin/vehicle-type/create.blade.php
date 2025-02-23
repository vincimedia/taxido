@extends('admin.layouts.master')
@section('title', __('taxido::static.vehicle_types.add'))
@section('content')
    <div class="vehicle-create">
        <form id="vehicleTypeForm" action="{{ route('admin.vehicle-type.store') }}" method="POST" enctype="multipart/form-data">
            @method('POST')
            @csrf
            @include('taxido::admin.vehicle-type.fields')
        </form>
    </div>
@endsection
