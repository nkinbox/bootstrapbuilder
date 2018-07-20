@include('DataEntry.Forms.geolocation')
@if($operation == "edit")
<form action="{{ route('DataEntry.Page.edit') }}" method="post">
    <input type="hidden" name="_method" value="put">
    <input type="hidden" name="id" value="{{$page->id}}">
@else
<form action="{{ route('DataEntry.Page.add') }}" method="post">
@endif
    @csrf
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span id="url" class="input-group-text">URL</span>
        </div>
        <input type="text" class="form-control" placeholder="URL String" tabindex="10" name="url" value="{{ old("url", (($page)?$page->url:'')) }}" readonly>
    </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Website</span>
            </div>
            <select class="custom-select" tabindex="1" name="template_id">
                <option value="0"{{(old("template_id") == $template_id)?' selected':''}}>Unknown</option>
                @foreach(App\Models\Template::where('is_website', 1)->get() as $template_)
                <option value="{{$template_->id}}"{{(old("template_id", $template_id) == $template_->id)?' selected':''}}>{{$template_->title}}</option>
                @endforeach
            </select>
            <div class="input-group-prepend ml-1">
                <span class="input-group-text"><i class="fa fa-globe"></i></span>
            </div>
            <select class="custom-select" tabindex="1" name="geolocation_id">
                <option value="0"{{(old("geolocation_id", (($page)?$page->geolocation_id:'')) == 0)?' selected':''}}>Global</option>
                <option value="1"{{(old("geolocation_id", (($page)?$page->geolocation_id:'')) != 0)?' selected':''}}>Geolocation</option>
            </select>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                @if($template_id)
                <select class="custom-select" tabindex="2" name="page_id">
                    @foreach($template->Pages as $pag)
                    <option value="{{$pag->id}}"{{(old("page_id", (($page)?$page->page_id:'')) == $pag->id)?' selected':''}}>{{$pag->title}}</option>
                    @endforeach
                </select>
                @elseif(old("template_id"))
                <select class="custom-select" tabindex="2" name="page_id">
                    @foreach(App\Models\Page::where('template_id', old("template_id"))->get() as $pag)
                    <option value="{{$pag->id}}"{{(old("page_id") == $pag->id)?' selected':''}}>{{$pag->title}}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="page_id" value="0">
                @endif
                @if($page && $page->broked)
                <select class="custom-select" tabindex="3" name="broked">
                    <option value="1"{{(old("broked", $page->broked) == 1)?' selected':''}}>Broked</option>
                    <option value="0"{{(old("broked", $page->broked) == 0)?' selected':''}}>Fixed</option>
                </select>
                @else
                <input type="hidden" name="broked" value="0">
                @endif
            </div>
            <input type="text" class="form-control" placeholder="Title" tabindex="4" name="title" value="{{ old("title", (($page)?$page->title:'')) }}">
            <div class="input-group-prepend ml-1"><span class="input-group-text"><i class="fa fa-thumb-tack"></i></span></div>
            <input type="text" class="form-control" placeholder="Group Title" tabindex="5" name="group_title" value="{{ old("group_title", (($page)?$page->group_title:'')) }}">
            <div class="input-group-append">
            <select class="custom-select" tabindex="6" name="type">
                <option value="sitemap"{{(old("type", (($page)?$page->type:'')) == "sitemap")?' selected':''}}>Sitemap</option>
                <option value="header"{{(old("type", (($page)?$page->type:'')) == "header")?' selected':''}}>Header</option>
                <option value="footer"{{(old("type", (($page)?$page->type:'')) == "footer")?' selected':''}}>Footer</option>
                <option value="other"{{(old("type", (($page)?$page->type:'')) == "other")?' selected':''}}>Other</option>
            </select>
            </div>
        </div>
        @if($operation == "edit")
        <div id="struct_content_form_group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-gears"></i></span>
                </div>
                <select name="content_type" class="custom-select" tabindex="7">
                    <option value="blade" selected>HTML Blade Content</option>
                </select>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i {!!($page && $page->page_id && $page->content_id)?'id="bladeEditor" ':''!!}class="fa fa-code"></i></span>
                </div>
                <textarea class="form-control" name="content" placeholder="..." tabindex="8" style="height:300px">{{ old("content", (($page && $page->getContent)?$page->getContent->content:'')) }}</textarea>
            </div>
        </div>
        @if($page && $page->page_id && $page->content_id)
        @push('scripts')
        <script>
            $("#bladeEditor").css("cursor", "pointer");
            $("#bladeEditor").click(function() {
                window.open("{{route('DataEntry.Blade', ["page_id" => $page->page_id, "content_id" => $page->content_id])}}", "BladeEditor", "width=1000,height=600");
            });
        </script>
        @endpush
        @endif
        @endif
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success submit_button" tabindex="9">Save</button>
        </div>
</form>
@push('scripts')
<script>
    $('input[name="title"]').change(function(){
        url = $(this).val();
        url = url.trim().replace(/\s+/g, '-').toLowerCase();
        $('input[name="url"]').val(url);
    });
    $('#url').click(function(){
        $('input[name="url"]').prop("readonly", !$('input[name="url"]').is('[readonly]'));
    });
</script>
@endpush