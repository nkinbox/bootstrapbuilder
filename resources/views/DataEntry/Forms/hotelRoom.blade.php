@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{$hotel->hotel_name}} | {{ucwords($operation)}} Room</h2>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('DataEntry.Hotel.Room.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$hotelRoom->id}}">
            @else
            <form action="{{ route('DataEntry.Hotel.Room.add') }}" method="post">
            @endif
                <input type="hidden" name="hotel_id" value="{{$hotel->id}}">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Room Type" tabindex="1" name="title" value="{{ old("title", (($hotelRoom)?$hotelRoom->title:'')) }}">
                </div>
                <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-money"></i></span>
                    <select class="custom-select" tabindex="2" name="currency">
                        <option value="INR"{{(old("currency", (($hotelRoom)?$hotelRoom->currency:'')) == "INR")?' selected':''}}>INR</option>
                        <option value="USD"{{(old("currency", (($hotelRoom)?$hotelRoom->currency:'')) == "USD")?' selected':''}}>USD</option>
                    </select>
                </div>
                <input type="number" class="form-control" placeholder="Price Start" tabindex="3" name="price_start" value="{{ old("price_start", (($hotelRoom)?$hotelRoom->price_start:'')) }}">
                <input type="number" class="form-control" placeholder="Price End" tabindex="4" name="price_end" value="{{ old("price_end", (($hotelRoom)?$hotelRoom->price_end:'')) }}">
                <input type="number" class="form-control" placeholder="Discount %" tabindex="5" name="discount_percent" value="{{ old("discount_percent", (($hotelRoom)?$hotelRoom->discount_percent:'')) }}">
                <input type="number" class="form-control" placeholder="Person" tabindex="6" name="person" value="{{ old("person", (($hotelRoom)?$hotelRoom->person:'')) }}">
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success submit_button" tabindex="7">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{$hotel->hotel_name}} | {{ucwords($operation)}} Room</title>
@endpush