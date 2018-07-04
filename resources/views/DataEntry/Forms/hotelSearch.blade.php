<div>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-building-o"></i></span>
            <a href="#" id="hotel_search" class="btn border" role="button" tabindex="1"><i id="ajax_status_hotel" class="fa fa-search"></i></a>
        </div>
        <input name="hotel_name" type="text" value="{{ old('hotel_name', ((isset($default) && $default)?$default:'')) }}" class="form-control" placeholder="Hotel Name" tabindex="1" list="hotel_list">
        <div class="input-group-append">
        </div>
    </div>
    <datalist id="hotel_list">
    </datalist>
</div>