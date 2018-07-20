<?php //visibility
    $render = false;
    if($element->visibility != "none") {
        if($element->visibility != "show") {
            $mode = "guest";
            if(Auth::check())
            $mode = "auth";
            if($element->visibility == $mode)
            $render = true;
            else
            $render = false;
        }
        else $render = true;
    } else $render = false;
    if($element->node == "self" && $element->Parent) {
        if($element->Parent->visibility != "none") {
            if($element->Parent->visibility != "show") {
                if($element->Parent->visibility == $mode)
                $render = true;
                else
                $render = false;
            }
            else $render = true;
        } else $render = false;
    }
?>
@if($render)
@include('Website.style_script')
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
    $classes = json_decode($element->classes, true);
    if(count($classes)) {
        echo ' class="'.implode(" ", $classes).'"';
    }
?>>
@endif
{{-- Parent Header END --}}
@if($element->loop_source)
<?php
    $execute = true;
    $temp = explode("|", $element->loop_source);
    $query = (isset($temp[1]) && $temp[1])?$temp[1]:'';
    $database_variables = explode(".", $temp[0]);
    $loop = false;
    $loops;
    $loopResolver($database_variables, [$query,""], $loop_count, $loops);
    try {
        $loopThrough = eval("return " .$loops[$loop_count]['model_var']. " ;");
    } catch (ParseError $e) {
        $loopThrough = [];
        $execute = false;
    }
?>
@if($execute)
    @if($loops[$loop_count]['isArray'])
    @if($loopThrough instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{$loopThrough->links()}}
    @endif
        @foreach($loopThrough as ${"key".$loop_count} => ${"value".$loop_count})
        @include('Website.html')
        @endforeach
    @else
        @include('Website.html')
    @endif
@else
<h1 class="text-danger">ERROR: Name: {{$element->name}} ID: {{$element->id}} Node: {{$element->node}}</h1>
@endif
@else
    @include('Website.html')
@endif
{{-- Parent Footer START --}}
@if($element->node == "self" && $element->Parent)
{!! $element->Parent->end_tag !!}
@endif
{{-- Parent Footer START --}}
@endif