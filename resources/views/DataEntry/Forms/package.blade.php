@if($operation == "edit")
<form action="{{ route('DataEntry.Package.edit') }}" method="post">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="id" value="{{$package->id}}">
@else
<form action="{{ route('DataEntry.Package.add') }}" method="post">
<?php $package = null; ?>
@endif
    @csrf
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-text-width"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Title" tabindex="1" name="title" value="{{ old("title", (($package)?$package->title:'')) }}">
        </div>
        <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-pencil"></i></span>
        </div>
        <input type="text" class="form-control" placeholder="Description" tabindex="2" name="package_content" value="{{ old("package_content", (($package)?$package->content:'')) }}">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" tabindex="3">
            </button>
            <div class="dropdown-menu">
            <a class="dropdown-item struct_content" href="#">Add / Remove Content</a>
            </div>
        </div>
        </div>
        <div id="struct_content_form_group">
                @if(old("content_type", (($package && $package->content_id)?true:'')))
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-gears"></i></span>
                    </div>
                    <select name="content_type" class="custom-select struct_content_type" tabindex="4">
                        <option value="text"{{(old("content_type", (($package && $package->getContent)?$package->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                        <option value="html"{{(old("content_type", (($package && $package->getContent)?$package->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                        <option value="blade"{{(old("content_type", (($package && $package->getContent)?$package->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="..." tabindex="5" style="height:300px">{{ old("content", (($package && $package->getContent)?$package->getContent->content:'')) }}</textarea>
                </div>
                @endif
        </div>
        <div class="d-flex justify-content-end">
            @if(old("content_type", (($package && $package->content_id)?true:'')))
            <button type="submit" class="btn btn-success submit_button" tabindex="6">Save</button>
            @else
            <button type="submit" class="btn btn-success submit_button" tabindex="4">Save</button>
            @endif
        </div>
</form>