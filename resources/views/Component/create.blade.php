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
<div class="py-4" id="component_display"></div>
<div class="fixed-bottom bg-light">
    <div class="toggler d-flex justify-content-center bg-primary p-1" data-toggle="collapse" data-target="#collapse_1" role="button">
        <i class="fa fa-toggle-up"></i>
    </div>
    <div id="collapse_1" class="container p-2 collapse in">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="component">Component</label>
                        <select class="form-control" id="component" data-selected="0">
                            <option value="">Select Component</option>
                            @foreach($basicComponents as $component)
                            <option>{{ $component->name }}</option>
                            @endforeach
                            <option>Grid</option>
                            <option>Alerts</option>
                            <option>Badge</option>
                            <option>Breadcrumb</option>
                            <option>Buttons</option>
                            <option>ButtonsGroup</option>
                            <option>Card</option>
                            <option>Carousel</option>
                            <option>Collapse</option>
                            <option>Dropdowns</option>
                            <option>Forms</option>
                            <option>InputGroup</option>
                            <option>Jumbotron</option>
                            <option>ListGroup</option>
                            <option>Modal</option>
                            <option>Navs</option>
                            <option>Navbar</option>
                            <option>Popovers</option>
                            <option>Images</option>
                            <option>Figures</option>
                            <option>Tables</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">col</div>
                <div class="col-md-2">col</div>
            </div>
    </div>
</div>
@endsection
@push("scripts")
<script>
    var json = {};
    var component = {};
    var url = {
        "loadComponent":"{{ route('LoadComponent') }}"
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
</style>
@endpush