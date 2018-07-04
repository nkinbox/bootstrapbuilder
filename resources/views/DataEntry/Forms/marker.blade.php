@if($operation == "edit")
<form action="{{ route('DataEntry.Markers.edit') }}" method="post">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="id" value="{{$marker->id}}">
@else
<form action="{{ route('DataEntry.Markers.add') }}" method="post">
<?php $marker = null; ?>
@endif
    @csrf
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-text-width"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Title" tabindex="1" name="title" value="{{ old("title", (($marker)?$marker->title:'')) }}">
            <select class="custom-select" tabindex="2" name="category">
                <option value="hotel"{{(old("category", (($marker)?$marker->category:'')) == "hotel")?' selected':''}}>Hotel Marker</option>
                <option value="packg"{{(old("category", (($marker)?$marker->category:'')) == "packg")?' selected':''}}>Package Marker</option>
            </select>
            <select class="custom-select" tabindex="3" name="type">
                <option value="label"{{(old("type", (($marker)?$marker->type:'')) == "label")?' selected':''}}>Label</option>
                <option value="category"{{(old("type", (($marker)?$marker->type:'')) == "category")?' selected':''}}>Category</option>
                <option value="tag"{{(old("type", (($marker)?$marker->type:'')) == "tag")?' selected':''}}>Tag</option>
                <option value="inclusions"{{(old("type", (($marker)?$marker->type:'')) == "inclusions")?' selected':''}}>Inclusions</option>
                <option value="exclusions"{{(old("type", (($marker)?$marker->type:'')) == "exclusions")?' selected':''}}>Exclusions</option>
                <option value="activity"{{(old("type", (($marker)?$marker->type:'')) == "activity")?' selected':''}}>Activity</option>
                <option value="theme"{{(old("type", (($marker)?$marker->type:'')) == "theme")?' selected':''}}>Theme</option>
            </select>
        </div>
        <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-pencil"></i></span>
        </div>
        <input type="text" class="form-control" placeholder="Description" tabindex="4" name="marker_content" value="{{ old("marker_content", (($marker)?$marker->content:'')) }}">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" tabindex="5">
            </button>
            <div class="dropdown-menu">
            <a class="dropdown-item struct_content" href="#">Add / Remove Content</a>
            </div>
        </div>
        </div>
        <div id="struct_content_form_group">
                @if(old("content_type", (($marker && $marker->content_id)?true:'')))
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-gears"></i></span>
                    </div>
                    <select name="content_type" class="custom-select struct_content_type" tabindex="6">
                        <option value="text"{{(old("content_type", (($marker && $marker->getContent)?$marker->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                        <option value="html"{{(old("content_type", (($marker && $marker->getContent)?$marker->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                        <option value="blade"{{(old("content_type", (($marker && $marker->getContent)?$marker->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="..." tabindex="7" style="height:300px">{{ old("content", (($marker && $marker->getContent)?$marker->getContent->content:'')) }}</textarea>
                </div>
                @endif
        </div>
        <div class="d-flex justify-content-end">
            @if(old("content_type", (($marker && $marker->content_id)?true:'')))
            <button type="submit" class="btn btn-success submit_button" tabindex="8">Save</button>
            @else
            <button type="submit" class="btn btn-success submit_button" tabindex="6">Save</button>
            @endif
        </div>
</form>