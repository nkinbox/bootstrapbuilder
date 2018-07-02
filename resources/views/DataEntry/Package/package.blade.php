<table class="table table-hover table-bordered">
<thead class="thead-dark d-none">
    <tr>
    <th scope="col">#</th>
    <th scope="col">Title</th>
    <th scope="col">Locations</th>
    <th scope="col">Content</th>
    @if(Auth::user()->admin)
    <th scope="col">User</th>
    <th scope="col">Created at</th>
    <th scope="col">Updated at</th>
    @endif
    </tr>
</thead>
<tbody>
        <?php
        $isComplete = true;
        if(count($package->PackageDetail)) {
            if(!count($package->PackageItinerary) || !count($package->PackageMarker) || !count($package->PackagePrice))
            $isComplete = false;
        } else $isComplete = false;
        $arrGeoLocation = [];
        ?>
        <tr>
        <th scope="row"{!! (!$isComplete)?' class="bg-danger text-light"':' class="bg-success text-light"' !!}><i class="fa fa-thumb-tack"></i></th>
        <td><a href="{{ route('DataEntry.Package.Detail', ["package_id"=>$package->id]) }}">{{$package->title}}</a></td>
        <td>
            @foreach($package->PackageItinerary as $itinerary)
            @if($itinerary->geolocation_id && !in_array($itinerary->geolocation_id, $arrGeoLocation))
            <?php $arrGeoLocation[] = $itinerary->geolocation_id; ?>
            @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $itinerary->geoLocation, "routeName" => null, "routePram" => []])
            @endcomponent
            <br>
            @endif
            @endforeach
        </td>
        <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
            $image = App\Models\Images::find($m[1]);
            return 'src="' .asset('storage/'.$image->file_name). '"';
            },$package->content); ?>
            @if($package->content_id)
            <script> var content_{{$package->content_id}} = {!!json_encode($package->getContent->content)!!};</script>
            <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$package->content_id}})'></i>
            @endif
        </td>
        @if(Auth::user()->admin)
        <td>{{($package->user_id)?$package->getUser->name:'-'}}</td>
        <td>{{$package->created_at}}</td>
        <td>{{$package->updated_at}}</td>
        @endif
        </tr>
</tbody>
</table>