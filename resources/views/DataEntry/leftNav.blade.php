<div class="list-group">
    <!--a href="" class="list-group-item list-group-item-action{{ (Route::is("Geolocation"))?' active':'' }}" data-toggle="popover" data-trigger="hover" title="Current GeoLocation" data-content="<?php
    if(Cookie::get('geolocation_id')) {
        $g = App\Models\GeoLocation::find(Cookie::get('geolocation_id'));
        $popover = [];
        foreach($g->toArray() as $k => $v) {
            if($v && in_array($k, ["country", "division", "subdivision", "city"]))
            $popover[] = $v;
        }
        echo implode(", ", array_reverse($popover));
    } else { echo "None"; }
    ?>">Geolocation</a-->
    <a href="{{ route('DataEntry.Facilities') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Facilities"))?' active':'' }}">Facilities</a>
    <a href="{{ route('DataEntry.Markers') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Markers"))?' active':'' }}">Labels/Tags/Categories</a>
    <a href="{{ route('DataEntry.Locations') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Locations"))?' active':'' }}">Locations</a>
    <a href="{{ route('DataEntry.Package') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Package") || Route::is("DataEntry.Package.*"))?' active':'' }}">Package</a>
    <a href="#" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.hotle"))?' active':'' }}">Hotel</a>
    <div class="list-group-item list-group-item-action">
        Image
        <a href="#" id="browseImage" class=""><i class="fa fa-folder-open-o"></i></a>
        <a href="#" id="uploadImage" class=""><i class="fa fa-cloud-upload"></i></a>
    </div>
</div>