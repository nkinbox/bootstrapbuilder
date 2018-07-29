@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">Loop Sources</h2>
        <h6 class="card-subtitle mb-2 text-danger">Data sensitive. Only for SKILLED DEVELOPER!</h6>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('Loopsource.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$loopsource->id}}">
            @else
            <form action="{{ route('Loopsource.add') }}" method="post">
            @endif
            @csrf
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Title</span>
                </div>
                <input type="text" class="form-control" name="title" placeholder="Description of QUERY" value="{{ old("title", (($loopsource)?$loopsource->title:'')) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">DB Vars</span>
                </div>
                <input type="text" class="form-control" name="database_variables" placeholder="eg 1.2.3" value="{{ old("database_variables", (($loopsource)?$loopsource->database_variables:'')) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Object Query</span>
                </div>
                <input type="text" class="form-control" name="object_query" placeholder='where("","")->get()' value="{{ old("object_query", (($loopsource)?$loopsource->object_query:'')) }}">
                <div class="input-group-append ml-1">
                    <span class="input-group-text">Relation</span>
                    <select class="custom-select" name="relation">
                        <option value="">Model Query</option>
                        <option value="0"{{ (old("relation", (($loopsource)?$loopsource->relation:'')) == "0")?' selected':'' }}>First Object</option>
                        <option value="1"{{ (old("relation", (($loopsource)?$loopsource->relation:'')) == "1")?' selected':'' }}>Latest Object</option>
                    </select>
                </div>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Property Query</span>
                </div>
                <input type="text" class="form-control" name="property_query" placeholder='where("","")->get()' value="{{ old("property_query", (($loopsource)?$loopsource->property_query:'')) }}">
            </div>   
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Variable</span>
                </div>
                <input type="text" class="form-control" name="variables" placeholder='{"url":{"var1":"key1"}, "loop":{"val2":"1.2.3|first"}}' value="{{ old("variables", (($loopsource)?$loopsource->variables:'')) }}">
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Loop Source</title>
@endpush