@if($operation == "edit")
<form action="{{ route('DataEntry.Facilities.edit') }}" method="post">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="id" value="{{$facility->id}}">
@else
<form action="{{ route('DataEntry.Facilities.add') }}" method="post">
<?php $facility = null; ?>
@endif
    @csrf
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-text-width"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Title" tabindex="1" name="title" value="{{ old("title", (($facility)?$facility->title:'')) }}">
            <select class="custom-select" tabindex="2" name="facility_type">
                <option value="hotel"{{(old("facility_type", (($facility)?$facility->type:'')) == "hotel")?' selected':''}}>Hotel Facility</option>
                <option value="room"{{(old("facility_type", (($facility)?$facility->type:'')) == "room")?' selected':''}}>Hotel's Room Facility</option>
            </select>
        </div>
        <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-pencil"></i></span>
        </div>
        <input type="text" class="form-control" placeholder="Description" tabindex="3" name="facility_content" value="{{ old("facility_content", (($facility)?$facility->content:'')) }}">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" tabindex="4">
            </button>
            <div class="dropdown-menu">
            <a class="dropdown-item struct_content" href="#">Add / Remove Content</a>
            </div>
        </div>
        </div>
        <div id="struct_content_form_group">
                @if(old("content_type", (($facility && $facility->content_id)?true:'')))
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-gears"></i></span>
                    </div>
                    <select name="content_type" class="custom-select struct_content_type" tabindex="5">
                        <option value="text"{{(old("content_type", (($facility && $facility->getContent)?$facility->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                        <option value="html"{{(old("content_type", (($facility && $facility->getContent)?$facility->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                        <option value="blade"{{(old("content_type", (($facility && $facility->getContent)?$facility->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="..." tabindex="6" style="height:300px">{{ old("content", (($facility && $facility->getContent)?$facility->getContent->content:'')) }}</textarea>
                </div>
                @endif
        </div>
        <div class="d-flex justify-content-end">
            @if(old("content_type", (($facility && $facility->content_id)?true:'')))
            <button type="submit" class="btn btn-success submit_button" tabindex="7">Save</button>
            @else
            <button type="submit" class="btn btn-success submit_button" tabindex="5">Save</button>
            @endif
        </div>
</form>