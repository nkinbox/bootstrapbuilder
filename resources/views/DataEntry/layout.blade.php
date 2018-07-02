@extends('layouts.app')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger m-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <ul class="list-group p-1">
            @foreach ($errors->all() as $error)
                <li class="list-group-item">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('message'))
    <div class="alert alert-success m-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session('message') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger m-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session('error') }}
    </div>
@endif
<div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                @if($loop->last)
                <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>                    
                @elseif(!empty($breadcrumb['routePar']))
                <li class="breadcrumb-item"><a href="{{ route($breadcrumb['route'], $breadcrumb['routePar']) }}">{{ $breadcrumb['name'] }}</a></li>                    
                @else
                <li class="breadcrumb-item"><a href="{{ route($breadcrumb['route']) }}">{{ $breadcrumb['name'] }}</a></li>                    
                @endif
                @endforeach
            </ol>
        </nav>
<div class="row">
    <div class="col-2">
            @include('DataEntry.leftNav')
    </div>
    <div class="col-10">
        @yield('card')
    </div>
</div>
</div>
@endsection
@push('scripts')
<script>
    var urls = {
        "geolocation":"{{ route('Geolocation.get') }}",
        "imageUpload":"{{ route('Image.upload') }}",
        "hotel":"{{ route('Hotel.get') }}",
        "image":"{{ route('Image.get') }}"
    }
</script>
@endpush
@push('scripts')
<script src="{{ asset('js/data_entry_script.js') }}" defer></script>
@endpush