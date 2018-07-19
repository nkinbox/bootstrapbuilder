@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">Hotel | {{ $hotel->hotel_name }}</h2>
        <div class="card-text">
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Hotel Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Address</th>
                    <th scope="col">Locality</th>
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
                    <?php
                    $isComplete = true;
                    if(!count($hotel->HotelContact) || !count($hotel->AllHotelFacility) || !count($hotel->HotelMarker) || !count($hotel->HotelRoom))
                    $isComplete = false;
                    ?>
                    <tr{!! (!$hotel->visibility)?' class="bg-danger text-light"':'' !!}>
                    <th scope="row"{!! (!$isComplete)?' class="bg-danger text-light"':'' !!}><i class="fa fa-thumb-tack"></i></th>
                    <td><a href="{{ route('DataEntry.Hotel', ["operation" => "show", "id" => $hotel->id]) }}">{{$hotel->hotel_name}}</a></td>
                    <td><kbd>{{$hotel->property_type}}</kbd></td>
                    <td>{{$hotel->address}}</td>
                    <td>{{($hotel->location_id)?$hotel->Location->title:''}}</td>
                    @if($hotel->geolocation_id)
                        <td>
                        @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $hotel->geoLocation, "routeName" => null, "routePram" => []])
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
                </tbody>
            </table>
        </div>
        <div class="card-body">
            <div class="card-text">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link{{($tab == "hotel_contact")?' active':''}}" data-toggle="tab" href="#hotel_contact">Hotel Contacts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{($tab == "hotel_markers")?' active':''}}" data-toggle="tab" href="#hotel_markers">Labels Tags Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{($tab == "hotel_rooms")?' active':''}}" data-toggle="tab" href="#hotel_rooms">Hotel Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{($tab == "hotel_facilities")?' active':''}}" data-toggle="tab" href="#hotel_facilities">Hotel Facilities</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade{{($tab == "hotel_contact")?' show active':''}}" id="hotel_contact" role="tabpanel">
                        <a class="nav-link pull-right" href="{{ route('DataEntry.Hotel.Contact',['hotel_id' => $hotel->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add Contact</a>
                        <table class="table table-hover">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Detail</th>
                                    @if(Auth::user()->admin)
                                    <th scope="col">User</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                    @endif
                                    <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($hotel->HotelContact as $contact)
                                    <tr>
                                    <th scope="row">{{$loop->iteration}}</th>
                                    <td>{{$contact->type}}</td>
                                    <td><?php
                                    switch($contact->type) {
                                        case "email":
                                        echo '<i class="fa fa-envelope"></i> ';
                                        break;
                                        case "mobile":
                                        echo '<i class="fa fa-mobile"></i> ';
                                        break;
                                        case "landline":
                                        echo '<i class="fa fa-phone"></i> ';
                                        break;
                                        case "website":
                                        echo '<i class="fa fa-chain"></i> ';
                                    }
                                    ?>{{$contact->content}}</td>
                                    @if(Auth::user()->admin)
                                    <td>{{($contact->user_id)?$contact->getUser->name:'-'}}</td>
                                    <td><small>{{$contact->created_at}}</small></td>
                                    <td><small>{{$contact->updated_at}}</small></td>
                                    @endif
                                    <td>
                                        <a href="{{ route('DataEntry.Hotel.Contact', ['hotel_id' => $hotel->id, 'operation' => 'edit', 'id' => $contact->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                        /
                                        <a href="{{ route('DataEntry.Hotel.Contact.delete', ['id' => $contact->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                    <div class="tab-pane fade{{($tab == "hotel_markers")?' show active':''}}" id="hotel_markers" role="tabpanel">
                        <a class="nav-link pull-right" href="{{ route('DataEntry.Hotel.Marker',['hotel_id' => $hotel->id]) }}"><i class="fa fa-edit"></i> Markers</a>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Type</th>
                                <th scope="col">Title</th>
                                <th scope="col">Content</th>
                                <th scope="col">Order</th>
                                <th scope="col">Primary</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($hotel->HotelMarker as $marker)
                                <tr>
                                <th scope="row">{{$loop->iteration}}</th>
                                <td>{{$marker->type}}</td>
                                <td>{{$marker->title}}</td>
                                <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
                                    $image = App\Models\Images::find($m[1]);
                                    return 'src="' .(($image)?asset('storage/'.$image->file_name):'#'). '"';
                                    },$marker->content); ?>
                                    @if($marker->content_id)
                                    <script> var content_{{$marker->content_id}} = {!!json_encode($marker->getContent->content)!!};</script>
                                    <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$marker->content_id}})'></i>
                                    @endif
                                </td>
                                <td>{{ $marker->pivot->order }}</td>
                                <td>{!! ($marker->pivot->primary_marker)?'<span class="badge badge-primary">primary</span>':'-' !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade{{($tab == "hotel_rooms")?' show active':''}}" id="hotel_rooms" role="tabpanel">
                        <a class="nav-link pull-right" href="{{ route('DataEntry.Hotel.Room',['hotel_id' => $hotel->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Room Type</th>
                                <th scope="col">Price Start</th>
                                <th scope="col">Price End</th>
                                <th scope="col">Discount Percent</th>
                                @if(Auth::user()->admin)
                                <th scope="col">User</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Updated At</th>
                                @endif
                                <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($hotel->HotelRoom as $hotelRoom)
                                <tr>
                                <th scope="row">{{$loop->iteration}}</th>
                                <td>{{$hotelRoom->title}}</td>
                                <td>{{$hotelRoom->price_start}} {{$hotelRoom->currency}}</td>
                                <td>{{($hotelRoom->price_end)?$hotelRoom->price_end.' '.$hotelRoom->currency:'-'}}</td>
                                <td>{{ ($hotelRoom->discount_percent)?$hotelRoom->discount_percent.'%':'-' }}</td>
                                @if(Auth::user()->admin)
                                <td>{{($hotelRoom->user_id)?$hotelRoom->getUser->name:'-'}}</td>
                                <td><small>{{$hotelRoom->created_at}}</small></td>
                                <td><small>{{$hotelRoom->updated_at}}</small></td>
                                @endif
                                <td>
                                    <a href="{{ route('DataEntry.Hotel.Facility',['hotel_id' => $hotel->id, 'operation' => 'room', 'hotel_room_id' => $hotelRoom->id]) }}"><i class="fa fa-bed"></i> Facility</a>
                                    /
                                    <a href="{{ route('DataEntry.Hotel.Room',['hotel_id' => $hotel->id, 'operation' => 'edit', 'id' => $hotelRoom->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                    /
                                    <a href="{{ route('DataEntry.Hotel.Room.delete', ['id' => $hotelRoom->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade{{($tab == "hotel_facilities")?' show active':''}}" id="hotel_facilities" role="tabpanel">
                        <a class="nav-link pull-right" href="{{ route('DataEntry.Hotel.Facility',['hotel_id' => $hotel->id, 'operation' => 'hotel']) }}"><i class="fa fa-edit"></i> Hotel Facility</a>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Facility Type</th>
                                <th scope="col">Content</th>
                                @if(Auth::user()->admin)
                                <th scope="col">User</th>
                                @endif
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($hotel->HotelFacility->where('type', 'hotel') as $facility)
                                <tr>
                                <th scope="row">{{$loop->iteration}}</th>
                                <td>{{$facility->title}}</td>
                                <td><span class="badge badge-light">{!!($facility->type == "hotel")?'<i class="fa fa-building"></i> Hotel':'<i class="fa fa-hotel"></i> Room'!!}</span></td>
                                <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
                                    $image = App\Models\Images::find($m[1]);
                                    return 'src="' .(($image)?asset('storage/'.$image->file_name):'#'). '"';
                                    },$facility->content); ?>
                                    @if($facility->content_id)
                                    <script> var content_{{$facility->content_id}} = {!!json_encode($facility->getContent->content)!!};</script>
                                    <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$facility->content_id}})'></i>
                                    @endif
                                </td>
                                @if(Auth::user()->admin)
                                <td>{{($facility->user_id)?$facility->getUser->name:'-'}}</td>
                                @endif
                                </tr>
                            @endforeach
                            @foreach($hotel->HotelFacility->where('type', 'room')->unique() as $facility)
                            <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{$facility->title}}</td>
                            <td><span class="badge badge-light">{!!($facility->type == "hotel")?'<i class="fa fa-building"></i> Hotel':'<i class="fa fa-hotel"></i> Room'!!}</span></td>
                            <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
                                $image = App\Models\Images::find($m[1]);
                                return 'src="' .(($image)?asset('storage/'.$image->file_name):'#'). '"';
                                },$facility->content); ?>
                                @if($facility->content_id)
                                <script> var content_{{$facility->content_id}} = {!!json_encode($facility->getContent->content)!!};</script>
                                <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$facility->content_id}})'></i>
                                @endif
                            </td>
                            @if(Auth::user()->admin)
                            <td>{{($facility->user_id)?$facility->getUser->name:'-'}}</td>
                            @endif
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Hotel |{{ $hotel->hotel_name }}</title>
@endpush
