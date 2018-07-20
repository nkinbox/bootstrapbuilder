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
    if(isset($loops)) {
        $element_var_attributes = preg_replace_callback('/@@database\.(.*?)@@/', function($match_) use ($propertyResolver, $loops) {
            $database_variables = explode(".", $match_[1]);
            return $propertyResolver($database_variables, $loops);
        }, json_encode($element->var_attributes));
        try {
            eval("\$element_var_attributes=".$element_var_attributes.";");
        } catch (ParseError $e) {}
        $var_attributes = json_decode($element_var_attributes, true);
    } else {
        $var_attributes = json_decode($element->var_attributes, true);
    }
    foreach($var_attributes as $key => $val) {
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
{{-- Content START --}}
@if($element->content_type == "element")
    @if($element->node == "self" && count($element->Children))
        @foreach($element->Children as $child)
        @include('Website.element', ["element" => $child])
        @endforeach
    @endif
    @if($element->nested_component)
        @include('Website.element', ["element" => $element->nestedComponent])
    @endif
@else
<?php
$this_content = "";
if(isset($content[$element->id.'_'.$id])) {
    $this_content = $content[$element->id.'_'.$id];
} elseif($element->content) {
    $this_content = $element->content;
} else {
    echo '__content__';
}
if($this_content) {
    if(isset($loops)) {
        $this_content = preg_replace_callback('/@@database\.(.*?)@@/', function($match_) use ($propertyResolver, $loops) {
            $database_variables = explode(".", $match_[1]);
            return $propertyResolver($database_variables, $loops);
        }, json_encode($this_content));
        try {
            eval("\$this_content=".$this_content.";");
        } catch (ParseError $e) {}
        echo $this_content;
    } else {
        echo $this_content;
    }
}
?>
@endif
{{-- Content END --}}
{!! $element->end_tag !!}
{{-- HTML END --}}