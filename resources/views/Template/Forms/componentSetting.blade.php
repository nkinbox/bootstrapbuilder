@push('nodes')
<a href="#component_{{$element->node.'_'.$element->id}}" class="list-group-item list-group-item-action" data-toggle="collapse" tabindex="20"><i class="fa fa-cube"></i> {{$element->node.'_'.$element->id}}<?php
if($element->visibility == 'none') {
    echo ' <i class="fa fa-eye-slash text-danger"></i>';
} else {
    if($element->visibility != 'show') {
        echo (($element->visibility_id)?' <i class="fa fa-eye-slash text-success"></i>':' <i class="fa fa-eye text-success"></i>');
    } elseif($element->visibility_id) {
        echo ' <i class="fa fa-eye text-warning"></i>';
    }
}
?>{!!($element->geolocation)?' <i class="fa fa-globe"></i>':''!!}{!!($element->loop_source)?' <i class="fa fa-repeat"></i>':''!!}</a>
@endpush
@if($element->node == "self" && $element->Parent)
<input type="hidden" name="parent[{{$element->id}}]" value="{{$element->Parent->id}}">
@include('Template.Forms.componentSetting', ["element" => $element->Parent])
@endif
<input type="hidden" name="category[{{$element->id}}]" value="{{$element->category}}">
<input type="hidden" name="node[{{$element->id}}]" value="{{$element->node}}">
<div id="component_{{$element->node.'_'.$element->id}}" class="collapse mb-2">
    <div class="card">
        <div class="card-header">
            <h4 class="setting_toggle" data-toggle="component_{{$element->node.'_'.$element->id}}"><i class="fa fa-cube"></i> {{$element->node.'_'.$element->id}}</h4>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-globe"></i></span>
                </div>
                <input type="text" class="form-control" placeholder="Country1,Country2" tabindex="1" name="geolocation[{{$element->id}}]" value="{{ old("geolocation.".$element->id, $element->geolocation) }}">
                <div class="input-group-prepend ml-1">
                    <span class="input-group-text"><i class="fa fa-eye-slash"></i></span>
                </div>
                <input type="number" class="form-control" placeholder="Visibility Condition" tabindex="1" name="visibility_id[{{$element->id}}]" value="{{ old("visibility_id.".$element->id, $element->visibility_id) }}">
                <div class="input-group-append">
                    <span class="input-group-text">{{(old("visibility_id.".$element->id, $element->visibility_id) == "0")?'Visible':'Partial'}}</span>
                </div>
                <div class="input-group-prepend ml-1">
                    <span class="input-group-text"><i class="fa fa-eye"></i></span>
                </div>
                <select class="custom-select" tabindex="1" name="visibility[{{$element->id}}]">
                    <option value="show"{{(old("visibility.".$element->id, $element->visibility) == "show")?' selected':''}}>Show</option>
                    <option value="auth"{{(old("visibility.".$element->id, $element->visibility) == "auth")?' selected':''}}>Auth</option>
                    <option value="guest"{{(old("visibility.".$element->id, $element->visibility) == "guest")?' selected':''}}>Guest</option>
                    <option value="none"{{(old("visibility.".$element->id, $element->visibility) == "none")?' selected':''}}>None</option>
                </select>
                <select class="custom-select component_type d-none" tabindex="1" name="type[{{$element->id}}]">
                    <option value="body"{{(old("type.".$element->id, $element->type) == "body")?' selected':''}}>Body</option>
                    <option value="main"{{(old("type.".$element->id, $element->type) == "main")?' selected':''}}>Main</option>
                    <option value="header"{{(old("type.".$element->id, $element->type) == "header")?' selected':''}}>Header</option>
                    <option value="footer"{{(old("type.".$element->id, $element->type) == "footer")?' selected':''}}>Footer</option>
                </select>
                @if($element->node == "child")
                <div class="input-group-prepend ml-1">
                    <span class="input-group-text"><i class="fa fa-sort-numeric-asc"></i></span>
                </div>
                <select class="custom-select child_order" data-value="{{$element->child_order}}" data-group="{{$element->name}}" tabindex="1" name="child_order[{{$element->id}}]">
                    @foreach($element->Children as $child_)
                    <option {{(old("child_order.".$element->id, $element->child_order) == $child_->child_order)?' selected':''}}>{{$child_->child_order}}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="child_order[{{$element->id}}]" value="1">
                @endif
                @if($element->node != "parent")
                <div class="input-group-prepend ml-1">
                    <span class="input-group-text"><a href="{{route('Loopsource')}}" target="_blank"><i class="fa fa-repeat"></i></a></span>
                </div>
                <select class="custom-select" tabindex="1" name="loop_source[{{$element->id}}]">
                    <option value="">None</option>
                    @foreach(App\Models\LoopSource::all() as $ls)
                    <option value="{{$ls->id}}"{{(old("loop_source.".$element->id, $element->loop_source) == $ls->id)?' selected':''}}>{{$ls->title}}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="loop_source[{{$element->id}}]" value="">
                @endif
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Attribute</span>
                </div>
                <input type="text" class="form-control" placeholder='eg. {"attr1":"value1", "attr2":"value2"}' tabindex="1" name="attribute[{{$element->id}}]" value="{{ old("attribute.".$element->id, $element->attributes) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Classes</span>
                </div>
                <input type="text" class="form-control" placeholder='eg. ["class1", "class2"]' tabindex="1" name="classes[{{$element->id}}]" value="{{ old("classes.".$element->id, $element->classes) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Style</span>
                </div>
                <input type="text" class="form-control" placeholder='eg. {"selector":"", "style":{"width":"100%"}}' tabindex="1" name="style[{{$element->id}}]" value="{{ old("style.".$element->id, $element->style) }}">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">HTML Tag</span>
                </div>
                <input type="text" class="form-control" placeholder='eg. <div>' tabindex="1" name="start_tag[{{$element->id}}]" value="{{ old("start_tag.".$element->id, $element->start_tag) }}">
                <input type="text" class="form-control" placeholder='eg. </div>' tabindex="1" name="end_tag[{{$element->id}}]" value="{{ old("end_tag.".$element->id, $element->end_tag) }}">
            </div>
            @if($element->content_type == "element")
                <input type="hidden" name="content_type[{{$element->id}}]" value="{{$element->content_type}}">
                <input type="hidden" name="content[{{$element->id}}]" value="{{$element->content}}">
            @else
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Content</span>
                </div>
                <input type="text" class="form-control" placeholder='Content' tabindex="1" name="content[{{$element->id}}]" value="{{ old("content.".$element->id, $element->content) }}">
                <select class="custom-select" tabindex="1" name="content_type[{{$element->id}}]">
                    <option value="static"{{(old("content_type.".$element->id, $element->content_type) == "static")?' selected':''}}>Static</option>
                    <option value="variable"{{(old("content_type.".$element->id, $element->content_type) == "variable")?' selected':''}}>Variable</option>
                </select>
            </div>
            @endif
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Script</span>
                </div>
                <input type="text" class="form-control" placeholder='JavaScript' tabindex="1" name="script[{{$element->id}}]" value="{{ old("script.".$element->id, $element->script) }}">
            </div>
        </div>
    </div>
</div>
@if($element->node == "self" && count($element->Children))
@foreach($element->Children as $child)
<input type="hidden" name="children[{{$element->id}}][]" value="{{$child->id}}">
@include('Template.Forms.componentSetting', ["element" => $child])
@endforeach
@endif
@if($element->nested_component)
<input type="hidden" name="nested_component[{{$element->id}}]" value="{{$element->nestedComponent->id}}">
@include('Template.Forms.componentSetting', ["element" => $element->nestedComponent])
@endif