<form id="geolocation_form" data-route="{{(isset($route) && $route)?$route:'0'}}">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-map-o"></i></span>
            </div>
            <input id="continent" name="continent" type="text" class="form-control" placeholder="Continent" tabindex="1" list="continents">
            <input id="country" name="country" type="text" class="form-control" placeholder="Country" tabindex="1" list="countries">
            <input id="division" name="division" type="text" class="form-control" placeholder="Division" tabindex="1" list="divisions">
            <input id="subdivision" name="subdivision" type="text" class="form-control" placeholder="Subdivision" tabindex="1" list="subdivisions">
            <input id="city" name="city" type="text" class="form-control" placeholder="City" tabindex="1" list="cities">
            <div class="input-group-append">
                <button class="btn btn-light border" type="submit" tabindex="-1"><i id="ajax_status" class="fa fa-search"></i></button>
            </div>
        </div>
        <datalist id="continents">
            <option value="Africa">
            <option value="Antarctica">
            <option value="Asia">
            <option value="Europe">
            <option value="North America">
            <option value="Oceania">
            <option value="South America">
        </datalist>
        <datalist id="countries">
        </datalist>
        <datalist id="divisions">
        </datalist>
        <datalist id="subdivisions">
        </datalist>
        <datalist id="cities">
        </datalist>
</form>
<div id="geoLocation_in_Focus">@include('DataEntry.Forms.currentgeoLocation')</div>