@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">{{ucwords($operation)}} Web URL</h2>
        <h6 class="card-subtitle mb-2">{{$template->title}} | {{$page->title}}</h6>
        <hr><br>
        <div class="card-text">
            @if($operation == "edit")
            <form action="{{ route('WebUrl.edit') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="id" value="{{$weburl->id}}">
            @else
            <form action="{{ route('WebUrl.add') }}" method="post">
            @endif
            @csrf
            <input type="hidden" name="template_id" value="{{$template->id}}">
            <input type="hidden" name="page_id" value="{{$page->id}}">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-link"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="URL String" tabindex="1" name="url" value="{{ old("url", (($weburl)?$weburl->url:'')) }}">
                    <div class="input-group-append ml-1">
                        <span class="input-group-text">GeoLocation</span>
                        <select class="custom-select" tabindex="1" name="geolocation">
                            @foreach(App\Models\GeoLocation::select('country')->groupBy('country')->get() as $country)
                            <option{{(old("geolocation", (($weburl)?$weburl->geolocation:'')) == $country->country)?' selected':''}}>{{$country->country}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-chain-broken"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Regular Expression" tabindex="1" name="regex" value="{{ old("regex", (($weburl)?$weburl->regex:'')) }}">
                    <div class="input-group-append">
                        <span class="input-group-text">eg. /^(.*?)-([^-]+)$/</span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Regex Matches</span>
                    </div>
                    <input type="text" class="form-control" placeholder='{"1":"var1","2":"var2"}' tabindex="1" name="matches" value="{{ old("matches", (($weburl)?$weburl->matches:'')) }}">
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Additional Variables</span>
                    </div>
                    <input type="text" class="form-control" placeholder='{"var1":"val1","var2":"val2"}' tabindex="1" name="url_variables" value="{{ old("url_variables", (($weburl)?$weburl->url_variables:'')) }}">
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">URL Builder</span>
                    </div>
                    <input type="text" class="form-control" placeholder='{"varname1":"@@@database.1.2.3@@","varname2":"@@@global.var@@","varname3":"@@@variable.var@@","varname4":"value"}' tabindex="1" name="url_builder" value="{{ old("url_builder", (($weburl)?$weburl->url_builder:'')) }}">
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Meta Data</h5>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-code"></i></span>
                            </div>
                            <textarea class="form-control" name="meta_content" placeholder="..." tabindex="3" style="height:300px">{{ old("meta_content", (($weburl && $weburl->getMetadata)?$weburl->getMetadata->content:'')) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Global JavaScript</h5>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-code"></i></span>
                            </div>
                            <textarea class="form-control" name="js_content" placeholder="..." tabindex="4" style="height:300px">{{ old("js_content", (($weburl && $weburl->getScript)?$weburl->getScript->content:'')) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Global CSS</h5>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-code"></i></span>
                            </div>
                            <textarea class="form-control" name="css_content" placeholder="..." tabindex="5" style="height:300px">{{ old("css_content", (($weburl && $weburl->getCSS)?$weburl->getCSS->content:'')) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success submit_button" tabindex="5">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('title')
<title>DataBase Variables</title>
@endpush