@if(Cookie::get('geolocation_id'))
<ol class="breadcrumb bg-warning">
    @php
    $g = App\Models\GeoLocation::find(Cookie::get('geolocation_id'));
    @endphp
    @foreach($g->toArray() as $k => $v)
    @if($v && in_array($k, ["country", "division", "subdivision", "city"]))
    <li class="breadcrumb-item">{!!($k == "country")?'<i class="fa fa-map-marker"></i> ':''!!}{{$v}}</li>
    @endif
    @endforeach
</ol>
@endif