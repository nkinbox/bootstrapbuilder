@extends('layouts.app')
@push("title")
<title>Component Creator</title>
@endpush
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
                        <label>Component</label>
                        <div class="input-group mb-3">
                            <input id="component_name" type="text" class="form-control" placeholder="Select Component" disabled>
                            <div class="input-group-append">
                                <button id="component_new" class="btn btn-outline-secondary" type="button"><i class="fa fa-plus"></i></button>
                                <button id="component_reset" class="btn btn-outline-secondary" type="button"><i class="fa fa-refresh"></i></button>
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
                        <label title="Highlight Selected Element"><input type="checkbox" id="highlight"> <i class="fa fa-object-ungroup"></i></label>
                    </div>
                    <div>
                        <label title="Load Component ALL/Current"><input type="checkbox" id="loadstack"> <i class="fa fa-cubes"></i></label>
                    </div>
                    <div>
                        <label title="Add to Component Stack"><button class="btn btn-default" type="button" id="addtostack" data-wait="none"><i class="fa fa-fast-forward"></i></button></label>
                        <label title="Show Component Stack"><button class="btn btn-warning" type="button" id="showstack"><i class="fa fa-sitemap"></i></button></label>
                    </div>     
                </div>
            </div>
    </div>
</div>
{{-- Stack Tree Modal Start --}}
<div class="modal fade" id="stackTree" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 90%">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Stack View</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="d-flex justify-content-between">
                <div class="input-group mb-3" style="width:350px">
                    <div class="input-group-prepend">
                            <input id="new_component_name" type="text" class="form-control" placeholder="Component Name" value="{{($edit)?$name:''}}"{{($edit)?' disabled':''}}>
                    </div>
                    <select class="custom-select" id="component_category">
                        <option value="component"{{ ($edit && $stack['self']['category'] == "component")?' selected':'' }}>Component</option>
                        <option value="basic"{{ ($edit && $stack['self']['category'] == "basic")?' selected':'' }}>Basic</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="savestack" save-mode="{{ ($edit)?'edit':'save' }}"><i class="fa fa-save"></i></button>
                    </div>
                </div>
                <div class="switch-field">
                    <input type="radio" id="switch_3_left" name="mode" value="select" checked>
                    <label for="switch_3_left">Select</label>
                    <input type="radio" id="switch_3_center" name="mode" value="copy">
                    <label for="switch_3_center">Copy</label>
                    <input type="radio" id="switch_3_right" name="mode" value="delete">
                    <label for="switch_3_right">Delete</label>
                    <input type="radio" id="switch_4_right" name="mode" value="edit">
                    <label for="switch_4_right">Edit</label>
                </div>
            </div>
            <div class="tree"></div>
        </div>
      </div>
    </div>
  </div>
{{-- Stack Tree Modal End --}}

@endsection
@push("scripts")
<script>
    var component = {};
    var stack = {!! ($edit)?"JSON.parse('".json_encode($stack)."')":'{}' !!};
    var stackPointer = "";
    var ele_id = 0;
    var urls = {
        "loadComponent":"{{ route('LoadComponent') }}",
        "loadComponents":"{{ route('LoadComponents') }}",
        "saveComponent":"{{ route('SaveComponent') }}",
        "editComponent":"{{ route('UpdateComponent') }}"
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
    .tree {
    -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
    -webkit-transform-origin: 50%;
            transform-origin: 50%;
    }

    .tree ul {
    position: relative;
    padding: 1em 0;
    white-space: nowrap;
    margin: 0 auto;
    text-align: center;
    }
    .tree ul::after {
    content: '';
    display: table;
    clear: both;
    }

    .tree li {
    display: inline-block;
    vertical-align: top;
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 1em .5em 0 .5em;
    }
    .tree li::before, .tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 1px solid #ccc;
    width: 50%;
    height: 1em;
    }
    .tree li::after {
    right: auto;
    left: 50%;
    border-left: 1px solid #ccc;
    }
    .tree li:only-child::after, .tree li:only-child::before {
    display: none;
    }
    .tree li:only-child {
    padding-top: 0;
    }
    .tree li:first-child::before, .tree li:last-child::after {
    border: 0 none;
    }
    .tree li:last-child::before {
    border-right: 1px solid #ccc;
    border-radius: 0 5px 0 0;
    }
    .tree li:first-child::after {
    border-radius: 5px 0 0 0;
    }

    .tree ul ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 1px solid #ccc;
    width: 0;
    height: 1em;
    }

    .tree li a {
    border: 1px solid #ccc;
    padding: .5em .75em;
    text-decoration: none;
    display: inline-block;
    border-radius: 5px;
    color: #333;
    position: relative;
    top: 1px;
    -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
    }

    .tree li a:hover,
    .tree li a:hover + ul li a {
    background: #007cff;
    color: #fff;
    border: 1px solid #004894;
    }

    .tree li a:hover + ul li::after,
    .tree li a:hover + ul li::before,
    .tree li a:hover + ul::before,
    .tree li a:hover + ul ul::before {
    border-color: #004894;
    }
    .switch-field input {
        position: absolute !important;
        clip: rect(0, 0, 0, 0);
        height: 1px;
        width: 1px;
        border: 0;
        overflow: hidden;
    }

    .switch-field label {
    float: left;
    }

    .switch-field label {
    display: inline-block;
    width: 70px;
    background-color: #e4e4e4;
    color: rgba(0, 0, 0, 0.6);
    font-size: 14px;
    font-weight: normal;
    text-align: center;
    text-shadow: none;
    padding: 6px 14px;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition:    all 0.1s ease-in-out;
    -ms-transition:     all 0.1s ease-in-out;
    -o-transition:      all 0.1s ease-in-out;
    transition:         all 0.1s ease-in-out;
    }

    .switch-field label:hover {
        cursor: pointer;
    }

    .switch-field input:checked + label {
    background-color: #83b6ff;
    -webkit-box-shadow: none;
    box-shadow: none;
    }

    .switch-field label:first-of-type {
    border-radius: 4px 0 0 4px;
    }

    .switch-field label:last-of-type {
    border-radius: 0 4px 4px 0;
    }
</style>
@endpush