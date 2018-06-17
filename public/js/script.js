function element_renderer(ele, highlight = false, recrusive = false, pointer = "") {
    ele_id++;
    var element = $(ele.start_tag+ele.end_tag);
    var style = $("<style></style>");
    var temp_style = "";
    $.each(ele.style.style, function(key, value) {
        temp_style += key+':'+value+';';
    });
    style.html("#component_"+ele_id+ " "+ele.style.selector+"{"+temp_style+"}");
    $("#component_display").append(style);
    element.attr("id", "component_"+ele_id);
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
    if(highlight) {
        element.addClass("highlighter");
    }
    if(pointer)element.addClass(pointer.replace(".","_"));
    if(ele.content_type == "static" || ele.content_type == "variable")
    element.html(((typeof ele.content == "string" && ele.content != "")?ele.content:'@@'+ele.content_type+'@@'));
    else {
        if(ele.content != null && typeof ele.content === "object" && recrusive) {
            element.html("");
            element.append(component_renderer(ele.content, recrusive, pointer));
        } else if(recrusive && pointer != "" && !$.isEmptyObject(component) && pointer == stackPointer) {
            element.append(component_renderer(component));
        } else
        element.html("<div class='p-5 text-center text-light bg-info'>@@"+ele.content_type+"@@</div>");
    }
    return element;
}
function component_renderer(comp, recrusive = false, pointer = "") {
    var highlight = $("#highlight");
    var component_, rendered = [], i = 0;
    if(pointer) pointer += '.';
    $.each(comp, function(key, ele) {
        if(key == "child") {
            rendered[key] = [];
            $.each(ele, function(c_key, c_ele){
                if(!recrusive && highlight.is(':checked') && highlight.attr("data-highlight") == "child" && highlight.attr("data-childKey") == c_key)
                rendered[key][i] = element_renderer(c_ele, true, recrusive, pointer+c_key);
                else
                rendered[key][i] = element_renderer(c_ele, false, recrusive, pointer+c_key);
                i++;
            });
        } else if(key == "self") {
            if(highlight.is(':checked') && highlight.attr("data-highlight") == key)
            rendered[key] = element_renderer(ele, true, recrusive, ((typeof comp.child === "undefined")?pointer+'self':''));
            else
            rendered[key] = element_renderer(ele, false, recrusive);
        } else {
        if(highlight.is(':checked') && highlight.attr("data-highlight") == key)
        rendered[key] = element_renderer(ele, true, recrusive);
        else
        rendered[key] = element_renderer(ele, false, recrusive);
    } 
    });    
    if(typeof rendered['child'] !== "undefined") {
        rendered['self'].html("");
        $.each(rendered['child'], function(key, value){            
            rendered['self'].append(value);
        });
    }
    if(typeof rendered['parent'] !== "undefined") {
        rendered['parent'].html("");
        rendered['parent'].append(rendered['self']);
        component_ = rendered['parent'];
    } else component_ = rendered['self'];
    return component_;
}
function loadComponent() {
    var display = $("#component_display");
    display.html("");
    if($("#loadstack").is(':checked') && !$.isEmptyObject(stack) && typeof stack.self !== 'undefined')
    display.append(component_renderer(stack, true, ((typeof stack.child === "undefined")?'self':'')));
    else
    display.append(component_renderer(component, false,  ((typeof component.child === "undefined")?'self':'')));
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
function setter(node, property, value, key = "") {
    if(node == "child") {
        if(typeof component.child[key] !== "undefined") {
            if(property == "visibility" && value == "none") {
                component[node].splice(key,1);
                if(!component.child.length) {
                    delete component["child"];
                    component.self.content_type = "static";
                }
            } else
            component[node][key][property] = value;
        }
    } else if(node == "parent") {
        if(typeof component[node] !== "undefined") {
            component[node][property] = value;
        }
    } else if(node == "self") {
        if(typeof component[node] !== "undefined") {
            component[node][property] = value;
            if(property == "content_type" && value != "element") {
                if(typeof component.child !== "undefined")
                delete component["child"];
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
    var setting = '<h5><b>'+element.start_tag.replace("<","").replace(">","")+'</b>_<small>'+element.id+'</small></h5><hr>';
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
    // if(node == "child") {
    //     if(element.visibility == "none")
    //     setting += '<option selected>none</option>';
    //     else
    //     setting += '<option>none</option>';
    // }
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
        if(element.content_type == "element")
        setting += '<option selected>element</option>';
        else
        setting += '<option>element</option>';
        setting += '</select>';
        setting += '</div>';
        setting += '<div class="form-group">';
        setting += '<label>Content</label>';
        setting += '<input type="text" autocomplete="off" class="form-control" name="content" value="'+((typeof element.content != "object")?element.content:'')+'">';
        setting += '<small class="form-text text-muted">Required If content type Variable|Static.</small>';
        setting += '</div>';
    }
    // if(node == "child") {
    //     setting += '<div class="form-group">';
    //     setting += '<label>Duplicate</label>';
    //     setting += '<input type="number" autocomplete="off" class="form-control" name="loop">';
    //     setting += '<small class="form-text text-muted">Repeat element X times.</small>';
    //     setting += '</div>';
    // }
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
        setting += '<input type="checkbox" class="form-check-input" name="attributes" value=\'{"'+key+'":"'+value+'"}\' checked>';
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
    setting += '<input type="text" autocomplete="off" class="form-control" name="attributes" value="{}">';
    //setting += '<input type="text" autocomplete="off" class="form-control" placeholder="Attribute Value" name="attribute_value">';
    setting += '<small class="form-text text-muted">Attributes in JSON eg: {"attr1":"value1", "attr2":"value2"}</small>';
    setting += '</div>';
    return setting;
}
function addNewElement(node) {
    var element = {id:1,geolocation:0,name:"",category:"element",node:"",visibility:"show",content_type:"static",child_order:1,nested_component:null,loop_source:null,start_tag:"<div>",end_tag:"</div>",attributes:{},var_attributes:[],classes:[],style:{selector:"",style:{}},content:null};
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
            var id = 0;
            $.each(component.child, function(k,v){
                if(v.id > id)
                id = v.id;
            });
            element.id = id+1;
            element.child_order = component.child.length + 1;
            component.child.push(element);
        }
    } else if(node == "self") {
        if(typeof component !== "undefined") {
            component["self"] = element;
        }
    }
}
function addNewComponent(path) {
    //path stack = {"self":{"content":"value"}}
}
function duplicateChildElement(key) {
    if(typeof component.child[key] !== "undefined") {
        var temp = {}, id = 0;
        $.each(component.child, function(k,v){
            if(v.id > id)
            id = v.id;
        });
        $.each(component.child[key], function(key_, value){
            temp[key_] = value;
        });
        temp.id = id+1;
        component.child.push(temp);
    }
    var i = 1;
    $.each(component.child, function(key, value) {
        component.child[key].child_order = i++;
    });
    $("#child_tab").click();
    loadComponent();
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
    setting += '<option>element</option>';
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
$("#BrowseComponents").click(function(){
    if(!$.isEmptyObject(component) && typeof component.self !== 'undefined') {
        var r = confirm("Discard Component "+$("#component_name").val()+ "?");
        if(r) {
            component = {};
            $('#component_display').html("");
            $('form').each(function() {
            this.reset();
            });
        } else {
            return false;
        }
    }
    var display = $("#basicComponentContainer");
    display.addClass("d-block").removeClass("d-none");
    var container = $('<div class="card m-2">'+
    '<h4 class="card-header text-capitalize text-center">'+
    '</h4>'+
    '<div class="card-body">'+
    '</div>'+
    '<div class="card-footer text-muted text-center">'+
    '<a href="#" data-name="" class="btn btn-primary useComponent">Use Component</a>'+
    '</div>'+
    '</div>');
    display.html("<div id=\"basicLoader\" class=\"m-2 p-2 text-center\"><h2><i class=\"fa fa-spinner\"></i> Loading Components.</h2></div>");
    $.getJSON(url.loadComponents, function(response, status){
        if(status == "success") {
            $.each(response, function(i, value){
                container.find(".card-header").html(value.self.name);
                container.find(".card-body").html("").append(component_renderer(value, true));
                container.find(".useComponent").attr("data-name",value.self.name);
                display.append(container.clone());
            });
            display.find("#basicLoader").remove();
            display.prepend('<h2 class="text-center">Basic Components</h2><hr>');
            $(".useComponent").click(function(e){
                e.preventDefault();
                componentToEditor($(this).attr("data-name"));
                $("#basicComponentContainer").addClass("d-none").removeClass("d-block");;
            });
        }
    });
});
function componentToEditor(name = "") {
    if(name != "") {
        $("#component_setting_panel").addClass('d-block').removeClass('d-none');
        $("#loading_component_setting").addClass('d-block').removeClass('d-none');
        $("#component_setting_form_container").addClass('d-none').removeClass('d-block');
        $.getJSON(url.loadComponent+"/"+name, function(response, status){
            if(status == "success") {
                $("#component_name").val(response.self.name);
                component = response;
                $("#loading_component_setting").addClass('d-none').removeClass('d-block');
                $("#component_setting_form_container").addClass('d-block').removeClass('d-none');
                loadComponent();
                $("#component_tab").click();
            }            
        });
    }
}
$("#display_property").change(function(){
    if($(this).val()) {
        if(!$.isEmptyObject(component) && typeof component.self !== 'undefined') {
            var ele = $("#display_property_"+$(this).val()+"_panel");
            if(ele.hasClass("d-none")) {
                ele.addClass('d-block').removeClass('d-none');
                ele.appendTo("#setting_panel");
            }
        } else {
            alert("No Component Selected");
        }
    }
});
$(".display_property_form").submit(function (e) {
    e.preventDefault();
    $(window).scrollTop(0);
    var node = $("#component_setting_panel").attr("data-node");
    var key = "";
    var panel = "";
    if(typeof node !== 'undefined' && typeof component[((node == "children")?'child':node)] !== "undefined") {
        if(node == "children") {
            panel = "#child_tab";
        } else if( node == "self") {
            panel = "#component_tab";
        } else if (node == "child") {
            panel = "#child_tab";
        }
        else panel = "#parent_tab";
        var formData = $(this).serializeArray();
        var classes = {};
        var tempClasses = {};
        var finalClasses;
        for(var i = 0; i < formData.length; i++) {
            classes[formData[i].value] = "-";
        }
        if(node == "children") {
            $.each(component.child, function(key_, value){
                tempClasses = classes;
                $.each(value.classes, function(k, v){
                    tempClasses[v] = "-";
                });
                finalClasses = [];
                $.each(tempClasses, function(c, v){
                    finalClasses.push(c);
                });
                setter("child", "classes", finalClasses, key_);
            });
        } else {
            if(node == "child") {
                key = $("#component_setting_panel").attr("data-key");
                if(typeof key !== 'undefined' && typeof component.child[key] !== "undefined") {
                    tempClasses = classes;
                    $.each(component.child[key].classes, function(k, v){
                        tempClasses[v] = "-";
                    });
                    finalClasses = [];
                    $.each(tempClasses, function(c, v){
                        finalClasses.push(c);
                    });
                    setter("child", "classes", finalClasses, key);
                }
            } else {
                tempClasses = classes;
                $.each(component[node].classes, function(k, v){
                    tempClasses[v] = "-";
                });
                finalClasses = [];
                $.each(tempClasses, function(c, v){
                    finalClasses.push(c);
                });
                setter(node, "classes", finalClasses, key);
            }
        }
        $(panel).click();
        if(node == "child")
        $("a[data-name='loadChildSetting'][data-key='"+key+"']").click();
        loadComponent();
    }
    $("#display_property_"+$(this).attr("property-name")).collapse("hide");
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
    var highlight = $("#highlight");
    var setting = "";
    display.html("<div class=\"m-2 p-2\"><i class=\"fa fa-gear\"></i> No Settings to display.</div>");
    if(this.id == "parent_tab") {
        $("#component_setting_panel").attr("data-node","parent");
        if(typeof component.parent !== "undefined") {
            highlight.attr("data-highlight","parent");
            setting = settingFormData("parent", component.parent);
            display.html('<form id="component_setting_form">'+setting+'<label class="pull-right"><input type="checkbox" id="previewCollapse" checked> Collapse</label><button type="submit" class="btn btn-primary">Preview</button></form>');
            $("#component_setting_form").submit(function (e) {
                e.preventDefault();
                $(window).scrollTop(0);
                var formData = $(this).serializeArray();
                updateComponent(formData, "parent");
                if($("#previewCollapse").is(":checked"))
                $("#component_setting").collapse("hide");
            });
        } else {
            display.append('<a class="p-1 text-success" title="Add Wrapper to Component" href="#" id="newParent"><i class="fa fa-plus-square"></i> Add Wrapper</a>');
            $("#newParent").click(function(e){
                e.preventDefault();
                addNewElement("parent");
                $("#parent_tab").click();
                loadComponent();
            });
        }
    } else if(this.id == "component_tab") {
        $("#component_setting_panel").attr("data-node","self");
        if(typeof component.self !== "undefined") {
            highlight.attr("data-highlight","self");
            setting = settingFormData("self", component.self);
            display.html('<form id="component_setting_form">'+setting+'<label class="pull-right"><input type="checkbox" id="previewCollapse" checked> Collapse</label><button type="submit" class="btn btn-primary">Preview</button></form>');
            $("#component_setting_form").submit(function (e) {
                e.preventDefault();
                $(window).scrollTop(0);
                var formData = $(this).serializeArray();
                updateComponent(formData, "self");
                if($("#previewCollapse").is(":checked"))
                $("#component_setting").collapse("hide");
            });
        }
    } else if(this.id == "child_tab") {
        $("#component_setting_panel").attr("data-node","children");
        if(typeof component.child !== "undefined") {            
            display.html("");
            var i = 0;
            setting = '<div class="d-flex flex-wrap mb-3">';
            $.each(component.child, function(key, child) {
                setting += '<div class="d-flex ml-0 m-1 border border-info">';
                setting += '<div class="mr-auto py-1 px-2"><b>'+child.start_tag.replace("<","").replace(">","")+'</b>_<small>'+child.id+'</small></div>';
                if(i)
                setting += '<a class="p-1 child_pointer" title="Shift" href="#" data-name="orderLeft" data-key="'+ key +'"><i class="fa fa-angle-double-left"></i></a>';
                //setting += '<div class="d-flex flex-column">';
                setting += '<a class="p-1 child_pointer text-warning" title="Duplicate" href="#" data-name="duplicate" data-key="'+ key +'"><i class="fa fa-copy"></i></a>';
                setting += '<a class="p-1 child_pointer text-danger" title="Remove" href="#" data-name="remove" data-key="'+ key +'"><i class="fa fa-trash-o"></i></a>';
                //setting += '</div>';
                setting += '<a class="p-1 child_pointer" title="Setting" href="#" data-name="loadChildSetting" data-key="'+ key +'"><i class="fa fa-gears"></i></a>';
                setting += '</div>';
                i++;
            });
            //setting += '<div class="d-flex ml-0 m-1 border bg-warning text-light">';
            //setting += '<div class="mr-auto py-1 px-2">new</div>';
            setting += '<a class="p-1 child_pointer text-success" title="Create Child" href="#" data-name="newChild"><i class="fa fa-plus-square"></i></a>';
            //setting += '</div>';
            //setting += '</div>';
            display.append(setting);
            display.find('[data-toggle="popover"]').popover();
            $(".child_pointer").click(function(e){
                e.preventDefault();
                $("#component_setting_panel").attr("data-node","child");
                $("#component_setting_panel").attr("data-key",$(this).attr("data-key"));
                if($(this).attr("data-name") == "orderLeft") {
                    childOrder($(this).attr("data-key"));
                    $("#child_tab").click();
                } else if($(this).attr("data-name") == "loadChildSetting") {
                    var display = $("#component_setting_form_subcontainer");
                    var highlight = $("#highlight");
                    highlight.attr("data-highlight","child");
                    highlight.attr("data-childKey",$(this).attr("data-key"));
                    var setting = settingFormData("child", component.child[$(this).attr("data-key")]);
                    display.find("#component_setting_form").remove();
                    display.append('<form id="component_setting_form" data-key="'+$(this).attr("data-key")+'">'+setting+'<label class="pull-right"><input type="checkbox" id="previewCollapse" checked> Collapse</label><button type="submit" class="btn btn-primary">Preview</button></form>');
                    if(highlight.is(":checked"))
                    loadComponent();
                    $("#component_setting_form").submit(function (e) {
                        e.preventDefault();
                        $(window).scrollTop(0);
                        var formData = $(this).serializeArray();
                        updateComponent(formData, "child", $(this).attr("data-key"));
                        if($("#previewCollapse").is(":checked"))
                        $("#component_setting").collapse("hide");
                    });
                } else if($(this).attr("data-name") == "duplicate") {
                    duplicateChildElement($(this).attr("data-key"));
                } else if($(this).attr("data-name") == "remove") {
                    setter("child", "visibility", "none", key = $(this).attr("data-key"));
                    $("#child_tab").click();
                    loadComponent();
                } else if($(this).attr("data-name") == "newChild") {
                    addNewElement("child");
                    $("#child_tab").click();
                    loadComponent();
                }
            });
        }
   }
   if(highlight.is(":checked"))
   loadComponent();
});
$("#highlight").change(function() {
    loadComponent();
});
$("#loadstack").change(function() {
    if(!$.isEmptyObject(stack) && typeof stack.self !== 'undefined') {
    loadComponent();
    }
});
function updateComponent(formData, node, childkey="") {
    var tempSetting = {attributes:{},classes:[]};
    for(var i = 0; i < formData.length; i++) {
        if(formData[i].name == "style" || formData[i].name == "attributes") {
            try {
                formData[i].value = JSON.parse(formData[i].value);
            } catch (e) {
                alert("Invalid JSON found in component setting.");
            }
        }
        if(formData[i].name == "attributes") {
            for(key in formData[i].value)
            tempSetting.attributes[key] = formData[i].value[key];
        } else if(formData[i].name == "classes") {
            tempSetting.classes.push(formData[i].value);
        } else tempSetting[formData[i].name] = formData[i].value;
    }
    $.each(tempSetting, function(property, value){
        setter(node, property, value, childkey);
    });
    loadComponent();
}
function newComponent(formData) {

}
function stackSettingRender(container, display, pointer, key, val) {
    if(key == "child") {
        display.html("");
        $.each(val, function(k, v){
            if(v.content_type == "element") {
                display.prepend('<button class="btn btn-default" type="button" class="selectStackIndex" value="'+
                pointer+k
                +'"><i class="fa fa-cube"></i> '+v.start_tag.replace("<","").replace(">","")+'_'+v.id+'</button>');
            }
        });
        container.append(display.clone());
        $.each(val, function(k, v){
            if(v.content_type == "element") {
                if(v.content != null && typeof v.content === "object") {
                    if(typeof v.content.child !== "undefined" && v.content.child != null && typeof v.content.child === "object")
                        stackSettingRender(container, display, pointer+k+'.', "child", v.content.child);
                    else
                        stackSettingRender(container, display, pointer+k+'.', "self", v.content.self);
                }
            }
        });
    } else if(key == "self" && val.content_type == "element") {
        display.html("");
        display.prepend('<button class="btn btn-default" type="button" class="selectStackIndex" value="'+
        pointer+"self"
        +'"><i class="fa fa-cube"></i> '+val.start_tag.replace("<","").replace(">","")+'_'+val.id+'</button>');
        container.append(display.clone());
        if(val.content != null && typeof val.content === "object") {
            if(typeof val.content.child !== "undefined" && val.content.child != null && typeof val.content.child === "object")
                stackSettingRender(container, display, pointer+"self"+'.', "child", val.content.child);
            else
                stackSettingRender(container, display, pointer+"self"+'.', "self", val.content.self);
        }
    }
}
$("#savestack").click(function(){
    $(this).blur();
    if($(this).is(":disabled")) return;
    $(this).prop("disabled", false);
});
$("#showstack").click(function(){
    $(this).blur();
    if($.isEmptyObject(stack) || typeof stack.self !== "object"){
        alert("No component in Stack.");
        return;
    }
    var ele = $("#stack_panel");
    if(ele.hasClass("d-none")) {
        ele.addClass('d-block').removeClass('d-none');
        ele.appendTo("#setting_panel");
    }
    var container = $('<div id="stackBoxView" class="d-flex flex-column my-2"></div>');
    var display = $('<div class="d-flex justify-content-center"></div>');
    if(typeof stack.child !== "undefined" && stack.child != null && typeof stack.child === "object")
        stackSettingRender(container, display, "", "child", stack.child);
    else
        stackSettingRender(container, display, "", "self", stack.self);
        //stackPointer .replace(/^,/, '')

    stackPointer = ''+container.find("button:last").removeClass("btn-default").addClass("btn-success").val();
    $("#display_stack").html("").append(container);
    $(".selectStackIndex").click(function(){
        $(this).blur();
        stackPointer = ''+$(this).val();
        $(".selectStackIndex").removeClass("btn-success").addClass("btn-default");
        $(this).removeClass("btn-default").addClass("btn-success");
    });
});
$("#addtostack").click(function(){
    $(this).blur();
    if($.isEmptyObject(component)){
        alert("No Component to Add in Stack");
        return;
    }
    if($.isEmptyObject(stack)) {
        stack = component;
    } else if(stackPointer != "") {
        var temp = stack, pointer = stackPointer.split(".");
        pointer.forEach(function(index, key) {
            if(index == "self"){
                temp = temp.self;
            } else {
                temp = temp.child[index];
            }
            if(key == (pointer.length - 1) && temp.content_type == "element")
            temp.content = component;
        });
    }
    component = {};
    $("#component_display").html("");
    $("#component_name").val("");
    $("#component_tab").click();
});