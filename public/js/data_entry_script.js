function content_select() {
    var tabindex = $(".submit_button").attr("tabindex");
    var dynamic_ele = '<div class="input-group mb-3">';
    dynamic_ele += '<div class="input-group-prepend">';
    dynamic_ele += '<span class="input-group-text"><i class="fa fa-gears"></i></span>';
    dynamic_ele += '</div>';
    dynamic_ele += '<select name="content_type" class="custom-select struct_content_type" tabindex="'+(tabindex++)+'">';
    dynamic_ele += '<option value="text">Text Content</option>';
    dynamic_ele += '<option value="html">HTML Content</option>';
    dynamic_ele += '<option value="blade">HTML Blade Content</option>';
    dynamic_ele += '</select>';
    dynamic_ele += '</div>';
    dynamic_ele = $(dynamic_ele);
    $(".submit_button").attr("tabindex",(tabindex));
    return dynamic_ele;
}
function content_textarea() {
    var tabindex = $(".submit_button").attr("tabindex");
    var dynamic_ele = '<div class="input-group mb-3">';
    dynamic_ele += '<div class="input-group-prepend">';
    dynamic_ele += '<span class="input-group-text"><i class="fa fa-code preview_html" style="cursor:pointer; font-weight:bold; font-size:120%"></i></span>';
    dynamic_ele += '</div>';
    dynamic_ele += '<textarea class="form-control" name="content" placeholder="..." tabindex="'+(tabindex++)+'" style="height:300px"></textarea>';
    dynamic_ele += '</div>';
    dynamic_ele = $(dynamic_ele);
    $(".submit_button").attr("tabindex",(tabindex));
    return dynamic_ele;
}
function preview_html(html = "", select = "") {
    var content = "";
    var regex = /@@image.(.*?)@@/g;
    if(html)
    content += html;
    else if(select) {
    content += $(select).val();
    } else
    content += $("textarea[name='content']").val();
    var images = content.match(regex);
    content = content.replace(regex, '"image_$1"');
    $("#preview_html_modal").remove();
    var modal = '<div class="modal fade" id="preview_html_modal" tabindex="-1" role="dialog">';
    modal += '<div class="modal-dialog" role="document" style="max-width: 75% !important">';
    modal += '<div class="modal-content">';
    modal += '<div class="modal-header">';
    modal += '<h5 class="modal-title">';
    modal += 'Content Preview';
    modal += '</h5>';
    modal += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    modal += '<span aria-hidden="true">&times;</span>';
    modal += '</button>';
    modal += '</div>';
    modal += '<div class="modal-body">';
    modal += content;
    modal += '</div>';
    $("body").append(modal);
    if(images){
        images.forEach(image => {
            getImage(image.replace("@@image.", "").replace("@@", ""));
        });
    }
    $("#preview_html_modal").modal("show");
}
function getImage(id) {
    $.ajax({
        type:'POST',
        url: urls.image,
        data:'{"id":"'+id+'"}',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success:function(data){
            console.log(data);
            if(data.success) {
                $('#image_'+data.id).attr("src", data.src).css("max-width","100%");
            }
        },
        error: function(data){
            console.log(data);
        }
    });
}
function uploadImage() {
    $("#upload_image_modal").remove();
    var modal = '<div class="modal fade" id="upload_image_modal" tabindex="-1" role="dialog">';
    modal += '<div class="modal-dialog" role="document" style="max-width: 75% !important">';
    modal += '<div class="modal-content">';
    modal += '<div class="modal-header">';
    modal += '<h5 class="modal-title">';
    modal += 'Upload Image';
    modal += '</h5>';
    modal += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    modal += '<span aria-hidden="true">&times;</span>';
    modal += '</button>';
    modal += '</div>';
    modal += '<div class="modal-body">';
    modal += '<form id="upload_image_form">';
    modal += '<div class="input-group mb-3">';
    modal += '<div class="input-group-prepend">';
    modal += '<span class="input-group-text">Image File</span>';
    modal += '</div>';
    modal += '<div class="custom-file">';
    modal += '<input type="file" accept="image/*" class="custom-file-input" id="inputGroupFile01" name="image">';
    modal += '<label class="custom-file-label" for="inputGroupFile01">Choose file</label>';
    modal += '</div>';
    modal += '</div>';
    modal += '<div class="input-group mb-3">';
    modal += '<div class="input-group-prepend">';
    modal += '<span class="input-group-text"><i class="fa fa-file-image-o"></i></span>';
    modal += '</div>';
    modal += '<input type="text" class="form-control" placeholder="Image Title" name="image_title" value="" required>';
    // modal += '<select class="custom-select" name="type">';
    // modal += '<option value="asset">Asset Image</option>';
    // modal += '<option value="hotel">Hotel Image</option>';
    // modal += '<option value="package">Package Image</option>';
    // modal += '</select>';
    modal += '</div>';
    modal += '<div class="d-flex justify-content-end">';
    modal += '<button type="submit" class="btn btn-success submit_button"><i id="image_uploading" class="fa fa-cloud-upload"></i> Upload</button>';
    modal += '</div>';
    modal += '</form>';
    modal += '<div><div id="image_size" class="badge badge-dark d-none"></div></div><img id="preview_image" class="img-thumbnail d-none">';
    modal += '</div>';
    $("body").append(modal);
    $("#upload_image_modal").modal("show");
    $('#inputGroupFile01').on('change',function(){
        $('#preview_image').addClass("d-none");
        $("#image_size").addClass("d-none");
        var fileName = $(this).val();
        $(this).next('.custom-file-label').html(((fileName)?fileName:'Choose File'));
        if (this.files && this.files[0]) {
            $("#image_size").html(Math.round(this.files[0].size/1024) +'KB');
            var reader = new FileReader();        
            reader.onload = function(e) {
                var image = new Image();
                image.src = e.target.result;
                image.onload = function() {
                    $("#image_size").html(this.width+ '&times;' +this.height + "&nbsp;(" + $("#image_size").html() +')');
                    $("#image_size").removeClass("d-none");
                }                
                $('#preview_image').attr('src', e.target.result);
                $('#preview_image').removeClass("d-none");
            }
            reader.readAsDataURL(this.files[0]);
        }
    })
    $("#upload_image_form").submit(function(e){
        e.preventDefault();
        $("#image_uploading").addClass("fa-spinner").removeClass("fa-cloud-upload");
        var formData = new FormData(this);
        $.ajax({
            type:'POST',
            url: urls.imageUpload,
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            dataType: "json",
            success:function(data){
                if(data.success) {
                    var modal = $("#upload_image_modal");
                    modal.find(".modal-body").html("<div class=\"m-2 p-2 text-center text-success\"><h2><i class=\"fa fa-warning\"></i> Uploaded.</h2></div>");
                    modal.find(".modal-body").append('<div class="m-2 p-2 text-center text-muted border"><input type="text" id="uploaded_image" class="border-0 text-center" value="&lt;img id=@@image.' +data.image_id+ '@@&gt;"> <button id="copyText" class="btn btn-secondary pull-right" autofocus><i class="fa fa-clipboard"></i><b>Copy</b></button></div>');
                    $("#copyText").click(function(){
                        $("#uploaded_image").select();
                        document.execCommand("copy");
                        $(this).find("b").text("Copied");
                        $("#uploaded_image").blur();
                    });
                } else {
                    $("#upload_image_modal").find(".modal-body").html("<div class=\"m-2 p-2 text-center text-danger\"><h2><i class=\"fa fa-warning\"></i> Upload Failed.</h2></div>");
                }
            },
            error: function(data){
                $("#upload_image_modal").find(".modal-body").html("<div class=\"m-2 p-2 text-center text-danger\"><h2><i class=\"fa fa-warning\"></i> Network Error Occured!</h2></div>");
            }
        });
    });
}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#uploadImage").click(function(e){
    e.preventDefault();
    uploadImage();
});
$(".struct_content").click(function(e){
    e.preventDefault();
    var ele = $("#struct_content_form_group");
    if(!ele.html()) {
        var selectbox = content_select();
        ele.append(selectbox);
        ele.append(content_textarea());
        ele.find(".struct_content_type").focus();
        $(".preview_html").click(function() {
            preview_html();
        });
    } else {
        ele.html("");
    }
});
$(".preview_html_hotel_content").click(function() {
    preview_html("", "textarea[name='hotel_content']");
});
$(".preview_html_policy_content").click(function() {
    preview_html("", "textarea[name='policy_content']");
});
$(".preview_html").click(function() {
    preview_html();
});
$("#geolocation_form input").change(function(){
    if(typeof sync !== "undefined" && sync) {
        $("#" + this.id + "_sync").val($(this).val());
    }
    $(this).nextAll().val("");
    $("#geolocation_form").submit();
});
$("#geolocation_form").submit(function(e){
    e.preventDefault();
    var data = {};
    $.each($(this).serializeArray(), function(i, field){
        data[field.name] = field.value;
    });
    data['route'] = "";
    if($(this).attr('data-route'))
    data['route'] = $(this).attr('data-route');
    $("#ajax_status").addClass("fa-spinner").removeClass("fa-search");
    $.ajax({
        type: "POST",
        url: urls.geolocation,
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data) {
            var currentLocation = "";
            $.each(data, function(key, val){
                if(val && key != "current") {
                    $("#"+key).html("");
                    $.each(val, function(i, val1){
                        $.each(val1, function(j,value){
                            $("#"+key).append('<option value="' +value+ '">');
                        });
                    });
                }
                if(key == "current") {
                    currentLocation = val; 
                }
            });
        $("#ajax_status").addClass("fa-search").removeClass("fa-spinner");
        $("#geoLocation_in_Focus").html(currentLocation);
        },
        failure: function(errMsg) {
            console.log(errMsg);
        }
    });
});
$("#hotel_search").click(function(e){
    e.preventDefault();
    $("#ajax_status_hotel").addClass("fa-spinner").removeClass("fa-search");
    $.ajax({
        type: "POST",
        url: urls.hotel,
        dataType: "json",
        success: function(data) {
            $("#hotel_list").html("");
            $.each(data, function(key, value){
                $("#hotel_list").append('<option value="' +value+ '">');
            });
        $("#ajax_status_hotel").addClass("fa-search").removeClass("fa-spinner");
        },
        failure: function(errMsg) {
            console.log(errMsg);
        }
    });
});