@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{$hotel->hotel_name}} | Labels Tags Categories</h2>
        <div class="card-text">
            <form action="{{ route('DataEntry.Hotel.Markers') }}" method="post">
                <input type="hidden" name="hotel_id" value="{{$hotel->id}}">
                @csrf
                @php
                $markers_type = App\Models\DataMarker::select('type')->where('category', 'hotel')->groupBy('type')->orderBy('type')->get();
                @endphp
                @foreach($markers_type as $marker_type)
                <div class="card my-2">
                        <h4 class="card-header">
                                {{$marker_type->type}}
                        </h4>
                <div class="card-body">
                <div class="card-text">
                @php
                $markers = App\Models\DataMarker::where(["category" => "hotel", "type" => $marker_type->type])->get();
                $count = count($markers);
                @endphp
                @foreach($markers as $marker)
                <?php
                $checked = false;
                $primary = 0;
                $order = 0;
                    foreach($hotel->HotelMarker as $marked) {
                        if($marked->id == $marker->id) {
                            $checked = true;
                            $primary = $marked->pivot->primary_marker;
                            $order = $marked->pivot->order;
                            break;
                        }
                    }
                ?>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="checkbox" name="marker[{{$marker->id}}]" value="{{$marker->id}}" id="marker_{{$marker->id}}"{{($checked)?' checked':''}}>
                        </div>
                        <label for="marker_{{$marker->id}}" class="input-group-text {{($checked)?'bg-success text-light':'bg-light'}}">
                            {{$marker->title}}
                        </label>
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
@push('title')
<title>{{$hotel->hotel_name}} | Labels Tags Categories</title>
@endpush