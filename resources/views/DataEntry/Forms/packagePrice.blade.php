@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{ucwords($operation)}} Price</h2>
        <h6 class="card-subtitle mb-2 text-muted">{{$package->title}} | {{ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))}}</h6>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('DataEntry.Package.Price.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$price->id}}">
            @else
            <form action="{{ route('DataEntry.Package.Price.add') }}" method="post">
            <?php $price = null; ?>
            @endif
                <input type="hidden" name="package_id" value="{{$package->id}}">
                <input type="hidden" name="package_detail_id" value="{{$packageDetail->id}}">
                @csrf
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Title" tabindex="1" name="title" value="{{ old("title", (($price)?$price->title:'')) }}">
                    </div>
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-money"></i></span>
                        <select class="custom-select" tabindex="2" name="currency">
                            <option value="INR"{{(old("currency", (($price)?$price->currency:'')) == "INR")?' selected':''}}>INR</option>
                            <option value="USD"{{(old("currency", (($price)?$price->currency:'')) == "USD")?' selected':''}}>USD</option>
                        </select>
                    </div>
                    <input type="number" class="form-control" placeholder="Price Start" tabindex="3" name="price_start" value="{{ old("price_start", (($price)?$price->price_start:'')) }}">
                    <input type="number" class="form-control" placeholder="Price End" tabindex="4" name="price_end" value="{{ old("price_end", (($price)?$price->price_end:'')) }}">
                    <input type="number" class="form-control" placeholder="Discount %" tabindex="5" name="discount_percent" value="{{ old("discount_percent", (($price)?$price->discount_percent:'')) }}">
                    <input type="number" class="form-control" placeholder="Person" tabindex="6" name="person" value="{{ old("person", (($price)?$price->person:'')) }}">
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
<title>{{$package->title}} | {{ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))}}</title>
@endpush