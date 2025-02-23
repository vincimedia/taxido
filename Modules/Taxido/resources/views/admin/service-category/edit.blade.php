@extends('admin.layouts.master')
@section('title', __('taxido::static.service_categories.edit'))
@section('content')
<div class="serviceCategory-main">
    <form id="serviceCategoryForm" action="{{ route('admin.service-category.update', $serviceCategory->id) }}" method="POST" enctype="multipart/form-data">
        <div class="row g-xl-4 g-3">
            @method('PUT')
            @csrf
            @include('taxido::admin.service-category.fields')
        </div>
    </form>
</div>
@endsection
