@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        {{-- <h2 class="card-title">Data Management</h2> --}}
        {{-- <h4 class="card-subtitle mb-2 text-muted">TripClues Data Management</h4> --}}
        <div class="card-text">
                <div class="jumbotron bg-white">
                    <h1 class="display-4">Hello, {{ Auth::user()->name }}!</h1>
                    <p class="lead">This is your Dashboard!</p>
                    <hr class="my-4">
                    <p>Your Activities and Stats will be displayed here!</p>
                    <p class="text-warning">More Coming Soon!</p>
                </div>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{ Auth::user()->name }} | Data Manager</title>
@endpush