@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">Add Geolocation</h2>
        <h6 class="card-subtitle mb-2 text-danger">GeoLocations are data sensitive. Be cautious!</h6>
        <div class="card-text">
            @include('DataEntry.Forms.geolocation')
            <form id="newgeolocation_form" action="{{ route('Geolocation.add') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-map-o"></i></span>
                        <select name="is_in_european_union" class="custom-select" tabindex="-1">
                            <option value="0">-</option>
                            <option value="1">European Union</option>
                        </select>
                        <input name="time_zone" type="text" class="input-group-text" placeholder="Time Zone" list="time_zones">
                    </div>
                    <input id="continent_sync" name="continent" type="text" class="form-control" placeholder="Continent" tabindex="1">
                    <input id="country_sync" name="country" type="text" class="form-control" placeholder="Country" tabindex="1">
                    <input id="division_sync" name="division" type="text" class="form-control" placeholder="Division" tabindex="1">
                    <input id="subdivision_sync" name="subdivision" type="text" class="form-control" placeholder="Subdivision" tabindex="1">
                    <input id="city_sync" name="city" type="text" class="form-control" placeholder="City" tabindex="1">
                    <div class="input-group-append">
                        <button class="btn btn-light border" type="submit" tabindex="1"><i id="ajax_status" class="fa fa-plus"></i></button>
                    </div>
                </div>
                <datalist id="time_zones">
                    @foreach(App\Models\GeoLocation::select('time_zone')->groupBy('time_zone')->get() as $tz)
                    <option value="{{$tz->time_zone}}">
                    @endforeach
                </datalist>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Add New GeoLocation</title>
@endpush
@push('scripts')
<script>
    var sync = true;
    $("#newgeolocation_form input").change(function(){
    $(this).nextAll().val("");
    });
    $("#newgeolocation_form").submit(function(){
        return confirm("Add GeoLocation to Database?");
    });
</script>
@endpush