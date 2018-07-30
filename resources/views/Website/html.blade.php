{{-- HTML START --}}
<?php
    echo substr($element->start_tag, 0, -1);
    echo ' id="component_' .$element->id. '"';
    $attributes = preg_replace_callback('/@@database\.(.*?)@@/', function($match_) use ($propertyResolver, $loops) {
        return $propertyResolver($match_[1], $loops);
    }, json_encode($element->attributes));
    $attributes = preg_replace_callback('/@@url\.(.*?)@@/', function($match_) use ($propertyResolver, $loops) {
        $weburl = \App\Models\WebUrl::find($match_[1]);
        $url = $weburl->url;
        $builder = json_decode($weburl->url_builder, true);
        foreach($builder as $key => $val) {
            if(preg_match('/@@database\.(.*?)@@/', $val)) {
                $val = $propertyResolver($val, $loops);
            }
            $url = str_replace($key, $val, $url);
        }
        return strtolower(str_replace(" ", "-", $url));
    }, $attributes);
    try {
        eval("\$attributes=".$attributes.";");
    } catch (ParseError $e) {}
    $attributes = json_decode($attributes, true);
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
    $this_content = preg_replace_callback('/@@database\.(.*?)@@/', function($match_) use ($propertyResolver, $loops) {
        return $propertyResolver($match_[1], $loops, isset($_GET['debug']));
    }, json_encode($this_content));
    $this_content = preg_replace_callback('/@@url\.(.*?)@@/', function($match_) use ($propertyResolver, $loops) {
        $weburl = \App\Models\WebUrl::find($match_[1]);
        $url = $weburl->url;
        $builder = json_decode($weburl->url_builder, true);
        foreach($builder as $key => $val) {
            $m = [];
            if(preg_match('/@@database\.(.*?)@@/', $val, $m)) {
                $val = $propertyResolver($m[1], $loops);
            }
            dd($val);
            $url = str_replace($key, $val, $url);
        }
        return strtolower(str_replace(" ", "-", $url));
    }, $this_content);
    try {
        eval("\$this_content=".$this_content.";");
    } catch (ParseError $e) {}
    echo $this_content;
}
?>
@endif
{{-- Content END --}}
{!! $element->end_tag !!}
{{-- HTML END --}}