@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Loopsource',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">Loop Sources</h2>
        <h6 class="card-subtitle mb-2 text-danger">Data sensitive. Only for SKILLED DEVELOPER!</h6>
        <div class="card-text">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Database Variables</th>
                    <th scope="col">Resolved</th>
                    <th scope="col">Object Query</th>
                    <th scope="col">Property Query</th>
                    <th scope="col">Parameter Variables</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loopsource as $ls)
                    <tr>
                    <th scope="row">{{$ls->id}}</th>
                    <td>{!!'<kbd>'.$ls->database_variables.'</kbd>'!!}</td>
                    <td><small><?php
                    $Loopsource = "";
                    $database_variables = explode(".", $ls->database_variables);
                    foreach($database_variables as $variable) {
                        $db_var = \App\Models\DatabaseVariable::find($variable);
                        if($db_var) {
                            $Loopsource .= ((!$Loopsource)?$db_var->object:'').(($db_var->property)?"->".$db_var->property.(($db_var->is_array)?"[]":""):"");
                        } else $Loopsource .= "(error)";
                    }
                    echo $Loopsource;
                    ?></small></td>
                    <td>{!!($ls->object_query)?"<code>".$ls->object_query."</code>":(($ls->relation)?'<small><b>Latest Object</b></small>':'<small><b>First Object</b></small>')!!}</td>
                    <td><code>{{$ls->property_query}}</code></td>
                    <td>{{$ls->variables}}</td>
                    <td>
                        <a href="{{ route('Loopsource', ["operation"=>"edit", "id"=>$ls->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('Loopsource.delete', ['id' => $ls->id]) }}"><i class="fa fa-trash"></i> Delete</a>
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
<title>Loop Source</title>
@endpush