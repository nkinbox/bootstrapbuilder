@if($element->node == "self" && $element->Parent)
<?php
echo substr($element->Parent->start_tag, 0, -1);
echo ' id="parent_' .$element->Parent->id. '"';
$attributes = json_decode($element->Parent->attributes, true);
foreach($attributes as $key => $val) {
    if($val)
    echo ' '.$key.'="'.$val.'"';
    else
    echo ' '.$key;
}
// $var_attributes = json_decode($element->Parent->var_attributes, true);
// foreach($var_attributes as $key => $val) {
//     if($val)
//     echo ' '.$key.'="'.$val.'"';
//     else
//     echo ' '.$key;
// }
$classes = json_decode($element->Parent->classes, true);
if(count($classes)) {
    echo ' class="'.implode(" ", $classes).'"';
}
?>>
<?php
echo substr($element->start_tag, 0, -1);
echo ' id="' .$element->node. '_' .$element->id. '"';
$attributes = json_decode($element->attributes, true);
foreach($attributes as $key => $val) {
    if($val)
    echo ' '.$key.'="'.$val.'"';
    else
    echo ' '.$key;
}
// $var_attributes = json_decode($element->var_attributes, true);
// foreach($var_attributes as $key => $val) {
//     if($val)
//     echo ' '.$key.'="'.$val.'"';
//     else
//     echo ' '.$key;
// }
$classes = json_decode($element->classes, true);
if(count($classes)) {
    echo ' class="'.implode(" ", $classes).'"';
}
?>>
@if($element->content_type == "element")
@if($element->node == "self" && count($element->Children))
@foreach($element->Children as $child)
@include('Component.partialRender', ["element" => $child])
@endforeach
@endif
@if($element->nested_component)
@include('Component.partialRender', ["element" => $element->nestedComponent])
@endif
@else
{!!(strlen($element->content) > 50)? substr($element->content,0,50)."..." : $element->content;!!}
@endif
{!! $element->end_tag !!}
{!! $element->Parent->end_tag !!}
@else
<?php
echo substr($element->start_tag, 0, -1);
echo ' id="' .$element->node. '_' .$element->id. '"';
$attributes = json_decode($element->attributes, true);
foreach($attributes as $key => $val) {
    if($val)
    echo ' '.$key.'="'.$val.'"';
    else
    echo ' '.$key;
}
// $var_attributes = json_decode($element->var_attributes, true);
// foreach($var_attributes as $key => $val) {
//     if($val)
//     echo ' '.$key.'="'.$val.'"';
//     else
//     echo ' '.$key;
// }
$classes = json_decode($element->classes, true);
if(count($classes)) {
    echo ' class="'.implode(" ", $classes).'"';
}
?>>
@if($element->content_type == "element")
@if($element->node == "self" && count($element->Children))
@foreach($element->Children as $child)
@include('Component.partialRender', ["element" => $child])
@endforeach
@endif
@if($element->nested_component)
@include('Component.partialRender', ["element" => $element->nestedComponent])
@endif
@else
{!!(strlen($element->content) > 50)? substr($element->content,0,50)."..." : $element->content;!!}
@endif
{!! $element->end_tag !!}
@endif