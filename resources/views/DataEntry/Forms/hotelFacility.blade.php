@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{$hotel->hotel_name}} | Facilities</h2>
        <div class="card-text">
            <form action="{{ route('DataEntry.Hotel.Facility.option') }}" method="post">
                <input type="hidden" name="hotel_id" value="{{$hotel->id}}">
                @if($operation == "room")
                <input type="hidden" name="hotel_room_id" value="{{$hotelRoom->id}}">
                @endif
                <input type="hidden" name="operation" value="{{$operation}}">
                @csrf
                <div class="card my-2">
                    <h4 class="card-header">
                        {{($operation == "hotel")?'Hotel':'Hotel Room'}} Facilities
                    </h4>
                <div class="card-body">
                <div class="card-text">
                @php
                $facilities = App\Models\DataFacility::where(["type" => $operation])->get();
                $count = count($facilities);
                @endphp
                @foreach($facilities as $facility)
                <?php
                $checked = false;
                $previousFacilities = (($operation == "hotel")?$hotel->HotelFacility:$hotelRoom->RoomFacility);
                foreach($previousFacilities as $previousFacility) {
                    if($previousFacility->title == $facility->title && $previousFacility->type == $facility->type) {
                        $checked = true;
                        break;
                    }
                }
                ?>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="checkbox" name="facility[{{$facility->id}}]" value="{{$facility->id}}" id="facility_{{$facility->id}}" {{($checked)?' checked':''}}>
                        </div>
                        <label for="facility_{{$facility->id}}" class="input-group-text {{($checked)?'bg-success text-light':'bg-light'}}">
                            {{$facility->title}}
                        </label>
                    </div>
                </div>
                @endforeach
                </div>
                </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary submit_button">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{$hotel->hotel_name}} | Facilities</title>
@endpush