@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">@if($component){{ucwords($operation)}} Component | {{$component->name}} @else Select Component @endif</h2>
        <div class="card-text">
            @if($component)
                @if($operation == "edit")
                <form action="{{ (isset($template_id))?route('Template.Component.edit'):route('Template.Page.Component.edit') }}" method="post">
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="redirectTo" value="{{$redirectTo}}">
                @else
                <form action="{{ (isset($template_id))?route('Template.Component.add'):route('Template.Page.Component.add') }}" method="post">
                @endif
                    <input type="hidden" name="id" value="{{$component->id}}">
                    @csrf
                    <input type="hidden" name="template_id" value="{{$template_id}}">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-text-width"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Identifying Name" tabindex="1" name="name" value="{{ old("name", (($component && $component->category == "web")?$component->name:'')) }}" required>
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text"><i class="fa fa-columns"></i></span>
                        </div>
                        <select class="custom-select component_type" tabindex="1">
                            <option value="body"{{(old("type.".$component->id, $component->type) == "body")?' selected':''}}>Body</option>
                            <option value="main"{{(old("type.".$component->id, $component->type) == "main")?' selected':''}}>Main</option>
                            <option value="header"{{(old("type.".$component->id, $component->type) == "header")?' selected':''}}>Header</option>
                            <option value="footer"{{(old("type.".$component->id, $component->type) == "footer")?' selected':''}}>Footer</option>
                        </select>
                    </div>
                    @include('Template.Forms.componentSetting', ["element" => $component])
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success submit_button" tabindex="2">Save</button>
                    </div>
                </form>
            @push('scripts')
                <?php $html = View::make('Component.partialRender')->withelement($component);?>
                <script>
                    $(".component_type").change(function(){
                        var val = $(this).val();
                        $(".component_type").each(function(){
                            $(this).val(val);
                        });
                    });
                    $(".child_order").change(function(){
                        var val = $(this).val();
                        var prev = $(this).attr("data-value");
                        var name = $(this).attr("name");
                        $(this).attr("data-value", val);
                        $("[data-group='"+$(this).attr("data-group")+"']").each(function(){
                            if($(this).attr("name") != name && $(this).val() == val) {
                                $(this).val(prev);
                                $(this).attr("data-value", prev);
                            }
                        });
                    });
                    $(".list-group-item").click(function(){
                        if($(this).hasClass("active"))
                        $(this).removeClass("active")
                        else
                        $(this).addClass("active")
                    });
                    $("#node_source").click(function(){
                        if($(this).hasClass("node_source_show")) {
                            $(this).removeClass("node_source_show").addClass("node_source_hidden");
                        }
                        else {
                            $(this).removeClass("node_source_hidden").addClass("node_source_show");
                        }
                    });
                    $("#html_source").click(function(){
                        if($(this).hasClass("html_source_show")) {
                            $(this).removeClass("html_source_show").addClass("html_source_hidden");
                        }
                        else {
                            $(this).removeClass("html_source_hidden").addClass("html_source_show");
                        }
                    });
                    var component = {!! str_replace(['\n','\r'], '', json_encode($html->render())) !!};
                    function html(str) {
                        var div = document.createElement('div');
                        div.innerHTML = str.trim();
                        return format(div, 0).innerHTML;
                    }
                    function format(node, level) {
                        var indentBefore = new Array(level++ + 1).join('  '),
                            indentAfter  = new Array(level - 1).join('  '),
                            textNode;
                        for (var i = 0; i < node.children.length; i++) {
                            textNode = document.createTextNode('\n' + indentBefore);
                            node.insertBefore(textNode, node.children[i]);
                            format(node.children[i], level);
                            if (node.lastElementChild == node.children[i]) {
                                textNode = document.createTextNode('\n' + indentAfter);
                                node.appendChild(textNode);
                            }
                        }
                        return node;
                    }
                    $("#html_source").text(html(component));
                </script>
            @endpush
            @push('styles')
                <style>
                    .html_source_show {
                        box-sizing: border-box;
                        position: fixed;
                        margin:0;
                        width: 500px;
                        height: 500px;
                        right:0;
                        bottom:0;
                        overflow: scroll;
                        z-index: 20;
                        border-left: 20px solid #f1f1f1 !important;
                        border-top: 20px solid #f1f1f1 !important;
                        box-shadow: -1px 2px 6px 2px #aaa;
                    }
                    .html_source_hidden {
                        box-sizing: border-box;
                        position: fixed;
                        margin:0;
                        padding: 0 !important;
                        width: 20px;
                        height: 500px;
                        right:0;
                        bottom:0;
                        overflow: hidden;
                        z-index: 20;
                        border-left: 20px solid #585858 !important;
                        box-shadow: -1px 2px 6px 2px #aaa;
                    }
                    .node_source_show {
                        position: fixed;
                        width: 200px;
                        height: 500px;
                        left:0;
                        bottom:0;
                        overflow-y: scroll;
                        z-index: 20;
                        border-top: 20px solid #f1f1f1 !important;
                        border-bottom: 20px solid #f1f1f1 !important;
                        box-shadow: 1px 2px 6px 2px #aaa;
                    }
                    .node_source_hidden {
                        box-sizing: border-box;
                        position: fixed;
                        width: 20px;
                        padding: 0 !important;
                        height: 500px;
                        left:0;
                        bottom:0;
                        overflow: hidden;
                        z-index: 20;
                        border-right: 20px solid #585858 !important;
                        box-shadow: 1px 2px 6px 2px #aaa;
                    }
                </style>
            @endpush
            @else
                <div id="ComponentContainer" class="p-1 rounded"></div>
                @push('scripts')
                <script>
                    var ele_id = 0;
                    function element_renderer(ele, highlight = false, pointer = "") {
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
                        if(highlight && pointer == "") {
                            element.addClass("highlighter");
                        } else if(pointer != "" && pointer == stackPointer) {
                            element.addClass("highlighter");
                        }
                        if(ele.content_type == "static" || ele.content_type == "variable")
                        element.html(((typeof ele.content == "string" && ele.content != "")?ele.content:'@@'+ele.content_type+'@@'));
                        else {
                            if(ele.content != null && typeof ele.content === "object" && pointer) {
                                element.html("");
                                element.append(component_renderer(ele.content, pointer));
                            } else if(pointer != "" && !$.isEmptyObject(component) && pointer == stackPointer) {
                                element.append(component_renderer(component, false));
                            } else
                            element.html("<div class='p-5 text-center text-light bg-info'>@@"+ele.content_type+"@@</div>");
                        }
                        return element;
                    }
                    function component_renderer(comp, pointer = "") {
                        var highlight = $("#highlight");
                        var component_, rendered = [], i = 0;
                        $.each(comp, function(key, ele) {
                            if(key == "child") {
                                rendered[key] = [];
                                $.each(ele, function(c_key, c_ele){
                                    if(highlight.is(':checked') && highlight.attr("data-highlight") == "child" && highlight.attr("data-childKey") == c_key)
                                    rendered[key][i] = element_renderer(c_ele, !$("#loadstack").is(":checked"), (($("#loadstack").is(":checked"))?((pointer)?pointer+'.'+c_key:((typeof pointer === "boolean")?'':c_key)):''));
                                    else
                                    rendered[key][i] = element_renderer(c_ele, false, (($("#loadstack").is(":checked"))?((pointer)?pointer+'.'+c_key:((typeof pointer === "boolean")?'':c_key)):''));
                                    i++;
                                });
                            } else if(key == "self") {
                                if(highlight.is(':checked') && highlight.attr("data-highlight") == key)
                                rendered[key] = element_renderer(ele, !$("#loadstack").is(":checked"),(($("#loadstack").is(":checked"))?((pointer)?pointer+'.self':((typeof pointer === "boolean")?'':'self')):''));
                                else
                                rendered[key] = element_renderer(ele, false, (($("#loadstack").is(":checked"))?((pointer)?pointer+'.self':((typeof pointer === "boolean")?'':'self')):''));
                            } else {
                            if(highlight.is(':checked') && highlight.attr("data-highlight") == key)
                            rendered[key] = element_renderer(ele, true);
                            else
                            rendered[key] = element_renderer(ele, false);
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
                    function loadComponents() {
                        var display = $("#ComponentContainer");
                        display.removeClass("bg-dark");
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
                        $.getJSON('{{ route('LoadComponents.component') }}', function(response, status){
                            if(status == "success") {
                                display.closest('.w-75').removeClass('w-75').addClass('w-100');
                                $.each(response, function(i, value){
                                    container.find(".card-header").html(value.self.name);
                                    container.find(".card-body").html("").append(component_renderer(value, true));
                                    container.find(".useComponent").attr("data-name",value.self.id);
                                    display.append(container.clone());
                                });
                                display.find("#basicLoader").remove();
                                display.addClass("bg-dark");
                                $(".useComponent").click(function(e){
                                    e.preventDefault();
                                    var display = $("#ComponentContainer");
                                    window.location = '{{ route('Template.Component', ['template_id' => $template_id, 'operation' => 'add']).'/' }}' + $(this).attr("data-name");
                                    display.closest('.w-100').removeClass('w-100').addClass('w-75');
                                    display.removeClass("bg-dark").html("<div id=\"basicLoader\" class=\"m-2 p-2 text-center\"><h2><i class=\"fa fa-spinner\"></i> Loading Component.</h2></div>");
                                });
                            }
                        });
                    }
                    $(function(){
                        loadComponents();
                    });
                </script>
                @endpush
            @endif
        </div>
    </div>
</div>
@if($component)
<div id="node_source" class="node_source_show bg-white border p-2 list-group">
    @stack('nodes')
</div>
<pre id="html_source" class="html_source_hidden bg-white border p-2"></pre>
@endif
@endsection
@push('title')
<title>@if($component){{ucwords($operation)}} Component | {{$component->name}} @else Select Component @endif</title>
@endpush