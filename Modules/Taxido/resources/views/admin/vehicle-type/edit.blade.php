@extends('admin.layouts.master')
@section('title', __('taxido::static.vehicle_types.edit'))
@section('content')
    <div class="banner-main">
        <form id="vehicleForm" action="{{ route('admin.vehicle-type.update', $vehicleType->id) }}" method="POST"
            enctype="multipart/form-data">
            @method('PUT')
            @csrf
            @include('taxido::admin.vehicle-type.fields')
        </form>
    </div>
@endsection
