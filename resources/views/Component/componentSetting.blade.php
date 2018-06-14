{{-- Component Setting START --}}
<div id="component_setting_panel" class="position-relative d-none ml-1" style="width: 40%">
        <div class="d-flex align-items-center bg-warning p-1">
            <div class="mr-auto"><span class="fa fa-wrench"></span> Component Setting</div>
            <div class="toggler pr-1" data-toggle="collapse" data-target="#component_setting" role="button">
                <i class="fa fa-toggle-up"></i>
            </div>
            {{-- <div class="showhide" data-target="#component_setting_panel">
                <i class="fa fa-window-close-o"></i>
            </div> --}}
        </div>
        <div id="component_setting" class="collapse border container bg-light position-absolute w-100" style="right:0">
            <div class="m-2 p-2" id="loading_component_setting"><i class="fa fa-spinner"></i> Loading Component Settings</div>
            <form id="component_setting_form" class="pb-2 d-none">
                <ul class="nav nav-tabs mt-2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="parent_tab" data-toggle="tab" href="#parent_tab" role="tab">Wrapper</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" id="component_tab" data-toggle="tab" href="#component_tab" role="tab">Component</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="child_tab" data-toggle="tab" href="#child_tab" role="tab">Children</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade" id="parent_tab_content" role="tabpanel"></div>
                    <div class="tab-pane fade show active" id="component_tab_content" role="tabpanel"></div>
                    <div class="tab-pane fade" id="child_tab_content" role="tabpanel"></div>
                </div>
                <button type="submit" class="btn btn-primary">Preview</button>
            </form>
        </div>
</div>
{{-- Component Setting End --}}