@extends('layouts.app')
@section('content')
<div class="container-fluid py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach ($breadcrumbs as $breadcrumb)
                @if($loop->last)
                <li class="breadcrumb-item active">{!! $breadcrumb['name'] !!}</li>                    
                @elseif(!empty($breadcrumb['routePar']))
                <li class="breadcrumb-item"><a href="{{ route($breadcrumb['route'], $breadcrumb['routePar']) }}">{{ $breadcrumb['name'] }}</a></li>                    
                @else
                <li class="breadcrumb-item"><a href="{{ route($breadcrumb['route']) }}">{!! $breadcrumb['name'] !!}</a></li>                    
                @endif
                @endforeach
            </ol>
        </nav>
    <div id="template-card-container" class="mx-auto my-3">
        @yield('card')
    </div>
@endsection
@push('scripts')
<script>
    var urls = {
        "imageUpload":"{{ route('Image.upload') }}",
        "image":"{{ route('Image.get') }}"
    }
</script>
@endpush
@push('scripts')
<script src="{{ asset('js/data_entry_script.js') }}"></script>
@endpush