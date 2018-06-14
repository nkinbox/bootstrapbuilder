function renderer(ele) {
    var element = $(ele.start_tag+ele.end_tag);
    var style = $("<style></style>");
    element.attr("id", "component_"+ele.id);
    $.each(ele.classes, function(key, value){
        element.addClass(value);
    });
    $.each(ele.attributes, function(key, value){
        element.attr(key,value);
    });
    if(ele.content_type == "static" || ele.content_type == "variable")
    element.html(ele.content);
    var temp_style = "";
    $.each(ele.style.style, function(key, value) {
        temp_style += key+':'+value+';';
    });
    style.html("#component_"+ele.id+ " "+ele.style.selector+"{"+temp_style+"}");
    $("#component_display").append(style);
    return element;
}
function loadComponent() {
    var display = $("#component_display");
    var element, rendered = [], i = 0;
    $.each(component, function(key, ele) {
        if(key == "basic") {
            return true;
        } else if(key == "child") {
            rendered[key] = [];
            $.each(ele, function(c_key, c_ele){                
                rendered[key][i] = renderer(c_ele);
                i++;
            });
        } else {
            rendered[key] = renderer(ele);
        } 
    });    
    if(typeof rendered['child'] != 'undefined') {
        $.each(rendered['child'], function(key, value){
            rendered['self'].append(value);
        });
    }
    if(typeof rendered['parent'] != 'undefined') {
        rendered['parent'].append(rendered['self']);
        element = rendered['parent'];
    } else element = rendered['self'];
    display.append(element);
}
$('.toggler').click(function(){
    $(this).find('i').toggleClass('fa-toggle-up').toggleClass('fa-toggle-down');
});
$('.showhide').click(function(){
    $($(this).attr("data-target")).toggleClass('d-block').toggleClass('d-none');
});
$('#component').change(function(){
    if($(this).val()) {
        if(typeof json["component_name"] !== 'undefined' && json["component_name"] != $(this).val()) {
            var r = confirm("Discard Component "+json["component_name"]+ "?");
            if(r) {
                json = {};
                component = {};
                $('#component_display').html("");
                $('form').each(function() {
                this.reset();
                });
            } else {
                $(this).val($(this).attr("data-selected"));
                return false;
            }
        }
        $(this).attr("data-selected", $(this).val());
        json["component_name"] = $(this).val();
        json["classes"] = {};
        $("#component_setting_panel").addClass('d-block').removeClass('d-none');
        //$("#component_setting").collapse("show");
        $("#loading_component_setting").addClass('d-block').removeClass('d-none');
        $("#component_setting_form").addClass('d-none').removeClass('d-block');
        $.getJSON(url.loadComponent+"/"+$(this).val(), function(response, status){
            if(status == "success") {
                component = response;
                $("#loading_component_setting").addClass('d-none').removeClass('d-block');
                $("#component_setting_form").addClass('d-block').removeClass('d-none');
                loadComponent();
            }            
        });
    } else {
        $(this).val($(this).attr("data-selected"));
    }
});
$("#display_property").change(function(){
    if($(this).val()) {
        if(typeof json["component_name"] !== 'undefined') {
            $("#display_property_"+$(this).val()+"_panel").addClass('d-block').removeClass('d-none');
        } else {
            alert("No Component Selected");
        }
    }
});
$(".display_property_form").submit(function (e) {
    e.preventDefault();
    var formData = $(this).serializeArray();
    var property_name = $(this).attr("property-name");
    json["classes"][property_name] = [];
    for(var i = 0; i < formData.length; i++) {
        json["classes"][property_name].push(formData[i].value);
    }
    $("#display_property_"+property_name).collapse("hide");
});
$("#Display_value").change(function(){
    var screen = $("#Display_screen").val();
    var property = "";
    if(screen == "xs") {
        property = "d-"+$(this).val();
    } else {
        property = "d-"+screen+"-"+$(this).val();
    }
    var form = $(this).closest("form");
    var checkbox = '<div class="form-group">'+
    '<div class="form-check">'+
    '<input class="form-check-input Display_checkbox" type="checkbox" name="name" value="'+
    property
    +'" checked>'+
    '<label class="form-check-label">'+
    property+
    '</label>'+
    '</div>'+
    '</div>';
    form.prepend(checkbox);
    $(".Display_checkbox").change(function() {
        var ischecked= $(this).is(':checked');
        if(!ischecked) {
            $(this).closest(".form-group").remove();
        }
    });
});
$("#Spacing_size").change(function(){
    var screen = $("#Spacing_screen").val();
    var property = $("#Spacing_property").val();
    var side = $("#Spacing_sides").val();
    var size = $(this).val();
    var value = "";
    if(screen == "xs") {
        value = property+side+'-'+size;
    } else {
        value = property+side+'-'+screen+'-'+size;
    }
    var form = $(this).closest("form");
    var checkbox = '<div class="form-group">'+
    '<div class="form-check">'+
    '<input class="form-check-input Spacing_checkbox" type="checkbox" name="name" value="'+
    value
    +'" checked>'+
    '<label class="form-check-label">'+
    value+
    '</label>'+
    '</div>'+
    '</div>';
    form.prepend(checkbox);
    $(".Spacing_checkbox").change(function() {
        var ischecked= $(this).is(':checked');
        if(!ischecked) {
            $(this).closest(".form-group").remove();
        }
    });
});
$("#component_setting_form").submit(function (e) {
    e.preventDefault();
    var formData = $(this).serializeArray();
    json["setting"] = {};
    for(var i = 0; i < formData.length; i++) {
        //json["setting"][property_name].push(formData[i].value);
    }
    $("#component_setting").collapse("hide");
});
$("#load_wrapper_setting").click(function(){
    var ele = $("#wrapper_setting_container");
    var setting = "";
    if($(this).html() == "<i class=\"fa fa-level-down\"></i> Load Wrapper Settings") {
        $(this).html("<i class=\"fa fa-level-up\"></i> Remove Wrapper Settings");
        setting += '<input type="hidden" name="parent[category]" value="element">';
        setting += '<input type="hidden" name="parent[content_type]" value="element">';
        setting += '<input type="hidden" name="parent[node]" value="parent">';
        setting += '<input type="hidden" name="parent[var_attributes]" value="[]">';
        setting += '<div class="form-group">';
        setting += '<label>Start Tag</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="parent[start_tag]">';
        setting += '<small class="form-text text-muted">Starting HTML eg &lt;p&gt;,&lt;div&gt;</small>';
        setting += '</div>';
        setting += '<div class="form-group">';
        setting += '<label>End Tag</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="parent[end_tag]">';
        setting += '<small class="form-text text-muted">Ending HTML eg &lt;/p&gt;,&lt;/div&gt;</small>';
        setting += '</div>';
        setting += '<div class="form-group">';
        setting += '<label>Classes</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="parent[classes]" value="[]">';
        setting += '<small class="form-text text-muted">Classes in JSON eg: ["class1", "class2"]</small>';
        setting += '</div>';
        setting += '<div class="form-group">';
        setting += '<label>Attributes</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="parent[attributes]" value="{}">';
        setting += '<small class="form-text text-muted">Attributes in JSON eg: {"attr1":"value1", "attr2":"value2"}</small>';
        setting += '</div>';
        setting += '<div class="form-group">';
        setting += '<label>Style</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="parent[style]" value="{&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{}}">';
        setting += '<small class="form-text text-muted">Style in JSON eg: {&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{&quot;width&quot;:&quot;100%&quot;}}</small>';
        setting += '</div>';
    } else {
        $(this).html("<i class=\"fa fa-level-down\"></i> Load Wrapper Settings");
    }
    ele.html(setting);
});
$("#component_content_type").change(function(){
    if($(this).val() == "static") {
        $("#childrenContentaccordion").removeClass("d-block").addClass("d-none");
        $("input[name='self[content]']").val("May Contain Some Text or Another Component");
    } else {
        $("#childrenContentaccordion").removeClass("d-none").addClass("d-block");
        $("input[name='self[content]']").val("");
    }
});
$("#load_child_setting").click(function(){
    var ele = $("#child_setting_container");
    var setting = "";
    var childCount = ele.children().length + 1;
    setting += '<div class="m-2 border p-2">';
    setting += '<h4>Child'+childCount+' <small class="pull-right mr-1" data-toggle="collapse" data-target="#collapsechild'+childCount+'"><i class="fa fa-wrench"></i></small></h4><div class="collapse" id="collapsechild'+childCount+'">';
    setting += '<input type="hidden" name="child['+childCount+'][category]" value="element">';
    setting += '<input type="hidden" name="child['+childCount+'][child_order]" value="'+childCount+'">';
    setting += '<input type="hidden" name="child['+childCount+'][content_type]" value="static">';
    setting += '<input type="hidden" name="child['+childCount+'][node]" value="child">';
    setting += '<input type="hidden" name="child['+childCount+'][var_attributes]" value="[]">';
    setting += '<div class="form-group">';
    setting += '<label>Start Tag</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="child['+childCount+'][start_tag]">';
    setting += '<small class="form-text text-muted">Starting HTML eg &lt;p&gt;,&lt;div&gt;</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>End Tag</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="child['+childCount+'][end_tag]">';
    setting += '<small class="form-text text-muted">Ending HTML eg &lt;/p&gt;,&lt;/div&gt;</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>Content</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="child['+childCount+'][content]" value="May Contain Some Text or Another Component">';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>Classes</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="child['+childCount+'][classes]" value="[]">';
    setting += '<small class="form-text text-muted">Classes in JSON eg: ["class1", "class2"]</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>Attributes</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="child['+childCount+'][attributes]" value="{}">';
    setting += '<small class="form-text text-muted">Attributes in JSON eg: {"attr1":"value1", "attr2":"value2"}</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>Style</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="child['+childCount+'][style]" value="{&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{}}">';
    setting += '<small class="form-text text-muted">Style in JSON eg: {&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{&quot;width&quot;:&quot;100%&quot;}}</small>';
    setting += '</div>';
    setting += '</div></div>';
    ele.append(setting);
});
