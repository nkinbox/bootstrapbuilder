@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{ucwords($operation)}} Itinerary</h2>
        <h6 class="card-subtitle mb-2 text-muted">{{$package->title}} | {{ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))}}</h6>
        <div class="card-text">
            @include('DataEntry.Forms.geolocation')
            @if($operation == "edit")
            <form action="{{ route('DataEntry.Package.Itineraries.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$itinerary->id}}">
            @else
            <form action="{{ route('DataEntry.Package.Itineraries.add') }}" method="post">
            <?php $itinerary = null; ?>
            @endif
                <input type="hidden" name="package_detail_id" value="{{$packageDetail->id}}">
                <input type="hidden" name="package_id" value="{{$package->id}}">
                @csrf
                @component('DataEntry.Forms.hotelSearch', ["default" => (($itinerary && $itinerary->hotel_id)?$itinerary->hotel->hotel_name:'')])
                @endcomponent
                <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Title" tabindex="1" name="title" value="{{ old("title", (($itinerary)?$itinerary->title:'')) }}">
                        <div class="input-group-append">
                            <select class="custom-select" tabindex="2" name="itinerary_type">
                                <option value="">Skip Geolocation</option>
                                <option value="geolocation"{{(old("itinerary_type", (($itinerary && $itinerary->geolocation_id && !$itinerary->hotel_id)?'geolocation':'')) == "geolocation")?' selected':''}}>With Geolocation</option>
                                <option value="geo_hotel"{{(old("itinerary_type", (($itinerary && $itinerary->geolocation_id && $itinerary->hotel_id)?'geo_hotel':'')) == "geo_hotel")?' selected':''}}>With Geolocation and Hotel</option>
                            </select>
                        </div>
                    </div>
                    <div id="struct_content_form_group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-gears"></i></span>
                            </div>
                            <select name="content_type" class="custom-select struct_content_type" tabindex="3">
                                <option value="text"{{(old("content_type", (($itinerary && $itinerary->getContent)?$itinerary->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                                <option value="html"{{(old("content_type", (($itinerary && $itinerary->getContent)?$itinerary->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                                <option value="blade"{{(old("content_type", (($itinerary && $itinerary->getContent)?$itinerary->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                            </div>
                            <textarea class="form-control" name="content" placeholder="..." tabindex="4" style="height:300px">{{ old("content", (($itinerary && $itinerary->getContent)?$itinerary->getContent->content:'')) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success submit_button" tabindex="5">Save</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{$package->title}} | {{ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))}}</title>
@endpush