@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">@if($component){{ucwords($operation)}} Component to {{$page->title}}| {{$component->name}} @else Select Component @endif</h2>
        <div class="card-text">
            @if($component)
                <form action="{{ route('Template.Page.Component.add') }}" method="post">
                    <input type="hidden" name="id" value="{{$component->id}}">
                    @csrf
                    <input type="hidden" name="page_id" value="{{$page_id}}">
                    <input type="hidden" name="component_id" value="{{$component->id}}">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-cube"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Identifying Name" tabindex="1" value="{{ $component->name }}" disabled>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success submit_button" tabindex="2">Add to Page</button>
                    </div>
                </form>
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
                        $.getJSON('{{ route('LoadComponents.template', ["template_id" => $page->Template->id]) }}', function(response, status){
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
                                    window.location = '{{ ((isset($page_id))?route('Template.Page.Component', ['page_id' => $page_id, 'operation' => 'add']):route('Template.Component', ['template_id' => $template_id, 'operation' => 'add'])).'/' }}' + $(this).attr("data-name");
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
@endsection
@push('title')
<title>@if($component){{ucwords($operation)}} Component | {{$component->name}} @else Select Component @endif</title>
@endpush