@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if(!$operation)
        <a class="nav-link pull-right" href="{{ route('DataEntry.Transports',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        @endif
        <h2 class="card-title">{{ ucwords($operation)." " }}Global {{($operation)?'Transport':'Transports'}}</h2>
        @if(!$operation)
        <h6 class="card-subtitle mb-2 text-muted">These are global transports can be Landmark or Attraction</h6>
        @endif
        @if($operation == "geography")
        @include('DataEntry.Forms.geolocation', ["route" => Route::currentRouteName()])
        @endif
        <p class="card-text">
            @if($operation && ($operation == "add" || $operation == "edit"))
            @include('DataEntry.Forms.transport')
            @else
            {{ $transports->links() }}
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
                @foreach($transports as $transport)
                    <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$transport->title}}</td>
                    <td><span class="badge badge-light">@if($transport->type == "Airport")
                        <i class="fa fa-plane"></i> Airport
                        @elseif($transport->type == "Busstand")
                        <i class="fa fa-bus"></i> Bus stand
                        @elseif($transport->type == "Railwaystation")
                        <i class="fa fa-train"></i> Railway station
                        @elseif($transport->type == "Taxistand")
                        <i class="fa fa-tax"></i> Taxi stand
                        @elseif($transport->type == "Cyclestand")
                        <i class="fa fa-bicycle"></i> Cycle stand
                        @endif</span> <small>{{$transport->category}}</small></td>
                    <td{!! (!$transport->geoLocation)?' class="bg-danger"':''!!}>
                        @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $transport->geoLocation, "routeName" => 'DataEntry.Transports', "routePram" => ["operation"=>"geography", "id"=>$transport->geolocation_id]])
                        @endcomponent
                    </td>
                    <td{!! (!$transport->latitude || !$transport->longitude)?' class="bg-danger"':''!!}>{{ (($transport->latitude)?$transport->latitude.", ":'').$transport->longitude }}</td>
                    <td>
                        @if($transport->content_id)
                        <script> var content_{{$transport->content_id}} = {!!json_encode($transport->getContent->content)!!};</script>
                        <i class="fa fa-clone" style="cursor:pointer" onclick='preview_html(content_{{$transport->content_id}})'></i>
                        @else
                        -
                        @endif
                    </td>
                    @if(Auth::user()->admin)
                    <td>{{($transport->user_id)?$transport->getUser->name:'-'}}</td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Transports', ["operation"=>"edit", "id"=>$transport->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Transports.delete', ['id' => $transport->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $transports->links() }}
            @endif
        </p>
    </div>
</div>
@endsection
@push('title')
<title>Global Transports</title>
@endpush