@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Database',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">DataBase Variables</h2>
        <h6 class="card-subtitle mb-2 text-danger">Data sensitive. Only for SKILLED DEVELOPER!</h6>
        <div class="card-text">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Object</th>
                    <th scope="col">Property</th>
                    <th scope="col">Related</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($database as $row)
                    <tr>
                    <th scope="row">{{$row->id}}</th>
                    <td>{!!(!$row->property)?'<kbd>'.$row->object.'</kbd>':$row->object!!}</td>
                    <td>{{$row->property}}</td>
                    <td>{!!($row->related_to)?'<kbd>'.$row->Related->object.'</kbd> ':''!!}{!!(($row->is_array)?' <code>Array</code>':'')!!}</td>
                    <td>
                        <a href="{{ route('Database', ["operation"=>"edit", "id"=>$row->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('Database.delete', ['id' => $row->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>DataBase Variables</title>
@endpush