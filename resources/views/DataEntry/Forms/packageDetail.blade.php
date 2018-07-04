@extends('DataEntry.layout')
@section('card')
<div class="card">
    <div class="card-body">
        @if($operation == "edit")
        <h2 class="card-title">
                {{ $package->title }}
        </h2>
        <h6 class="card-subtitle mb-2 text-muted">Edit
                {{ ($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only' }}
                {{ ($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only' }}
        </h6>
        @else
        <h2 class="card-title">Add to {{ $package->title }}</h2>
        @endif
        <p class="card-text">
                @if($operation == "edit")
                <form action="{{ route('DataEntry.Package.Detail.edit') }}" method="post">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="id" value="{{$packageDetail->id}}">
                @else
                <form action="{{ route('DataEntry.Package.Detail.add') }}" method="post">
                <?php $packageDetail = null; ?>
                @endif
                    @csrf
                <input type="hidden" name="package_id" value="{{$package->id}}">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
                    </div>
                    <input name="days" type="number" class="form-control" placeholder="No of Days" tabindex="1" value="{{ old("days", (($packageDetail)?$packageDetail->days:'')) }}">
                    <input name="nights" type="number" class="form-control" placeholder="No of Nights" tabindex="2" value="{{ old("nights", (($packageDetail)?$packageDetail->nights:'')) }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" tabindex="3">
                        </button>
                        <div class="dropdown-menu">
                        <a class="dropdown-item struct_content" href="#">Add / Remove Content</a>
                        </div>
                    </div>
                </div>
                <div id="struct_content_form_group">
                        @if(old("content_type", (($packageDetail && $packageDetail->content_id)?true:'')))
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-gears"></i></span>
                            </div>
                            <select name="content_type" class="custom-select struct_content_type" tabindex="4">
                                <option value="text"{{(old("content_type", (($packageDetail && $packageDetail->getContent)?$packageDetail->getContent->content_type:'')) == "text")?' selected':''}}>Text Content</option>
                                <option value="html"{{(old("content_type", (($packageDetail && $packageDetail->getContent)?$packageDetail->getContent->content_type:'')) == "html")?' selected':''}}>HTML Content</option>
                                <option value="blade"{{(old("content_type", (($packageDetail && $packageDetail->getContent)?$packageDetail->getContent->content_type:'')) == "blade")?' selected':''}}>HTML Blade Content</option>
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>
                            </div>
                            <textarea class="form-control" name="content" placeholder="..." tabindex="5" style="height:300px">{{ old("content", (($packageDetail && $packageDetail->getContent)?$packageDetail->getContent->content:'')) }}</textarea>
                        </div>
                        @endif
                </div>
                <div class="d-flex justify-content-end">
                    @if(old("content_type", (($packageDetail && $packageDetail->content_id)?true:'')))
                    <button type="submit" class="btn btn-success submit_button" tabindex="6">Save</button>
                    @else
                    <button type="submit" class="btn btn-success submit_button" tabindex="4">Save</button>
                    @endif
                </div>
            </form>
        </p>
    </div>
</div>
@endsection