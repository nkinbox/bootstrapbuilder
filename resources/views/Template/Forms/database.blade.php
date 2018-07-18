@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">DataBase Variables</h2>
        <h6 class="card-subtitle mb-2 text-danger">Data sensitive. Only for SKILLED DEVELOPER!</h6>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('Database.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$database->id}}">
            @else
            <form action="{{ route('Database.add') }}" method="post">
            @endif
            @csrf
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Object</span>
                </div>
                <input type="text" class="form-control" name="object" placeholder="eg User" value="{{ old("object", (($database)?$database->object:'')) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Property</span>
                </div>
                <input type="text" class="form-control" name="property" placeholder="eg name" value="{{ old("property", (($database)?$database->property:'')) }}">
                <select class="custom-select" name="is_array">
                    <option value="0"{{ (old("is_array", (($database)?$database->is_array:'')) == 0)?' selected':'' }}>Model | Column | hasOne | belongsTo</option>
                    <option value="1"{{ (old("is_array", (($database)?$database->is_array:'')) == 1)?' selected':'' }}>hasMany | belongsToMany | hasManyThough</option>
                </select>
                <select class="custom-select" name="related_to">
                    <option value="0">Direct Property</option>
                    @foreach(App\Models\DatabaseVariable::select('id', 'object')->whereNull('property')->get() as $object)
                    <option value="{{$object->id}}"{{ (old("related_to", (($database)?$database->related_to:'')) == $object->id)?' selected':'' }}>{{$object->object}}</option>
                    @endforeach
                </select>
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