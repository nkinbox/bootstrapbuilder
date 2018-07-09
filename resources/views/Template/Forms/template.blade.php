@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{(($operation == "add")?'Create Template':'Edit Template | ' .$template->title)}}</h2>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('Template.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$template->id}}">
            @else
            <form action="{{ route('Template.add') }}" method="post">
            @endif
                @csrf
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Template Name" tabindex="1" name="title" value="{{ old("title", (($template)?$template->title:'')) }}">
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Global JavaScript</h5>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-code"></i></span>
                                </div>
                                <textarea class="form-control" name="js_content" placeholder="..." tabindex="2" style="height:300px">{{ old("js_content", (($template && $template->getScript)?$template->getScript->content:'')) }}</textarea>
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
                                <textarea class="form-control" name="css_content" placeholder="..." tabindex="3" style="height:300px">{{ old("css_content", (($template && $template->getCSS)?$template->getCSS->content:'')) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success submit_button" tabindex="4">Save</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{(($operation == "add")?'Create Template':'Edit Template | ' .$template->title)}}</title>
@endpush