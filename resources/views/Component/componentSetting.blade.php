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
                <h5>Apply Config</h5>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="inFocus" id="inFocus1" value="this_parent">
                            <label class="form-check-label" for="inFocus1">
                                Parent Element
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="inFocus" id="inFocus2" value="this_self" checked>
                            <label class="form-check-label" for="inFocus2">
                                Component itself
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="inFocus" id="inFocus3" value="this_child">
                            <label class="form-check-label" for="inFocus3">
                                Child Element
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Background color</label>
                    <select multiple class="form-control" name="name">
                        <option>bg-primary</option>
                        <option>bg-secondary</option>
                        <option>bg-success</option>
                        <option>bg-danger</option>
                        <option>bg-warning</option>
                        <option>bg-info</option>
                        <option>bg-light</option>
                        <option>bg-dark</option>
                        <option>bg-white</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Preview</button>
            </form>
        </div>
</div>
{{-- Component Setting End --}}