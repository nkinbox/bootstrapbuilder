@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Template.index',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Create Template</a>
        <h2 class="card-title">Templates</h2>
        <div class="card-text">
            <table class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Pages</th>
                    <th scope="col">Script</th>
                    <th scope="col">CSS</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Updated at</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td><a href="{{ route('Template.Page', ['template_id' => $template->id]) }}">{{$template->title}}</a></td>
                            <td>{{$template->Pages->count()}}</td>
                            <td>
                                @if($template->script_id)
                                <script> var script_content_{{$template->script_id}} = {!!json_encode($template->getScript->content)!!};</script>
                                <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(script_content_{{$template->script_id}})'></i>
                                @endif
                            </td>
                            <td>
                                @if($template->css_id)
                                <script> var css_content_{{$template->css_id}} = {!!json_encode($template->getCSS->content)!!};</script>
                                <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(css_content_{{$template->css_id}})'></i>
                                @endif
                            </td>
                            @if(Auth::user()->admin)
                            <td>{{($template->user_id)?$template->getUser->name:'-'}}</td>
                            <td><small>{{$template->created_at}}</small></td>
                            <td><small>{{$template->updated_at}}</small></td>
                            @endif
                            <td>
                                <a href="{{ route('Template.index', ["operation"=>"edit", "id"=>$template->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                /
                                <a href="{{ route('Template.delete', ['id' => $template->id]) }}"><i class="fa fa-trash"></i> Delete</a>
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
<title>Template Panel</title>
@endpush