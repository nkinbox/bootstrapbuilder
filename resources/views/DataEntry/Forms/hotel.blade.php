@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{ ucwords($operation) }} Hotel {{ ($hotel)?'| '.$hotel->hotel_name:''}}</h2>
        <div class="card-text">
            @include('DataEntry.Forms.geolocation')
            @if($operation == "edit")
            <form action="{{ route('DataEntry.Hotel.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$hotel->id}}">
            @else
            <form action="{{ route('DataEntry.Hotel.add') }}" method="post">
            @endif
                @csrf
                    @component('DataEntry.Forms.locationSearch', ["default" => (($hotel && $hotel->location_id)?$hotel->Location->title:'')])
                    @endcomponent
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Latitude" tabindex="1" name="latitude" value="{{ old("latitude", (($hotel)?$hotel->latitude:'')) }}">
                        <input type="text" class="form-control" placeholder="Longitude" tabindex="2" name="longitude" value="{{ old("longitude", (($hotel)?$hotel->longitude:'')) }}">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Hotel Name" tabindex="3" name="hotel_name" value="{{ old("hotel_name", (($hotel)?$hotel->hotel_name:'')) }}">
                        <div class="input-group-append">
                            <input type="number" class="form-control" placeholder="No of Rooms" tabindex="4" name="no_of_rooms" value="{{ old("no_of_rooms", (($hotel)?$hotel->no_of_rooms:'')) }}">
                            <select class="custom-select" tabindex="5" name="visibility">
                                <option value="1"{{(old("visibility", (($hotel)?$hotel->visibility:'')) == "1")?' selected':''}}>Show</option>
                                <option value="0"{{(old("visibility", (($hotel)?$hotel->visibility:'')) == "0")?' selected':''}}>Hide</option>
                            </select>
                            <select class="custom-select" tabindex="5" name="property_type">
                                <option{{(old("property_type", (($hotel)?$hotel->type:'')) == "Hotel")?' selected':''}}>Hotel</option>
                                <option{{(old("property_type", (($hotel)?$hotel->type:'')) == "Resort")?' selected':''}}>Resort</option>
                                <option{{(old("property_type", (($hotel)?$hotel->type:'')) == "Cottage")?' selected':''}}>Cottage</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-address-card-o"></i></span>
                        </div>
                        <textarea class="form-control" name="address" placeholder="Address" tabindex="6" style="height:50px">{{ old("address", (($hotel)?$hotel->address:'')) }}</textarea>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Hotel Content</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-gears"></i></span>
                                </div>
                                <select name="hotel_content_type" class="custom-select" tabindex="7">
                                    <option value="text"{{(old("hotel_content_type", (($hotel && $hotel->getContent)?$hotel->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                                    <option value="html"{{(old("hotel_content_type", (($hotel && $hotel->getContent)?$hotel->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                                    <option value="blade"{{(old("hotel_content_type", (($hotel && $hotel->getContent)?$hotel->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code preview_html_hotel_content" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                                </div>
                                <textarea class="form-control" name="hotel_content" placeholder="..." tabindex="8" style="height:300px">{{ old("hotel_content", (($hotel && $hotel->getContent)?$hotel->getContent->content:'')) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Hotel Policy</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-gears"></i></span>
                                </div>
                                <select name="policy_content_type" class="custom-select" tabindex="9">
                                    <option value="text"{{(old("policy_content_type", (($hotel && $hotel->getPolicy)?$hotel->getPolicy->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                                    <option value="html"{{(old("policy_content_type", (($hotel && $hotel->getPolicy)?$hotel->getPolicy->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                                    <option value="blade"{{(old("policy_content_type", (($hotel && $hotel->getPolicy)?$hotel->getPolicy->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code preview_html_policy_content" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                                </div>
                                <textarea class="form-control" name="policy_content" placeholder="..." tabindex="10" style="height:300px">{{ old("policy_content", (($hotel && $hotel->getPolicy)?$hotel->getPolicy->content:'')) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Hotel Images</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                                </div>
                                <textarea class="form-control" name="content" placeholder="..." tabindex="11" style="height:100px"><?php
                                if(old("content"))
                                echo old("content");
                                else {
                                    if($hotel && count($hotel->Gallery)) {
                                        foreach($hotel->Gallery as $image) {
                                            echo '<img id=@@image.' .$image->id. '@@>';
                                        }
                                    }
                                }
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success submit_button" tabindex="12">Save</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{ ucwords($operation) }} Hotel {{ ($hotel)?'| '.$hotel->hotel_name:''}}</title>
@endpush