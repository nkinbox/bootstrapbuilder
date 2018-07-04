@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if(!$operation)
        <a class="nav-link pull-right" href="{{ route('DataEntry.Facilities',['operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
        @endif
        <h2 class="card-title">{{ ucwords($operation)." " }}Global {{($operation)?'Facility':'Facilities'}}</h2>
        @if(!$operation)
        <h6 class="card-subtitle mb-2 text-muted">These are global data for Hotel(Rooms) Facilities</h6>
        @endif
        <p class="card-text">
            @if($operation)
            @if($operation == "add" || $operation == "edit")
            @include('DataEntry.Forms.facility')
            @endif
            @else
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Facility Type</th>
                    <th scope="col">Content</th>
                    @if(Auth::user()->admin)
                    <th scope="col">User</th>
                    @endif
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($facilities as $facility)
                    <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$facility->title}}</td>
                    <td><span class="badge badge-light">{!!($facility->type == "hotel")?'<i class="fa fa-building"></i> Hotel':'<i class="fa fa-hotel"></i> Room'!!}</span></td>
                    <td><?php echo preg_replace_callback('/id=@@image\.(.*?)@@/', function($m) {                        
                        $image = App\Models\Images::find($m[1]);
                        return 'src="' .(($image)?asset('storage/'.$image->file_name):'#'). '"';
                        },$facility->content); ?>
                        @if($facility->content_id)
                        <script> var content_{{$facility->content_id}} = {!!json_encode($facility->getContent->content)!!};</script>
                        <i class="fa fa-window-maximize" style="cursor:pointer" onclick='preview_html(content_{{$facility->content_id}})'></i>
                        @endif
                    </td>
                    @if(Auth::user()->admin)
                    <td>{{($facility->user_id)?$facility->getUser->name:'-'}}</td>
                    @endif
                    <td>
                        <a href="{{ route('DataEntry.Facilities', ["operation"=>"edit", "id"=>$facility->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                        /
                        <a href="{{ route('DataEntry.Facilities.delete', ['id' => $facility->id]) }}"><i class="fa fa-trash"></i> Delete</a>
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
<title>Global Facilities</title>
@endpush