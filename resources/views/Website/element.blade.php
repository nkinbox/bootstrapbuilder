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
    $classes = json_decode($element->Parent->classes, true);
    if(count($classes)) {
        echo ' class="'.implode(" ", $classes).'"';
    }
?>>
@endif
{{-- Parent Header END --}}
@if($element->loop_source && $ls = \App\Models\LoopSource::find($element->loop_source))
<?php
    $database_variables = explode(".", $ls->database_variables);
    $loop_count++;
    $loops[$loop_count] = [
        "loaded" => [],
        "related" => [],
        "isArray" => false,
        "model_var" => ""
    ];
    $error = false;
    $error_message = "";
    $eval = "";
    foreach($database_variables as $variable) {
        if($error)
        break;
        if($variable && $db_var = \App\Models\DatabaseVariable::find($variable)) {
            $isRelation = false;
            if($ls->relation) {
                for($i = (count($loops) - 1); $i >= 0; $i--) {
                    if(isset($loops[$i]['related'][$db_var->object])) {
                        $isRelation = true;
                        if($loops[$i]['isArray'])
                        $eval = "\$value".$i;
                        else
                        $eval = $loops[$i]['model_var'];
                        break;
                    }
                }
            } else {
                for($i = 0; $i < count($loops); $i++) {
                    if(isset($loops[$i]['related'][$db_var->object])) {
                        $isRelation = true;
                        if($loops[$i]['isArray'])
                        $eval = "\$value".$i;
                        else
                        $eval = $loops[$i]['model_var'];
                        break;
                    }
                }
            }
            if(!$isRelation) {
                $isSet = false;
                if(!$ls->object_query) {
                    if($ls->relation) {
                        for($i = (count($loops) - 1); $i >= 0; $i--) {
                            if(isset($loops[$i]['loaded'][$db_var->object])) {
                                $isSet = true;
                                if($loops[$i]['isArray'])
                                $eval = "\$value".$i;
                                else
                                $eval = $loops[$i]['model_var'];
                                break;
                            }
                        }
                    } else {
                        for($i = 0; $i < count($loops); $i++) {
                            if(isset($loops[$i]['loaded'][$db_var->object])) {
                                $isSet = true;
                                if($loops[$i]['isArray'])
                                $eval = "\$value".$i;
                                else
                                $eval = $loops[$i]['model_var'];
                                break;
                            }
                        }
                    }
                }
                if(!$isSet) {
                    $object_query = json_encode($ls->object_query);
                    $q_var = json_decode($ls->variables, true);
                    if(isset($q_var['url']) && is_array($q_var['url']) && count($q_var['url'])) {
                        $object_query = preg_replace_callback('/@@(.*?)@@/', function($match_) use ($q_var, $url) {
                            if($match_[1]) {
                                if(array_key_exists($match_[1], $q_var['url'])) {
                                    if(array_key_exists($match_[1], $url))
                                    return $url[$match_[1]];
                                    else
                                    return $q_var['url'][$match_[1]];
                                } else
                                return $match_[0];
                            }
                            return "";
                        }, $object_query);
                    }
                    if(isset($q_var['loop']) && is_array($q_var['loop']) && count($q_var['loop'])) {
                        $object_query = preg_replace_callback('/@@(.*?)@@/', function($match_) use ($q_var, $loops, $propertyResolver) {
                            if($match_[1]) {
                                if(array_key_exists($match_[1], $q_var['loop'])) {
                                    return $propertyResolver($q_var['loop'][$match_[1]], $loops);
                                } else
                                return "";
                            }
                            return "";
                        }, $object_query);
                    }
                    try {
                        eval("\$query = ".$object_query.";");
                        $loops[$loop_count]['loaded'][$db_var->object] = eval("return \App\Models\\".$db_var->object."::".(($query)?$query:"all()").";");
                    } catch (ParseError $e) {
                        $error_message = "return \App\Models\\".$db_var->object."::".(($query)?$query:"all()").";\n".$e->getMessage();
                        $error = 1;
                    }
                }
                if(!$error) {
                    if($db_var->property) {
                        if(!$isSet) {
                            if(is_iterable($loops[$loop_count]['loaded'][$db_var->object]))
                            $eval = "\$value".$loop_count."->".$db_var->property;
                            else
                            $eval = "\$loops[".$loop_count."]['loaded']['".$db_var->object."']->".$db_var->property;
                        }
                        else
                        $eval .= "->".$db_var->property;
                        if($db_var->related_to) {
                            $relation = App\Models\DatabaseVariable::find($db_var->related_to);
                            if($relation) {
                                $bool = true;
                                for($i = (count($loops) - 1); $i >= 0; $i--) {
                                    if(isset($loops[$i]['related'][$relation->object])) {
                                        $bool = false;
                                        break;
                                    }
                                }
                                if($bool)
                                $loops[$loop_count]['related'][$relation->object] = $db_var->object;
                            }
                        }
                    } else {
                        $eval = "\$loops[".$loop_count."]['loaded']['".$db_var->object."']";
                    }
                }
            } else {
                $eval .= "->".$db_var->property;
                if($db_var->related_to) {
                    $relation = App\Models\DatabaseVariable::find($db_var->related_to);
                    if($relation) {
                        $bool = true;
                        for($i = (count($loops) - 1); $i >= 0; $i--) {
                            if(isset($loops[$i]['related'][$relation->object])) {
                                $bool = false;
                                break;
                            }
                        }
                        if($bool)
                        $loops[$loop_count]['related'][$relation->object] = $db_var->object;
                    }
                }
            }
            if($db_var->is_array)
            $loops[$loop_count]['isArray'] = true;
            elseif(!$db_var->property && isset($loops[$loop_count]['loaded'][$db_var->object]) && is_iterable($loops[$loop_count]['loaded'][$db_var->object]))
            $loops[$loop_count]['isArray'] = true;
            else
            $loops[$loop_count]['isArray'] = false;
        }
    }
    if($eval) {
        $property_query = "";
        if($ls->property_query) {            
            $property_query = json_encode($ls->property_query);
            $q_var = json_decode($ls->variables, true);
            if(isset($q_var['url']) && is_array($q_var['url']) && count($q_var['url'])) {
                $property_query = preg_replace_callback('/@@(.*?)@@/', function($match_) use ($q_var, $url) {
                    if($match_[1]) {
                        if(array_key_exists($match_[1], $q_var['url'])) {
                            if(array_key_exists($match_[1], $url))
                            return $url[$match_[1]];
                            else
                            return $q_var['url'][$match_[1]];
                        } else
                        return $match_[0];
                    }
                    return "";
                }, $property_query);
            }
            if(isset($q_var['loop']) && is_array($q_var['loop']) && count($q_var['loop'])) {
                $property_query = preg_replace_callback('/@@(.*?)@@/', function($match_) use ($q_var, $loops, $propertyResolver) {
                    if($match_[1]) {
                        if(array_key_exists($match_[1], $q_var['loop'])) {
                            return $propertyResolver($q_var['loop'][$match_[1]], $loops);
                        } else
                        return "";
                    }
                    return "";
                }, $property_query);
            }
        }
        $loops[$loop_count]['model_var'] = $eval.(($property_query)?"->".$property_query:"");
        try {
            $loopThrough = eval("return " .$loops[$loop_count]['model_var']. " ;");
        } catch (ParseError $e) {
            $error_message = "return " .$loops[$loop_count]['model_var']. " ;\n".$e->getMessage();
            $loopThrough = [];
            $error = 2;
        }
    } else $error = 3;
?>
<!-- {!!print_r($loops)!!} -->
@if(!$error)
    @if($loops[$loop_count]['isArray'])
    @if($loopThrough instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="pagination_container">{{$loopThrough->links()}}</div>
    @endif
        @foreach($loopThrough as ${"key".$loop_count} => ${"value".$loop_count})
        @include('Website.html')
        @endforeach
    @if($loopThrough instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="pagination_container">{{$loopThrough->links()}}</div>
    @endif
    @else
        @include('Website.html')
    @endif
@else
<div class="d-none">
    <h1 class="text-danger">ERROR: Name: {{$element->name}} ID: {{$element->id}} Node: {{$element->node}} Error ID : {{$error}}</h1>
    <p>{{$error_message}}</p>
</div>
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