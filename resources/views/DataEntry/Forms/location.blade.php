@include('DataEntry.Forms.geolocation')
@if($operation == "edit")
<form action="{{ route('DataEntry.Locations.edit') }}" method="post">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="id" value="{{$location->id}}">
@else
<form action="{{ route('DataEntry.Locations.add') }}" method="post">
<?php $location = null; ?>
@endif
    @csrf
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Latitude" tabindex="1" name="latitude" value="{{ old("latitude", (($location)?$location->latitude:'')) }}">
            <input type="text" class="form-control" placeholder="Longitude" tabindex="2" name="longitude" value="{{ old("longitude", (($location)?$location->longitude:'')) }}">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-text-width"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Location Name" tabindex="3" name="title" value="{{ old("title", (($location)?$location->title:'')) }}">
            <select class="custom-select" tabindex="4" name="type">
                <option value="landmark"{{(old("type", (($location)?$location->type:'')) == "landmark")?' selected':''}}>Landmark</option>
                <option value="attraction"{{(old("type", (($location)?$location->type:'')) == "attraction")?' selected':''}}>Attraction</option>
                <option value="locality"{{(old("type", (($location)?$location->type:'')) == "locality")?' selected':''}}>Locality</option>
            </select>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" tabindex="5">
                </button>
                <div class="dropdown-menu">
                <a class="dropdown-item struct_content" href="#">Add / Remove Content</a>
                </div>
            </div>
        </div>
        <div id="struct_content_form_group">
                @if(old("content_type", (($location && $location->content_id)?true:'')))
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-gears"></i></span>
                    </div>
                    <select name="content_type" class="custom-select struct_content_type" tabindex="6">
                        <option value="text"{{(old("content_type", (($location && $location->getContent)?$location->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                        <option value="html"{{(old("content_type", (($location && $location->getContent)?$location->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                        <option value="blade"{{(old("content_type", (($location && $location->getContent)?$location->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="..." tabindex="7" style="height:300px">{{ old("content", (($location && $location->getContent)?$location->getContent->content:'')) }}</textarea>
                </div>
                @endif
        </div>
        <div class="d-flex justify-content-end">
            @if(old("content_type", (($location && $location->content_id)?true:'')))
            <button type="submit" class="btn btn-success submit_button" tabindex="8">Save</button>
            @else
            <button type="submit" class="btn btn-success submit_button" tabindex="6">Save</button>
            @endif
        </div>
</form>