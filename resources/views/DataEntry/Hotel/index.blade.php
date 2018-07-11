@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('DataEntry.Hotel',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">List of Hotels</h2>
        @if($operation == "geography")
        @include('DataEntry.Forms.geolocation', ["route" => Route::currentRouteName()])
        @endif
        <p class="card-text">
            {{ $hotels->links() }}
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Hotel Name</th>
                    <th scope="col">Address</th>
                    <th scope="col">Locatity</th>
                    <th scope="col">Location</th>
                    <th scope="col">LatLong</th>
                    <th scope="col">No of Rooms</th>
                    <th scope="col">Content</th>
                    <th scope="col">Policy</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Updated at</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($hotels as $hotel)
                    <?php
                    $isComplete = true;
                    if(!count($hotel->HotelContact) || !count($hotel->AllHotelFacility) || !count($hotel->HotelMarker) || !count($hotel->HotelRoom))
                    $isComplete = false;
                    ?>
                    <tr{!! (!$hotel->visibility)?' class="bg-danger text-light"':'' !!}>
                    <th scope="row"{!! (!$isComplete)?' class="bg-danger text-light"':'' !!}>{{$loop->iteration}}</th>
                    <td><a href="{{ route('DataEntry.Hotel', ["operation" => "show", "id" => $hotel->id]) }}">{{$hotel->hotel_name}}</a></td>
                    <td>{{$hotel->address}}</td>
                    <td>{{($hotel->location_id)?$hotel->Location->title:''}}</td>
                    @if($hotel->geolocation_id)
                        <td>
                        @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $hotel->geoLocation, "routeName" => 'DataEntry.Hotel', "routePram" => ["operation" => "geography", "id" => $hotel->geolocation_id]])
                        @endcomponent
                        </td>
                    @else
                        <td class="bg-danger text-light"></td>
                    @endif
                    <td{!! (!$hotel->latitude || !$hotel->longitude)?' class="bg-danger"':''!!}>{{ (($hotel->latitude)?$hotel->latitude.", ":'').$hotel->longitude }}</td>
                    <td>{{$hotel->no_of_rooms}}</td>
                    <td>
                        @if($hotel->content_id)
                        <script> var content_{{$hotel->content_id}} = {!!json_encode($hotel->getContent->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$hotel->content_id}})'></i>
                        @endif
                    </td>
                    <td>
                        @if($hotel->policy_id)
                        <script> var content_{{$hotel->policy_id}} = {!!json_encode($hotel->getPolicy->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$hotel->policy_id}})'></i>
                        @endif
                    </td>
                    @if(Auth::user()->admin)
                    <td>{{($hotel->user_id)?$hotel->getUser->name:'-'}}</td>
                    <td><small>{{$hotel->created_at}}</small></td>
                    <td><small>{{$hotel->updated_at}}</small></td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Hotel', ["operation"=>"edit", "id"=>$hotel->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Hotel.delete', ['id' => $hotel->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $hotels->links() }}
        </p>
    </div>
</div>
@endsection
@push('title')
<title>Hotels</title>
@endpush
