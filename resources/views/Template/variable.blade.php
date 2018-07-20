@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Variable',['template_id' => $template->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">{{$template->title}} |Global Variables</h2>
        <h6 class="card-subtitle mb-2 text-danger">Data sensitive. Only for SKILLED DEVELOPER!</h6>
        <div class="card-text">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Resolves</th>
                    <th scope="col">PHP Code</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($variable as $row)
                    <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td><kbd>{{$row->variable_name}}</kbd></td>
                    <td>{!!(!$row->is_php)?'<i class="fa fa-check-square-o"></i>':''!!} {{$row->evaluate}}</td>
                    <td>{!!($row->is_php)?'<i class="fa fa-check-square-o"></i>':''!!} {{$row->php_code}}</td>
                    <td>
                        <a href="{{ route('Variable', ['template_id' => $template->id, "operation"=>"edit", "id"=>$row->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('Variable.delete', ['template_id' => $template->id, 'id' => $row->id]) }}"><i class="fa fa-trash"></i> Delete</a>
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