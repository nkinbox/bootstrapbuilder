@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">Labels Tags Categories</h2>
        <h6 class="card-subtitle mb-2 text-muted">{{$package->title}} | {{ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))}}</h6>
        <div class="card-text">
                <form action="{{ route('DataEntry.Package.Marker.options') }}" method="post">
                    <input type="hidden" name="package_detail_id" value="{{$packageDetail->id}}">
                    <input type="hidden" name="package_id" value="{{$package->id}}">
                    @csrf
                    @php
                    $markers_type = App\Models\DataMarker::select('type')->where('category', 'packg')->groupBy('type')->orderBy('type')->get();
                    //$markers_type = App\Models\DataMarker::where('category', 'packg')->groupBy('type')->get();
                    @endphp
                    @foreach($markers_type as $marker_type)
                    <div class="card my-2">
                            <h4 class="card-header">
                                    {{$marker_type->type}}
                            </h4>
                    <div class="card-body">
                    <div class="card-text">
                    @php
                    $markers = App\Models\DataMarker::where(["category" => "packg", "type" => $marker_type->type])->get();
                    $count = count($markers);
                    @endphp
                    @foreach($markers as $marker)
                    <?php
                    $checked = false;
                    $primary = 0;
                    $order = 0;
                        foreach($packageDetail->PackageMarker as $marked) {
                            if($marked->title == $marker->title && $marker->type == $marked->type) {
                                $checked = true;
                                $primary = $marked->primary_marker;
                                $order = $marked->order;
                                break;
                            }
                        }
                    ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="checkbox" name="marker[{{$marker->id}}]" value="{{$marker->id}}" id="marker_{{$marker->id}}"{{($checked)?' checked':''}}>
                            </div>
                            <div class="input-group-text {{($checked)?'bg-success text-light':'bg-light'}}">
                                {{$marker->title}}
                            </div>
                        </div>
                        <div class="input-group-append">
                        <select class="custom-select" name="order[{{$marker->id}}]">
                            <option value="0">Order</option>
                            @for($i=1; $i<=$count; $i++)
                            <option{{($i == $order)?' selected':''}}>{{$i}}</option>
                            @endfor
                        </select>
                        <select class="custom-select" name="primary[{{$marker->id}}]">
                            <option value="0">Secondary</option>
                            <option value="1"{{($primary)?' selected':''}}>Primary</option>
                        </select>
                        </div>
                    </div>
                    @endforeach
                    </div>
                    </div>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary submit_button">Save</button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection