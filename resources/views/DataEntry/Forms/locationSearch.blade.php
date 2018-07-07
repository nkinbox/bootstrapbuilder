<div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-map-marker"></i></span>
            <a href="#" id="location_search" class="btn border" role="button" tabindex="1"><i id="ajax_status_location" class="fa fa-search"></i></a>
        </div>
        <input name="location_name" type="text" value="{{ old('location_name', ((isset($default) && $default)?$default:'')) }}" class="form-control" placeholder="Locality Name" tabindex="1" list="location_list">
        <div class="input-group-append">
        </div>
    </div>
    <datalist id="location_list">
    </datalist>
</div>