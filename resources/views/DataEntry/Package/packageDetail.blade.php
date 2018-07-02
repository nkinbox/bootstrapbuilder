@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{ ucwords($package->title) }}</h2>
        <div class="card-text">
        @include('DataEntry.Package.package')
        <div class="card">
            <div class="card-body">
                <a class="nav-link pull-right" href="{{ route('DataEntry.Package.Detail',['package_id' => $package->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                <h3 class="card-title">Tour Packages</h3>
                <p class="card-text">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Content</th>
                            @if(Auth::user()->admin)
                            <th scope="col">User</th>
                            @endif
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($packageDetails as $packageDetail)
                            <?php
                            $isComplete = true;
                            if(!count($packageDetail->PackageItinerary) || !count($packageDetail->PackageMarker) || !count($packageDetail->PackagePrice))
                            $isComplete = false;
                            ?>
                            <tr>
                            <th scope="row"{!! (!$isComplete)?' class="bg-danger text-light"':'' !!}>{{$loop->iteration}}</th>
                            <td><a href="{{ route('DataEntry.Package.Detail',['package_id' => $package->id, 'operation' => 'show', 'id' => $packageDetail->id])}}">
                                {{ ($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only' }}
                                {{ ($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only' }}
                                </a>
                            </td>
                            <td>
                                @if($packageDetail->content_id)
                                <script> var content_{{$packageDetail->content_id}} = {!!json_encode($packageDetail->getContent->content)!!};</script>
                                <i class="fa fa-clone" style="cursor:pointer" onclick='preview_html(content_{{$packageDetail->content_id}})'></i>
                                @else
                                -
                                @endif
                            </td>
                            @if(Auth::user()->admin)
                            <td>{{($packageDetail->user_id)?$packageDetail->getUser->name:'-'}}</td>
                            @endif
                            <td>
                                <a href="{{ route('DataEntry.Package.Detail',['package_id' => $package->id, 'operation' => 'edit', 'id' => $packageDetail->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                /
                                <a href="{{ route('DataEntry.Package.Detail.delete', ['id' => $packageDetail->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </p>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>{{ $package->title }} Detail</title>
@endpush
