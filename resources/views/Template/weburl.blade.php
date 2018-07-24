@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('WebUrl',['page_id' => $page->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New URL</a>
        <h2 class="card-title">Web URLs</h2>
        <h6 class="card-subtitle mb-2">{{$template->title}} | {{$page->title}}</h6>
        <div class="card-text">
            {{$weburl->links()}}
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">URL</th>
                    <th scope="col">GeoLocation</th>
                    <th scope="col">Regex</th>
                    <th scope="col">Matches</th>
                    <th scope="col">URL Variables</th>
                    <th scope="col">URL Builder</th>
                    <th scope="col">MetaData</th>
                    <th scope="col">Script</th>
                    <th scope="col">CSS</th>
                    <th scope="col">User</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weburl as $row)
                    <tr>
                    <th scope="row">{{$row->id}}</th>
                    <td><small>{{$row->url}}</small></td>
                    <td>@if($row->geolocation)<kbd>{{$row->geolocation}}</kbd>@else GLOBAL @endif</td>
                    <td>{!!($row->regex)?'<code>'.$row->regex.'</code>':''!!}</td>
                    <td>{!!($row->matches)?'<code>'.$row->matches.'</code>':''!!}</td>
                    <td>{!!($row->url_variables)?'<code>'.$row->url_variables.'</code>':''!!}</td>
                    <td>{!!($row->url_builder)?'<code>'.$row->url_builder.'</code>':''!!}</td>
                    <td>
                        @if($row->meta_id)
                        <script> var meta_content_{{$row->meta_id}} = {!!json_encode($row->getMetadata->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(meta_content_{{$row->meta_id}})'></i>
                        @endif
                    </td>
                    <td>
                        @if($row->script_id)
                        <script> var script_content_{{$row->script_id}} = {!!json_encode($row->getScript->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(script_content_{{$row->script_id}})'></i>
                        @endif
                    </td>
                    <td>
                        @if($row->css_id)
                        <script> var css_content_{{$row->css_id}} = {!!json_encode($row->getCSS->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(css_content_{{$row->css_id}})'></i>
                        @endif
                    </td>
                    <td>@if($row->user_id) {{$row->getUser->name}} @endif</td>
                    <td>
                        <a href="{{ route('WebUrl', ['page_id' => $page->id, "operation"=>"edit", "id" => $row->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('WebUrl.delete', ['page_id' => $page->id, 'id' => $row->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$weburl->links()}}
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Web URLs | {{$template->title}} | {{$page->title}}</title>
@endpush