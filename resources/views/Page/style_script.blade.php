{{-- STYLE PUSH START --}}
@if($element->style != '{"selector":"","style":[]}')
    <?php
    $style = json_decode($element->style, true);
    ?>
    @if(!isset($style['selector']) && is_array($style[0]))
    @push('styles')
    @foreach($style as $style_)
    #component_{{$element->id}}<?php if($style_['selector']) echo ' '.$style_['selector']; ?> {<?php
    foreach($style_['style'] as $prop => $val) {
    echo $prop.":".$val.";";
    }
    ?>}
    @endforeach
    @endpush
    @else
    @push('styles')
    #component_{{$element->id}}<?php if($style['selector']) echo ' '.$style['selector']; ?> {<?php
    foreach($style['style'] as $prop => $val) {
    echo $prop.":".$val.";";
    }
    ?>}
    @endpush
    @endif
@endif
@if($element->node == "self" && $element->Parent && $element->Parent->style != '{"selector":"","style":[]}')
    <?php
    $style = json_decode($element->Parent->style, true);
    ?>
    @if(!isset($style['selector']) && is_array($style[0]))
    @push('styles')
    @foreach($style as $style_)
    #component_{{$element->id}}<?php if($style_['selector']) echo ' '.$style_['selector']; ?> {<?php
    foreach($style_['style'] as $prop => $val) {
    echo $prop.":".$val.";";
    }
    ?>}
    @endforeach
    @endpush
    @else
    @push('styles')
    #component_{{$element->id}}<?php if($style['selector']) echo ' '.$style['selector']; ?> {<?php
    foreach($style['style'] as $prop => $val) {
    echo $prop.":".$val.";";
    }
    ?>}
    @endpush
    @endif
@endif
{{-- STYLE PUSH END --}}
{{-- SCRIPT PUSH START --}}
@if($element->script)
@push('scripts')
{!!$element->script!!}
@endpush
@endif
@if($element->node == "self" && $element->Parent && $element->Parent->script)
@push('scripts')
{!!$element->Parent->script!!}
@endpush
@endif
{{-- SCRIPT PUSH END --}}