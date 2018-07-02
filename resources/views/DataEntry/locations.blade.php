@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if(!$operation)
        <a class="nav-link pull-right" href="{{ route('DataEntry.Locations',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        @endif
        <h2 class="card-title">{{ ucwords($operation)." " }}Global {{($operation)?'Location':'Locations'}}</h2>
        @if(!$operation)
        <h6 class="card-subtitle mb-2 text-muted">These are global locations can be Landmark or Attraction</h6>
        @endif
        <p class="card-text">
            @if($operation && ($operation == "add" || $operation == "edit"))
            @include('DataEntry.Forms.location')
            @else
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Location</th>
                    <th scope="col">LatLong</th>
                    <th scope="col">Content</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($locations as $location)
                    <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$location->title}}</td>
                    <td><span class="badge badge-light">{!!($location->type == "landmark")?'<i class="fa fa-map-pin"></i> Landmark':'<i class="fa fa-group"></i> Attraction'!!}</span></td>
                    <td{!! (!$location->geoLocation)?' class="bg-danger"':''!!}>
                    @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $location->geoLocation, "routeName" => 'DataEntry.Locations', "routePram" => ["operation"=>"geography", "id"=>$location->geolocation_id]])
                    @endcomponent
                    </td>
                    <td{!! (!$location->latitude || !$location->longitude)?' class="bg-danger"':''!!}>{{ (($location->latitude)?$location->latitude.", ":'').$location->longitude }}</td>
                    <td>
                        @if($location->content_id)
                        <script> var content_{{$location->content_id}} = {!!json_encode($location->getContent->content)!!};</script>
                        <i class="fa fa-clone" style="cursor:pointer" onclick='preview_html(content_{{$location->content_id}})'></i>
                        @else
                        -
                        @endif
                    </td>
                    @if(Auth::user()->admin)
                    <td>{{($location->user_id)?$location->getUser->name:'-'}}</td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Locations', ["operation"=>"edit", "id"=>$location->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Locations.delete', ['id' => $location->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </p>
    </div>
</div>
@endsection
@push('title')
<title>Global Locations</title>
@endpush