@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{$package->title}} | {{(($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only')}}</h2>
        <div class="card-text">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link{{($tab == "itineraries")?' active':''}}" data-toggle="tab" href="#package_itineraries">Itineraries</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{($tab == "markers")?' active':''}}" data-toggle="tab" href="#package_markers">Labels Tags Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{($tab == "prices")?' active':''}}" data-toggle="tab" href="#package_prices">Prices</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade{{($tab == "itineraries")?' show active':''}}" id="package_itineraries" role="tabpanel">
                    <a class="nav-link pull-right" href="{{ route('DataEntry.Package.Itineraries',['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Location</th>
                            <th scope="col">Locality</th>
                            <th scope="col">Hotel</th>
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
                            <?php $arrGeoLocation = []; ?>
                        @foreach($packageDetail->PackageItinerary as $itinerary)
                            <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{ $itinerary->title }}</td>
                            <td>@if($itinerary->geolocation_id && !in_array($itinerary->geolocation_id, $arrGeoLocation))
                                <?php $arrGeoLocation[] = $itinerary->geolocation_id; ?>
                                @component('DataEntry.Forms.ComponentGeoLocation', ["geoLocation" => $itinerary->geoLocation, "routeName" => null, "routePram" => null])
                                @endcomponent
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($itinerary->location_id)
                                <?php $arrLocation[] = $itinerary->location_id; ?>
                                {{$itinerary->Location->title}}
                                @endif
                            </td>
                            <td>{!! ($itinerary->hotel_id)?'<i class="fa fa-building-o"></i> '.$itinerary->hotel->hotel_name:'-' !!}</td>
                            <td>
                                @if($itinerary->content_id)
                                <script> var content_{{$itinerary->content_id}} = {!!json_encode($itinerary->getContent->content)!!};</script>
                                <i class="fa fa-clone" style="cursor:pointer" onclick='preview_html(content_{{$itinerary->content_id}})'></i>
                                @else
                                -
                                @endif
                            </td>
                            @if($itinerary->content_id && Auth::user()->admin)
                            <td>{{($itinerary->getContent->getUser->name)?$itinerary->getContent->getUser->name:'-'}}</td>
                            <td><small>{{$itinerary->getContent->created_at}}</small></td>
                            <td><small>{{$itinerary->getContent->updated_at}}</small></td>
                            @endif
                            <td>
                                <a href="{{ route('DataEntry.Package.Itineraries',['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => 'edit', 'id' => $itinerary->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                /
                                <a href="{{ route('DataEntry.Package.Itineraries.delete', ['id' => $itinerary->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade{{($tab == "markers")?' show active':''}}" id="package_markers" role="tabpanel">
                    <a class="nav-link pull-right" href="{{ route('DataEntry.Package.Marker',['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => 'options']) }}"><i class="fa fa-edit"></i> Markers</a>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Type</th>
                            <th scope="col">Title</th>
                            <th scope="col">Content</th>
                            <th scope="col">Order</th>
                            <th scope="col">Primary</th>
                            @if(Auth::user()->admin)
                            <th scope="col">User</th>
                            @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($packageDetail->PackageMarker as $marker)
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
                            <td>{{ $marker->order }}</td>
                            <td>{!! ($marker->primary_marker)?'<span class="badge badge-primary">primary</span>':'-' !!}</td>
                            @if(Auth::user()->admin)
                            <td>{{($marker->user_id)?$marker->getUser->name:'-'}}</td>
                            @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade{{($tab == "prices")?' show active':''}}" id="package_prices" role="tabpanel">
                    <a class="nav-link pull-right" href="{{ route('DataEntry.Package.Price',['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
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
                        @foreach($packageDetail->PackagePrice as $price)
                            <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{$price->title}}</td>
                            <td>{{$price->price_start}} {{$price->currency}}</td>
                            <td>{{($price->price_end)?$price->price_end.' '.$price->currency:'-'}}</td>
                            <td>{{ ($price->discount_percent)?$price->discount_percent.'%':'-' }}</td>
                            @if(Auth::user()->admin)
                            <td>{{($price->user_id)?$price->getUser->name:'-'}}</td>
                            <td><small>{{$price->created_at}}</small></td>
                            <td><small>{{$price->updated_at}}</small></td>
                            <td>
                                <a href="{{ route('DataEntry.Package.Price',['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => 'edit', 'id' => $price->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                /
                                <a href="{{ route('DataEntry.Package.Price.delete', ['id' => $price->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                            </td>
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
@endsection
@push('title')
<title>{{$package->title}} | {{(($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only')}}</title>
@endpush