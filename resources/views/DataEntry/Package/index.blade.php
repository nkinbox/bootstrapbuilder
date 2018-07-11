@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if(!$operation)
        <a class="nav-link pull-right" href="{{ route('DataEntry.Package',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">Tour Packages</h2>
        @elseif($operation == "geography")
        <a class="nav-link pull-right" href="{{ route('DataEntry.Package',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">Tour Packages by GeoLocation</h2>
        @elseif($operation == "add")
        <h2 class="card-title">Add Tour Package</h2>
        @elseif($operation == "edit")
        <h2 class="card-title">{{ ucwords((($operation == "edit")?'Edit ':'').$package->title)." " }}</h2>
        @endif
        <p class="card-text">
            @if($operation && $operation == "add" || $operation == "edit")
            @include('DataEntry.Forms.package')
            @else
            @if($operation && $operation == "geography")
            @include('DataEntry.Forms.geolocation', ["route" => Route::currentRouteName()])
            @endif
            {{ $packages->links() }}
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Locations</th>
                    <th scope="col">Locality</th>
                    <th scope="col">Content</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Updated at</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($packages as $package)
                    <?php
                    $isComplete = true;
                    if(count($package->PackageDetail)) {
                        if(!count($package->PackageItinerary) || !count($package->PackageMarker) || !count($package->PackagePrice))
                        $isComplete = false;
                    } else $isComplete = false;
                    $arrGeoLocation = [];
                    $arrLocation = [];
                    ?>
                    <tr>
                    <th scope="row"{!! (!$isComplete)?' class="bg-danger text-light"':'' !!}>{{$loop->iteration}}</th>
                    <td><a href="{{ route('DataEntry.Package.Detail', ["package_id"=>$package->id]) }}">{{$package->title}}</a></td>
                    <td>
                        @foreach($package->PackageItinerary as $itinerary)
                        @if($itinerary->geolocation_id && !in_array($itinerary->geolocation_id, $arrGeoLocation))
                        <?php $arrGeoLocation[] = $itinerary->geolocation_id; ?>
                        @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $itinerary->geoLocation, "routeName" => 'DataEntry.Package', "routePram" => ['operation' => 'geography', 'id' =>$itinerary->geolocation_id]])
                        @endcomponent
                        <br>
                        @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($package->PackageItinerary as $itinerary)
                        @if($itinerary->location_id && !in_array($itinerary->location_id, $arrLocation))
                        <?php $arrLocation[] = $itinerary->location_id; ?>
                        {{$itinerary->Location->title}}
                        <br>
                        @endif
                        @endforeach
                    </td>
                    <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
                        $image = App\Models\Images::find($m[1]);
                        return 'src="' .(($image)?asset('storage/'.$image->file_name):'#'). '"';
                        },$package->content); ?>
                        @if($package->content_id)
                        <script> var content_{{$package->content_id}} = {!!json_encode($package->getContent->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$package->content_id}})'></i>
                        @endif
                    </td>
                    @if(Auth::user()->admin)
                    <td>{{($package->user_id)?$package->getUser->name:'-'}}</td>
                    <td><small>{{$package->created_at}}</small></td>
                    <td><small>{{$package->updated_at}}</small></td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Package', ["operation"=>"edit", "id"=>$package->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Package.delete', ['id' => $package->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $packages->links() }}
            @endif
        </p>
    </div>
</div>
@endsection
@push('title')
<title>Tour Packages</title>
@endpush
