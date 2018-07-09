@extends('Template.layout')
@section('card')
<div class="card">
    <h2 class="card-header">{{$template->title}} | {{(($operation == "add")?'Create Page':'Edit Page | ' .$page->title)}}</h2>
    <div class="card-body">
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('Template.Page.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$page->id}}">
            @else
            <form action="{{ route('Template.Page.add') }}" method="post">
            @endif
                @csrf
                <input type="hidden" name="template_id" value="{{$template->id}}">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-link"></i></span>
                            <input type="text" class="form-control" placeholder="Page Name" tabindex="1" name="title" value="{{ old("title", (($page)?$page->title:'')) }}">
                        </div>
                        <input type="text" class="form-control" placeholder="Page URL" tabindex="2" name="url" value="{{ old("url", (($page)?$page->url:'')) }}">
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Meta Data</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code"></i></span>
                                </div>
                                <textarea class="form-control" name="meta_content" placeholder="..." tabindex="3" style="height:300px">{{ old("meta_content", (($page && $page->getMetadata)?$page->getMetadata->content:'')) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Global JavaScript</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code"></i></span>
                                </div>
                                <textarea class="form-control" name="js_content" placeholder="..." tabindex="4" style="height:300px">{{ old("js_content", (($page && $page->getScript)?$page->getScript->content:'')) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Global CSS</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code"></i></span>
                                </div>
                                <textarea class="form-control" name="css_content" placeholder="..." tabindex="5" style="height:300px">{{ old("css_content", (($page && $page->getCSS)?$page->getCSS->content:'')) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success submit_button" tabindex="5">Save</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{(($operation == "add")?'Create Template':'Edit Template | ' .$template->title)}}</title>
@endpush