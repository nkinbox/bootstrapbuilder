@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{$template->title}} | Global Variables</h2>
        <h6 class="card-subtitle mb-2 text-danger">Data sensitive. Only for SKILLED DEVELOPER!</h6>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('Variable.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$variable->id}}">
            @else
            <form action="{{ route('Variable.add') }}" method="post">
            @endif
            <input type="hidden" name="template_id" value="{{$template->id}}">
            @csrf
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Variable Name</span>
                </div>
                <input type="text" class="form-control" name="variable_name" placeholder="eg Variable Name" value="{{ old("variable_name", (($variable)?$variable->variable_name:'')) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Value</span>
                </div>
                <input type="text" class="form-control" name="evaluate" value="{{ old("evaluate", (($variable)?$variable->evaluate:'')) }}">
                <select class="custom-select" name="is_php">
                    <option value="0"{{ (old("is_php", (($variable)?$variable->is_php:'')) == 0)?' selected':'' }}>Not PHP</option>
                    <option value="1"{{ (old("is_php", (($variable)?$variable->is_php:'')) == 1)?' selected':'' }}>Is PHP</option>
                </select>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-code"></i></span>
                </div>
                <textarea class="form-control" name="php_code" placeholder="..." style="height:200px">{{ old("php_code", (($variable)?$variable->php_code:'')) }}</textarea>
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
<title>DataBase Variables</title>
@endpush