<div class="list-group position-fixed">
    @if(Auth::user()->admin)
    <a href="{{ route('Geolocation') }}" class="list-group-item list-group-item-action{{ (Route::is("Geolocation"))?' active':'' }}" tabindex="20"><i class="fa fa-map-marker"></i> GeoLocation</a>
    @endif
    <a href="{{ route('DataEntry.Facilities') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Facilities"))?' active':'' }}" tabindex="20"><i class="fa fa-bed"></i> Facilities</a>
    <a href="{{ route('DataEntry.Markers') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Markers"))?' active':'' }}" tabindex="20"><i class="fa fa-tags"></i> Labels/Tags/Categories</a>
    <a href="{{ route('DataEntry.Locations') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Locations"))?' active':'' }}" tabindex="20"><i class="fa fa-map-signs"></i> Locations</a>
    <a href="{{ route('DataEntry.Transports') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Transports"))?' active':'' }}" tabindex="20"><i class="fa fa-motorcycle"></i> Transports</a>
    <a href="{{ route('DataEntry.Package') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Package") || Route::is("DataEntry.Package.*"))?' active':'' }}" tabindex="20"><i class="fa fa-shopping-bag"></i> Package</a>
    <a href="{{ route('DataEntry.Hotel') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Hotel") || Route::is("DataEntry.Hotel.*"))?' active':'' }}" tabindex="20"><i class="fa fa-building"></i> Hotel</a>
    <a href="{{ route('DataEntry.Page') }}" class="list-group-item list-group-item-action{{ (Route::is("DataEntry.Page"))?' active':'' }}" tabindex="20"><i class="fa fa-file-text"></i> Web Pages</a>
    <div class="list-group-item list-group-item-action">
        <i class="fa fa-photo"></i> Image
        <a href="#" id="browseImage" class="" tabindex="21"><i class="fa fa-folder-open-o"></i></a>
        <a href="#" id="uploadImage" class="" tabindex="20"><i class="fa fa-cloud-upload"></i></a>
    </div>
</div>