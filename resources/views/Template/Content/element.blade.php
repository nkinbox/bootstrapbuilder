{{-- STYLE PUSH START --}}
@if($element->style != '{"selector":"","style":[]}')
    <?php
    $style = json_decode($element->style, true);
    ?>
    @push('styles')
    #component_{{$element->id}}<?php if($style['selector']) echo ' '.$style['selector']; ?> {<?php
    foreach($style['style'] as $prop => $val) {
    echo $prop.":".$val.";";
    }
    ?>}
    @endpush
@endif
@if($element->node == "self" && $element->Parent && $element->Parent->style != '{"selector":"","style":[]}')
    <?php
    $style = json_decode($element->Parent->style, true);
    ?>
    @push('styles')
    #component_{{$element->Parent->id}}<?php if($style['selector']) echo ' '.$style['selector']; ?> {<?php
    foreach($style['style'] as $prop => $val) {
    echo $prop.":".$val.";";
    }
    ?>}
    @endpush
@endif
{{-- STYLE PUSH END --}}
{{-- Parent Header START --}}
@if($element->node == "self" && $element->Parent)
<?php
    echo substr($element->Parent->start_tag, 0, -1);
    echo ' id="component_' .$element->Parent->id. '"';
    $attributes = json_decode($element->Parent->attributes, true);
    foreach($attributes as $key => $val) {
        if($val)
        echo ' '.$key.'="'.$val.'"';
        else
        echo ' '.$key;
    }
    $var_attributes = json_decode($element->Parent->var_attributes, true);
    foreach($var_attributes as $attribute)
    echo ' '.$attribute.'=""';
    $classes = json_decode($element->classes, true);
    if(count($classes)) {
        echo ' class="'.implode(" ", $classes).'"';
    }
?>>
@endif
{{-- Parent Header END --}}

{{-- HTML START --}}
<?php
    echo substr($element->start_tag, 0, -1);
    echo ' id="component_' .$element->id. '"';
    $attributes = json_decode($element->attributes, true);
    foreach($attributes as $key => $val) {
        if($val)
        echo ' '.$key.'="'.$val.'"';
        else
        echo ' '.$key;
    }
    $var_attributes = json_decode($element->var_attributes, true);
    foreach($var_attributes as $attribute)
    echo ' '.$attribute.'=""';
    $classes = json_decode($element->classes, true);
    if(count($classes)) {
        echo ' class="'.(($element->content_type != "element")?' component':'').implode(" ", $classes).'"';
    } elseif($element->content_type != "element") {
        echo ' class="component"';
    }
    if($element->content_type != "element")
    echo ' contenteditable="true"';
?>>
{{-- Content START --}}
@if($element->content_type == "element")
    @if($element->node == "self" && count($element->Children))
        @foreach($element->Children as $child)
        @include('Template.Content.element', ["element" => $child])
        @endforeach
    @endif
    @if($element->nested_component)
        @include('Template.Content.element', ["element" => $element->nestedComponent])
    @endif
@else
{!!($element->content)?$element->content:'__content__'!!}
@endif
{{-- Content END --}}
{!! $element->end_tag !!}
{{-- HTML END --}}
{{-- Parent Footer START --}}
@if($element->node == "self" && $element->Parent)
{!! $element->Parent->end_tag !!}
@endif
{{-- Parent Footer START --}}