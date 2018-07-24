@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if($operation)
        @if($operation == "show")
        <a class="nav-link pull-right" href="{{ route('DataEntry.Page',['template_id'=> $template_id, 'operation' => 'add']) }}" target="_blank"><i class="fa fa-plus-circle"></i> Add New</a>
        <h2 class="card-title">{{($template_id)?$template->title:'Unknown'}} Pages</h2>
        @else
        <h2 class="card-title">{{ ucwords($operation)." " }}{{($template_id)?$template->title:'Unknown'}} Page</h2>
        @endif
        @else
        <a class="nav-link pull-right" href="{{ route('DataEntry.Page',['template_id'=> 0, 'operation' => 'add']) }}" target="_blank"><i class="fa fa-plus-circle"></i> Add Page</a>
        <h2 class="card-title">{{ ucwords($operation)." " }}Website Pages</h2>
        @endif
        <div class="card-text">
            @if($operation)
            @if($operation == "add" || $operation == "edit")
            @include('DataEntry.Forms.webpage')
            @elseif($operation == "show")
            {{$pages->links()}}
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Group</th>
                    <th scope="col">Location</th>
                    <th scope="col">URL</th>
                    <th scope="col">Content</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Updated at</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pages as $page)
                    <tr>
                    <th scope="row"{!! ($page->broked || !$page->page_id || !$page->content_id)?' class="bg-danger text-light"':''!!}>{{$loop->iteration}}</th>
                    <td><small>{{$page->title}}</small></td>
                    <td><kbd>{{$page->type}}</kbd></td>
                    <td><small>{{$page->group_title}}</small></td>
                    <td><small>@if($page->url && $page->url->geolocation)
                        {{$page->url->geolocation}}
                    @else Global
                    @endif</small></td>
                    <td>@if($page->url)<small><small><a{!!($page->page_id && $page->content_id)?' href="'.route('DataEntry.Blade', ["page_id" => $page->page_id, "content_id" => $page->content_id]).'" target="_blank"':''!!}>{{($template_id)?$template->title:'websitename'}}/{{$page->url->url}}.html</a></small></small>@else <b class="text-danger">NO URL</b> @endif</td>
                    <td>{!!($page->content_id && $page->getContent->content != '' || $page->getContent->content != null)?'Yes':'<b class="text-danger">No</b>'!!}</td>
                    @if(Auth::user()->admin)
                    <td>{{($page->user_id)?$page->getUser->name:'-'}}</td>
                    <td><small><small>{{$page->created_at}}</small></small></td>
                    <td><small><small>{{$page->updated_at}}</small></small></td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Page', ["template_id" => $page->template_id, "operation" => "edit", "id" => $page->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Page.delete', ['id' => $page->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$pages->links()}}
            @endif
            @else
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Pages</th>
                    <th scope="col">hasBroked</th>
                    <th scope="col">No URL</th>
                    <th scope="col">PageLess</th>
                    <th scope="col">ContentLess</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pages as $key => $page)
                    <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td><a href="{{ route('DataEntry.Page', ["template_id" => $page['template_id'], "operation" => "show"]) }}">{{$key}}</a></td>
                    <td{!! (!$page['count'])?' class="bg-danger text-light"':''!!}>{{$page['count']}}</td>
                    <td{!! ($page['broked'])?' class="bg-danger text-light"':''!!}>{{$page['broked']}}</td>
                    <td{!! ($page['nourl'])?' class="bg-danger text-light"':''!!}>{{$page['nourl']}}</td>
                    <td{!! ($page['pageless'])?' class="bg-danger text-light"':''!!}>{{$page['pageless']}}</td>
                    <td{!! ($page['content'])?' class="bg-danger text-light"':''!!}>{{$page['content']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
@push('title')
<title>Website Pages</title>
@endpush