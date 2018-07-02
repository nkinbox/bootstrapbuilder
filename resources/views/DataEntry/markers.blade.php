@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if(!$operation)
        <a class="nav-link pull-right" href="{{ route('DataEntry.Markers',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        @endif
        <h2 class="card-title">{{ ucwords((($operation != "add" && $operation != "edit")?"Filtered ":$operation))." " }}Global (Labels Tags Categories)</h2>
        @if(!$operation)
        <h6 class="card-subtitle mb-2 text-muted">These are global Markers for Hotel and Packages (category, tag, label, inclusions, exclusions, activity, theme)</h6>
        @endif
        <p class="card-text">
            @if($operation && ($operation == "add" || $operation == "edit"))
            @include('DataEntry.Forms.marker')
            @else
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Category</th>
                    <th scope="col">Type</th>
                    <th scope="col">Content</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($markers as $marker)
                    <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$marker->title}}</td>
                    <td><a href="{{route('DataEntry.Markers', ["operation"=>"category", "id"=>$marker->category])}}"><span class="badge badge-light">{!!($marker->category == "hotel")?'<i class="fa fa-building"></i> Hotel':'<i class="fa fa-suitcase"></i> Package'!!}</span></a></td>
                    <td><a href="{{route('DataEntry.Markers', ["operation"=>"type", "id"=>$marker->type])}}">{{$marker->type}}</a></td>
                    <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
                        $image = App\Models\Images::find($m[1]);
                        return 'src="' .asset('storage/'.$image->file_name). '"';
                        },$marker->content); ?>
                        @if($marker->content_id)
                        <script> var content_{{$marker->content_id}} = {!!json_encode($marker->getContent->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$marker->content_id}})'></i>
                        @endif
                    </td>
                    @if(Auth::user()->admin)
                    <td>{{($marker->user_id)?$marker->getUser->name:'-'}}</td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Markers', ["operation"=>"edit", "id"=>$marker->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Markers.delete', ['id' => $marker->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </p>
    </div>
</div>
@endsection
@push('title')
<title>Global (Labels Tags Categories)</title>
@endpush