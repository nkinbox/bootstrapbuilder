@include('DataEntry.Forms.geolocation')
@if($operation == "edit")
<form action="{{ route('DataEntry.Transports.edit') }}" method="post">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="id" value="{{$transport->id}}">
@else
<form action="{{ route('DataEntry.Transports.add') }}" method="post">
<?php $transport = null; ?>
@endif
    @csrf
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Latitude" tabindex="1" name="latitude" value="{{ old("latitude", (($transport)?$transport->latitude:'')) }}">
            <input type="text" class="form-control" placeholder="Longitude" tabindex="2" name="longitude" value="{{ old("longitude", (($transport)?$transport->longitude:'')) }}">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-text-width"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Display Name" tabindex="3" name="title" value="{{ old("title", (($transport)?$transport->title:'')) }}">
            <input type="text" class="form-control" placeholder="Category" tabindex="4" name="category" value="{{ old("category", (($transport)?$transport->category:'')) }}" list="transport_category">
            <datalist id="transport_category">
                <option value="International">
                <option value="Domestic">
                <option value="ISBT">
                <option value="Local Taxi Stand">
                <option value="Local Bus Stand">
                <option value="Railway Junction">
                <option value="Railway Terminal">
            </datalist>
            <div class="input-group-append">
                <select class="custom-select" tabindex="5" name="type">
                    <option value="Airport"{{(old("type", (($transport)?$transport->type:'')) == "Airport")?' selected':''}}>Airport</option>
                    <option value="Busstand"{{(old("type", (($transport)?$transport->type:'')) == "Busstand")?' selected':''}}>Bus stand</option>
                    <option value="Railwaystation"{{(old("type", (($transport)?$transport->type:'')) == "Railwaystation")?' selected':''}}>Railway station</option>
                    <option value="Taxistand"{{(old("type", (($transport)?$transport->type:'')) == "Taxistand")?' selected':''}}>Taxi stand</option>
                    <option value="Cyclestand"{{(old("type", (($transport)?$transport->type:'')) == "Cyclestand")?' selected':''}}>Cycle stand</option>
                </select>
                <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" tabindex="6">
                </button>
                <div class="dropdown-menu">
                <a class="dropdown-item struct_content" href="#">Add / Remove Content</a>
                </div>
            </div>
        </div>
        <div id="struct_content_form_group">
                @if(old("content_type", (($transport && $transport->content_id)?true:'')))
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-gears"></i></span>
                    </div>
                    <select name="content_type" class="custom-select struct_content_type" tabindex="7">
                        <option value="text"{{(old("content_type", (($transport && $transport->getContent)?$transport->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                        <option value="html"{{(old("content_type", (($transport && $transport->getContent)?$transport->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                        <option value="blade"{{(old("content_type", (($transport && $transport->getContent)?$transport->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="..." tabindex="8" style="height:300px">{{ old("content", (($transport && $transport->getContent)?$transport->getContent->content:'')) }}</textarea>
                </div>
                @endif
        </div>
        <div class="d-flex justify-content-end">
            @if(old("content_type", (($transport && $transport->content_id)?true:'')))
            <button type="submit" class="btn btn-success submit_button" tabindex="9">Save</button>
            @else
            <button type="submit" class="btn btn-success submit_button" tabindex="7">Save</button>
            @endif
        </div>
</form>