@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{$hotel->hotel_name}} | {{ucwords($operation)}} Contact</h2>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('DataEntry.Hotel.Contact.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$contact->id}}">
            @else
            <form action="{{ route('DataEntry.Hotel.Contact.add') }}" method="post">
            @endif
                <input type="hidden" name="hotel_id" value="{{$hotel->id}}">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-address-book-o"></i></span>
                        <select class="custom-select" tabindex="1" name="type">
                            <option value="email"{{(old("type", (($contact)?$contact->type:'')) == "email")?' selected':''}}>Email</option>
                            <option value="mobile"{{(old("type", (($contact)?$contact->type:'')) == "mobile")?' selected':''}}>Mobile</option>
                            <option value="landline"{{(old("type", (($contact)?$contact->type:'')) == "landline")?' selected':''}}>Landline</option>
                            <option value="website"{{(old("type", (($contact)?$contact->type:'')) == "website")?' selected':''}}>Website</option>
                        </select>
                    </div>
                    <input type="text" class="form-control" placeholder="Contact Information" tabindex="2" name="content" value="{{ old("content", (($contact)?$contact->content:'')) }}">
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success submit_button" tabindex="3">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{$hotel->hotel_name}} | {{ucwords($operation)}} Contact</title>
@endpush