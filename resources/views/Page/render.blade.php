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
@if($element->node == "self" && $element->Parent)
@if($element->Parent->style != '{"selector":"","style":[]}')
<?php
$style = json_decode($element->Parent->style, true);
?>
@push('styles')
#component_{{$element->id}}<?php if($style['selector']) echo ' '.$style['selector']; ?> {<?php
foreach($style['style'] as $prop => $val) {
echo $prop.":".$val.";";
}
?>}
@endpush
@endif
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
$classes = json_decode($element->Parent->classes, true);
if(count($classes)) {
    echo ' class="'.implode(" ", $classes).'"';
}
?>>
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
    echo ' class="'.implode(" ", $classes).'"';
}
?>>
@if($element->content_type == "element")
@if($element->node == "self" && count($element->Children))
@foreach($element->Children as $child)
@include('Page.render', ["element" => $child])
@endforeach
@endif
@if($element->nested_component)
@include('Page.render', ["element" => $element->nestedComponent])
@endif
@else
{!!($element->content)?$element->content:'__content__'!!}
@endif
{!! $element->end_tag !!}
{!! $element->Parent->end_tag !!}
@else
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
    echo ' class="'.implode(" ", $classes).'"';
}
?>>
@if($element->content_type == "element")
@if($element->node == "self" && count($element->Children))
@foreach($element->Children as $child)
@include('Page.render', ["element" => $child])
@endforeach
@endif
@if($element->nested_component)
@include('Page.render', ["element" => $element->nestedComponent])
@endif
@else
{!!($element->content)?$element->content:'__content__'!!}
@endif
{!! $element->end_tag !!}
@endif