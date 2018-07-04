@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">Add Geolocation</h2>
        <h6 class="card-subtitle mb-2 text-danger">GeoLocations are data sensitive. Be cautious!</h6>
        <div class="card-text">
            @include('DataEntry.Forms.geolocation')
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Add New GeoLocation</title>
@endpush