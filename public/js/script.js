function element_renderer(ele) {
    var element = $(ele.start_tag+ele.end_tag);
    var style = $("<style></style>");
    element.attr("id", "component_"+ele.id);
    $.each(ele.classes, function(key, value){
        element.addClass(value);
    });
    $.each(ele.attributes, function(key, value){
        element.attr(key,value);
    });
    if(ele.visibility != "show") {
        element.attr("data-toggle","popover");
        element.attr("data-trigger","hover");
        if(ele.visibility == "none") {
            element.addClass("Removed");
            element.attr("data-content","It is set to be removed.");
        } else {
            element.addClass("Conditional");
            element.attr("data-content","It is visible if user is " +ele.visibility+ ".");
        }
        element.attr("title","Visibility");
        element.attr("data-placement","bottom");
    }
    if(ele.content_type == "static" || ele.content_type == "variable")
    element.html(ele.content);
    else
    element.html("<div class='p-5 text-center text-light bg-info'>@@Component@@</div>");
    var temp_style = "";
    $.each(ele.style.style, function(key, value) {
        temp_style += key+':'+value+';';
    });
    style.html("#component_"+ele.id+ " "+ele.style.selector+"{"+temp_style+"}");
    $("#component_display").append(style);
    return element;
}
function component_renderer(comp) {
    var component_, rendered = [], i = 0;
    $.each(comp, function(key, ele) {
        if(key == "basic") {
            return true;
        } else if(key == "child") {
            rendered[key] = [];
            $.each(ele, function(c_key, c_ele){                
                rendered[key][i] = element_renderer(c_ele);
                i++;
            });
        } else {
            rendered[key] = element_renderer(ele);
        } 
    });    
    if(typeof rendered['child'] != 'undefined') {
        rendered['self'].html("");
        $.each(rendered['child'], function(key, value){            
            rendered['self'].append(value);
        });
    }
    if(typeof rendered['parent'] != 'undefined') {
        rendered['parent'].html("");
        rendered['parent'].append(rendered['self']);
        component_ = rendered['parent'];
    } else component_ = rendered['self'];
    return component_;
}
function loadComponent() {
    var display = $("#component_display");
    display.html("");
    display.append(component_renderer(component));
    display.find('[data-toggle="popover"]').popover();
}
function getter(node, property, key) {
    var setting;
    if(node == "child")
    setting = component[node][key];
    else
    setting = component[node];
    return setting[property];    
}
function setter(node, id, property, value) {
    if(property == "style") {
        try {
            value = JSON.parse(value);
        } catch (e) {
            alert("Invalid JSON in COMPONENT STYLE value");
            return false;
        }
    }
    if(node == "child") {
        if(typeof component.child != "undefined") {
            $.each(component.child, function(key, value) {
                if(value.id == id) {
                    if(property == "visibility" && value == "none")
                    delete component[node][key];
                    else
                    component[node][key][property] = value;
                }
            });
        }
    } else if(node == "parent") {
        if(typeof component[node] != "undefined") {
            component[node][property] = value;
        }
    } else if(node == "self") {
        if(typeof component[node] != "undefined") {
            component[node][property] = value;
            if(property == "content_type" && value != "element") {
                if(typeof component.child != "undefined")
                delete component.child;
            }
        }
    }    
}
function childOrder(child_key) {
    var i = 1, prevChild, prevKey, temp, tempKey;
    if(component.child.length > 1) {
        $.each(component.child, function(key, value) {
            if(key == child_key) {
                prevChild = temp;
                prevKey = tempKey;
            }
            temp = value;
            tempKey = key;
        });
        component.child[prevKey] = component.child[child_key];
        component.child[child_key] = prevChild;
        $.each(component.child, function(key, value) {
            component.child[key].child_order = i++;
        });
        loadComponent();
    }
}
function settingFormData(node, element) {
    var setting = "";
    setting += '<div class="form-group">';
    setting += '<label>Visibility</label>';
    setting += '<select class="form-control" name="visibility">';
    if(element.visibility == "show")
    setting += '<option selected>show</option>';
    else
    setting += '<option>show</option>';
    if(element.visibility == "auth")
    setting += '<option selected>auth</option>';
    else
    setting += '<option>auth</option>';
    if(element.visibility == "guest")
    setting += '<option selected>guest</option>';
    else
    setting += '<option>guest</option>';
    if(node == "child") {
        if(element.visibility == "none")
        setting += '<option selected>none</option>';
        else
        setting += '<option>none</option>';
    }
    setting += '</select>';
    setting += '</div>';
    if(node != "parent") {
        setting += '<div class="form-group">';
        setting += '<label>Content Type</label>';
        setting += '<select class="form-control" name="content_type">';
        if(element.content_type == "static")
        setting += '<option selected>static</option>';
        else
        setting += '<option>static</option>';
        if(element.content_type == "variable")
        setting += '<option selected>variable</option>';
        else
        setting += '<option>variable</option>';
        if(element.content_type == "component")
        setting += '<option selected>component</option>';
        else
        setting += '<option>component</option>';
        if(node == "self") {
            if(element.content_type == "element")
            setting += '<option selected>element</option>';
            else
            setting += '<option>element</option>';
        }
        setting += '</select>';
        setting += '</div>';
        setting += '<div class="form-group">';
        setting += '<label>Content</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="content" value="'+((element.content)?element.content:'')+'">';
        setting += '<small class="form-text text-muted">Required If content type Variable|Static.</small>';
        setting += '</div>';
    }
    if(node == "child") {
        setting += '<div class="form-group">';
        setting += '<label>Duplicate</label>';
        setting += '<input type="number" autocomplete="off" class="form-control" name="loop">';
        setting += '<small class="form-text text-muted">Repeat element X times.</small>';
        setting += '</div>';
    }
    setting += '<div class="form-group">';
    setting += '<label>Start Tag</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="start_tag" value="'+element.start_tag+'">';
    setting += '<small class="form-text text-muted">Starting HTML eg &lt;p&gt;,&lt;div&gt;</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>End Tag</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="end_tag" value="'+element.end_tag+'">';
    setting += '<small class="form-text text-muted">Ending HTML eg &lt;/p&gt;,&lt;/div&gt;</small>';
    setting += '</div>';
    setting += '<label>Attributes</label>';
    $.each(element.attributes, function(key, value) {
        setting += '<div class="form-check">';
        setting += '<label class="form-check-label">';
        setting += '<input type="checkbox" class="form-check-input" name="attributes" value="'+key+'" checked>';
        setting += key+ " : " +value;
        setting += '</label>';
        setting += '</div>';
    });
    setting += '<hr>';
    setting += '<label>Classes</label>';
    $.each(element.classes, function(key, value){
        setting += '<div class="form-check">';
        setting += '<label class="form-check-label">';
        setting += '<input type="checkbox" class="form-check-input" name="classes" value="'+value+'" checked>';
        setting += value;
        setting += '</label>';
        setting += '</div>';
    });
    setting += '<hr>';
    setting += '<div class="form-group"> ';
    setting += '<label>Style</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="style" value=\''+JSON.stringify(element.style)+'\'>';
    setting += '<small class="form-text text-muted">Style in JSON eg: {&quot;selector&quot;:&quot;&quot;, &quot;style&quot;:{&quot;width&quot;:&quot;100%&quot;}}</small>';
    setting += '</div>';
    setting += '<div class="form-group"> ';
    setting += '<label>Attribute</label>                          ';
    setting += '<input type="text" autocomplete="off" class="form-control" placeholder="Attribute Name" name="attribute_key">';
    setting += '<input type="text" autocomplete="off" class="form-control" placeholder="Attribute Value" name="attribute_value">';
    setting += '</div>';
    return setting;
}
function addNewElement(node) {
    var element = JSON.parse('{"id":0,"geolocation":0,"name":"","category":"element","node":"","visibility":"show","content_type":"","child_order":1,"nested_component":null,"loop_source":null,"start_tag":"","end_tag":"","attributes":{},"var_attributes":[],"classes":[],"style":{"selector":"","style":{}},"content":null}');
    element.node = node;
    if(node == "parent") {
        if(typeof component.parent == "undefined") {
            component["parent"] = element;
        }
    } else if(node == "child") {
        if(typeof component.child == "undefined") {
            component["child"] = [];
            component["child"][0] = element;
        } else {
            element.child_order = component.child.length + 1;
            component.child.push(element);
        }
    }    
}
function addChildForm(childnumber) {
    var setting = '<h4>Add Child</h4>';
    setting += '<input type="hidden" name="category" value="element">';
    setting += '<input type="hidden" name="child_order" value="'+childnumber+'">';
    setting += '<div class="form-group">';
    setting += '<label>Start Tag</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="start_tag">';
    setting += '<small class="form-text text-muted">Starting HTML eg &lt;p&gt;,&lt;div&gt;</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>End Tag</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="end_tag">';
    setting += '<small class="form-text text-muted">Ending HTML eg &lt;/p&gt;,&lt;/div&gt;</small>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>Content Type</label>';
    setting += '<select class="form-control" name="content_type">';
    setting += '<option selected>static</option>';
    setting += '<option>variable</option>';
    setting += '<option>component</option>';
    setting += '</select>';
    setting += '</div>';
    setting += '<div class="form-group">';
    setting += '<label>Content</label>';
    setting += '<input type="text" autocomplete="off" class="form-control" name="content" value="Some Static TEXT">';
    setting += '<small class="form-text text-muted">Required If content type Variable|Static.</small>';
    setting += '</div>';
    return setting;
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
                $('#parent_tab_content').html("");
                $('#component_tab_content').html("");
                $('#child_tab_content').html("");
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
        $("#component_setting_form_container").addClass('d-none').removeClass('d-block');
        $.getJSON(url.loadComponent+"/"+$(this).val(), function(response, status){
            if(status == "success") {
                component = response;
                $("#loading_component_setting").addClass('d-none').removeClass('d-block');
                $("#component_setting_form_container").addClass('d-block').removeClass('d-none');
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
$('#component_setting a[data-toggle="tab"]').click(function () {
    var display = $("#component_setting_form_subcontainer");
    var highlight = $("#highlight").is(':checked');
    var setting = "";
    display.html("<div class=\"m-2 p-2\"><i class=\"fa fa-gear\"></i> No Settings to display.</div>");
    if(this.id == "parent_tab") {
        if(typeof component.parent != "undefined") {
            setting = settingFormData("parent", component.parent);
            display.html('<form id="component_setting_form">'+setting+'<button type="submit" class="btn btn-primary">Preview</button></form>');
            $("#component_setting_form").submit(function (e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                updateComponent(formData);
                $("#component_setting").collapse("hide");
            });
        } else {
            display.append("Add Parent");
        }
    } else if(this.id == "component_tab") {
        setting = settingFormData("self", component.self);
        display.html('<form id="component_setting_form">'+setting+'<button type="submit" class="btn btn-primary">Preview</button></form>');
        $("#component_setting_form").submit(function (e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            updateComponent(formData);
            $("#component_setting").collapse("hide");
        });
    } else if(this.id == "child_tab") {
        if(typeof component.child != "undefined") {
            display.html("");
            var i = 0;
            setting = '<div class="d-flex flex-wrap mb-3">';
            $.each(component.child, function(key, child) {                
                setting += '<div class="d-flex ml-1 border border-info">';
                setting += '<div class="mr-auto py-1 px-2">'+child.id+'.<b>'+child.start_tag.replace("<","").replace(">","")+'</b></div>';
                if(i)
                setting += '<a class="p-1 child_pointer" href="#" id="orderLeft" data-key="'+ key +'"><i class="fa fa-arrow-left"></i></a>';
                setting += '<a class="p-1 child_pointer" href="#" id="loadChildSetting" data-key="'+ key +'"><i class="fa fa-gear"></i></a>';
                setting += '</div>';
                i++;
            });
            setting += '</div>';
            display.append(setting);
            $(".child_pointer").click(function(e){
                e.preventDefault();
                if(this.id == "orderLeft") {
                    childOrder($(this).attr("data-key"));
                    $("#child_tab").click();
                } else if(this.id == "loadChildSetting") {
                    var display = $("#component_setting_form_subcontainer");
                    var setting = settingFormData("child", component.child[$(this).attr("data-key")]);
                    display.find("#component_setting_form").remove();
                    display.append('<form id="component_setting_form">'+setting+'<button type="submit" class="btn btn-primary">Preview</button></form>');
                }
            });
        }
    }
});
function updateComponent(formData) {

}
function newComponent(formData) {

}
