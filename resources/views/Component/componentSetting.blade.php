{{-- Component Setting START --}}
<div id="component_setting_panel" class="position-relative d-none ml-1" style="width: 40%">
        <div class="d-flex align-items-center bg-warning p-1">
            <div class="mr-auto"><span class="fa fa-wrench"></span> Component Setting</div>
            <div class="toggler pr-1" data-toggle="collapse" data-target="#component_setting" role="button">
                <i class="fa fa-toggle-down"></i>
            </div>
            {{-- <div class="showhide" data-target="#component_setting_panel">
                <i class="fa fa-window-close-o"></i>
            </div> --}}
        </div>
        <div id="component_setting" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <div class="m-2 p-2" id="loading_component_setting"><i class="fa fa-spinner"></i> Loading Component Settings</div>
            <div id="component_setting_form_container" class="pb-2 d-none">
                <ul class="nav nav-tabs mt-2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="parent_tab" data-toggle="tab" href="#" role="tab">Wrapper</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="component_tab" data-toggle="tab" href="#" role="tab">Component</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="child_tab" data-toggle="tab" href="#" role="tab">Children</a>
                    </li>
                </ul>
                <div id="component_setting_form_subcontainer" class="pt-3">
                        <div>
                            <div class="m-2 p-2"><i class="fa fa-gear"></i> No Settings to display.</div>
                        </div>
                </div>
            </div>
        </div>
</div>
{{-- Component Setting End --}}