@extends('layouts.app')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger m-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <ul class="list-group p-1">
            @foreach ($errors->all() as $error)
                <li class="list-group-item">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('message'))
    <div class="alert alert-success m-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session('message') }}
    </div>
@endif
<div class="card mx-auto my-3 w-75">
    <div class="card-body">
        <h3 class="card-title">Add Basic Component</h3>
        <hr>
        <form method="post" action="{{ route("AddComponent") }}">
            @csrf
            <div class="form-group">
                <label>Component Name</label>
                <input type="text" class="form-control" name="self[name]">
                <small class="form-text text-muted">Display name of Component MAX(50)</small>
            </div>
            <div class="form-group">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Component
                            </button>
                        </h5>
                        </div>
                
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <input type="hidden" name="self[category]" value="basic">
                            <input type="hidden" name="self[node]" value="self">
                            <input type="hidden" name="self[var_attributes]" value="[]">
                            <div class="form-group">
                                <label>Start Tag</label>
                                <input type="text" class="form-control" name="self[start_tag]">
                                <small class="form-text text-muted">Starting HTML eg &lt;p&gt;,&lt;div&gt;</small>
                            </div>
                            <div class="form-group">
                                <label>End Tag</label>
                                <input type="text" class="form-control" name="self[end_tag]">
                                <small class="form-text text-muted">Ending HTML eg &lt;/p&gt;,&lt;/div&gt;</small>
                            </div>
                            <div class="form-group">
                                <label>Content Type</label>
                                <select class="form-control" id="component_content_type" name="self[content_type]">
                                    <option selected>static</option>
                                    <option>element</option>
                                </select>
                                <small class="form-text text-muted">Element implies Have Child, Static implies Have Text Content</small>
                            </div>
                            <div class="form-group">
                                <label>Content</label>
                                <input type="text" class="form-control" name="self[content]" value="May Contain Some Text or Another Component">
                                <small class="form-text text-muted">Only if Content Type STATIC</small>
                            </div>
                            <div class="form-group">
                                <label>Classes</label>
                                <input type="text" class="form-control" name="self[classes]" value="[]">
                                <small class="form-text text-muted">Classes in JSON eg: ["class1", "class2"]</small>
                            </div>
                            <div class="form-group">
                                <label>Attributes</label>
                                <input type="text" class="form-control" name="self[attributes]" value="{}">
                                <small class="form-text text-muted">Attributes in JSON eg: {"attr1":"value1", "attr2":"value2"}</small>
                            </div>
                            <div class="form-group">
                                <label>Style</label>
                                <input type="text" class="form-control" name="self[style]" value="{&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{}}">
                                <small class="form-text text-muted">Style in JSON eg: {&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{&quot;width&quot;:&quot;100%&quot;}}</small>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                            <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Wrapper
                            </button>
                        </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            <div class="d-flex flex-row-reverse">
                                <div id="load_wrapper_setting" class="border px-1" style="cursor:pointer"><i class="fa fa-level-down"></i> Load Wrapper Settings</div>                                
                            </div>
                            <div id="wrapper_setting_container"></div>
                        </div>
                        </div>
                    </div>
                    <div class="card d-none" id="childrenContentaccordion">
                        <div class="card-header" id="headingThree">
                        <h5 class="mb-0">
                            <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Children
                            </button>
                        </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                        <div class="card-body">
                            <div class="d-flex flex-row-reverse">
                                <div id="load_child_setting" class="border px-1" style="cursor:pointer"><i class="fa fa-level-down"></i> Load New Child Settings</div>                                
                            </div>
                            <div id="child_setting_container"></div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Basic Component</button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/component_script.js') }}" defer></script>
@endpush