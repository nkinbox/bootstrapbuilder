@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Template.Page',['template_id' => $template->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> New Page</a>
        <h2 class="card-title">Template | {{$template->title}}</h2>
        <div class="card-text">
            <table class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Pages</th>
                    <th scope="col">Components</th>
                    <th scope="col">Script</th>
                    <th scope="col">CSS</th>
                    <th scope="col">Variables</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Updated at</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <th scope="row"><i class="fa fa-bookmark-o"></i></th>
                            <td>{{$template->title}}</td>
                            <td><a href="{{ route('Template.Page', ['template_id' => $template->id]) }}">{{$template->Pages->count()}}</a></td>
                            <td><a href="{{ route('Template.Component', ['template_id' => $template->id]) }}">{{$template->Components->count()}}</a></td>
                            <td>
                                @if($template->script_id)
                                <script> var content_{{$template->script_id}} = {!!json_encode($template->getScript->content)!!};</script>
                                <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$template->script_id}})'></i>
                                @endif
                            </td>
                            <td>
                                @if($template->css_id)
                                <script> var content_{{$template->css_id}} = {!!json_encode($template->getCSS->content)!!};</script>
                                <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$template->css_id}})'></i>
                                @endif
                            </td>
                            <td><a href="{{ route('Variable', ['template_id' => $template->id]) }}">{{$template->GlobalVariables->count()}}</a></td>
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
                </tbody>
            </table>
            <div class="card">
                <div class="card-header">
                    <form class="pull-right" action="{{ route('mode') }}" method="post">
                        @csrf
                        <input type="hidden" name="template_id" value="{{$template->id}}">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">MODE</span>
                            </div>
                            <select class="custom-select" tabindex="1" name="mode">
                                <option value="guest">GUEST</option>
                                <option value="auth">AUTH</option>
                            </select>
                            <select class="custom-select" tabindex="1" name="country">
                                @foreach(App\Models\GeoLocation::select('country')->groupBy('country')->get() as $country)
                                <option{{(Cookie::get('country') && Cookie::get('country') == $country->country)?' selected':''}}>{{$country->country}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-light border" type="submit" tabindex="-1"><i class="fa fa-thumb-tack"></i></button>
                            </div>
                        </div>
                    </form>
                    <h4>Pages</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">URLs</th>
                            <th scope="col">Components</th>
                            <th scope="col">MetaData</th>
                            <th scope="col">Script</th>
                            <th scope="col">CSS</th>
                            <th scope="col">Variables</th>
                            @if(Auth::user()->admin)
                            <th scope="col">User</th>
                            <th scope="col">Created at</th>
                            <th scope="col">Updated at</th>
                            @endif
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($template->Pages as $page)
                                <tr>
                                    <th scope="row"{!! (!$page->Components->count())?' class="bg-danger text-light"':'' !!}>{{ $loop->iteration }}</th>
                                    <td>
                                        <a href="{{ route('view', ['template_id' => $template->id, 'id' => $page->id]) }}" target="_blank">{{$page->title}}</a>
                                    </td>
                                    <td><a href="{{ route('WebUrl', ['page_id' => $page->id]) }}" target="_blank">{{$page->URLs->count()}}</a></td>
                                    <td><a href="{{ route('Template.Page.Component', ['page_id' => $page->id]) }}">{{$page->Components->count()}}</a></td>
                                    <td>
                                        @if($page->meta_id)
                                        <script> var meta_content_{{$page->meta_id}} = {!!json_encode($page->getMetadata->content)!!};</script>
                                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(meta_content_{{$page->meta_id}})'></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($page->script_id)
                                        <script> var script_content_{{$page->script_id}} = {!!json_encode($page->getScript->content)!!};</script>
                                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(script_content_{{$page->script_id}})'></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($page->css_id)
                                        <script> var css_content_{{$page->css_id}} = {!!json_encode($page->getCSS->content)!!};</script>
                                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(css_content_{{$page->css_id}})'></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($page->variables)
                                        <script> var var_content_{{$page->id}} = {!!json_encode($page->variables)!!};</script>
                                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(var_content_{{$page->id}})'></i>
                                        @endif
                                    </td>
                                    @if(Auth::user()->admin)
                                    <td>{{($page->user_id)?$page->getUser->name:'-'}}</td>
                                    <td><small>{{$page->created_at}}</small></td>
                                    <td><small>{{$page->updated_at}}</small></td>
                                    @endif
                                    <td>
                                        <a href="{{ route('Template.Page', ['template_id' => $template->id, "operation"=>"edit", "id"=>$page->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                        /
                                        <a href="{{ route('Template.Page.delete', ['template_id' => $template->id, 'id' => $page->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Template | {{$template->title}}</title>
@endpush