@extends('front.layouts.master')
@section('content')
<body>
    <div class="container">
        <h1>{{ $page?->title ?? '' }}</h1>
        <div>
            {!! $page?->content ?? '' !!}
        </div>
    </div>
</body>
@endsection


