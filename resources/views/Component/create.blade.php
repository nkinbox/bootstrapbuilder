@extends('layouts.app')
@section('content')
<div id="setting_panel" class="d-flex flex-row-reverse p-1">
@include('Component.componentSetting')
@include('Bootstrap.utilities')
    {{-- Display Property @@ START --}}
    <div id="display_property_@@_panel" class="position-relative w-25 d-none ml-1">
        <div class="d-flex align-items-center bg-warning p-1">
            <div class="mr-auto"><span class="fa fa-wrench"></span> @@ Setting</div>
            <div class="toggler pr-1" data-toggle="collapse" data-target="#display_property_@@" role="button">
                <i class="fa fa-toggle-down"></i>
            </div>
            <div class="showhide" data-target="#display_property_@@_panel">
                <i class="fa fa-window-close-o"></i>
            </div>
        </div>
        <div id="display_property_@@" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <i class="fa fa-spinner"></i> Loading Settings
        </div>
    </div>
    {{-- Display Property @@ END --}}
</div>
<div class="py-4 border bg-white rounded d-none" id="basicComponentContainer" style="margin: 20px 10px 800px 10px"></div>
<div class="py-4" id="component_display"></div>
<div class="fixed-bottom bg-light">
    <div class="toggler d-flex justify-content-center bg-primary p-1" data-toggle="collapse" data-target="#collapse_1" role="button">
        <i class="fa fa-toggle-up"></i>
    </div>
    <div id="collapse_1" class="container p-2 collapse in">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="component">Component</label>
                        <div class="input-group mb-3">
                            <input id="component_name" type="text" class="form-control" placeholder="Select Component" disabled>
                            <div class="input-group-append">
                                <button id="BrowseComponents" class="btn btn-outline-secondary" type="button"><i class="fa fa-cloud-download"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="display_property">Display Property</label>
                        <select class="form-control" id="display_property">
                            <option value="">Select Property</option>
                            <option>Borders</option>
                            <option>Colors</option>
                            <option>Display</option>
                            <option>Flex</option>
                            <option>Float</option>
                            <option>Position</option>
                            <option>Sizing</option>
                            <option>Spacing</option>
                            <option>Text</option>
                            <option>VerticalAlign</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <label><input type="checkbox" id="highlight"><i class="fa fa-object-ungroup"></i></label>
                    </div>
                    <div>
                        <label><input type="checkbox" id="loadstack"><i class="fa fa-cubes"></i></label>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
@push("scripts")
<script>
    var component = {};
    var stack = {};
    var ele_id = 0;
    var url = {
        "loadComponent":"{{ route('LoadComponent') }}",
        "loadComponents":"{{ route('LoadComponents') }}"
    }
</script>
@endpush
@push("styles")
<style>
    .position-absolute {
        z-index: 999999;
        margin-bottom: 200px;
    }
    #component_display {
        margin-bottom: 200px;
    }
    #component_display .highlighter {
        box-shadow: 0 0 5px 1px #2e5bff inset;
    }
    #component_display .Removed {
        box-shadow: 0 0 5px 1px #e20000 inset;
    }
    #component_display .Conditional {
        box-shadow: 0 0 5px 1px #23e200 inset;
    }
</style>
@endpush