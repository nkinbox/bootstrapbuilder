@extends('Page.layout')
@php
$globalScript = $page->Template->getScript;
$globalStyle = $page->Template->getCSS;
$pageScript = $page->getScript;
$pageStyle = $page->getCSS;
$up = true;
@endphp
@foreach($page->Components as $component)
@php
$loops = [];
$loop_count = -1;
$id = $component->pivot->id;
if(isset($content_id) && $content_id) {
    $content = [];
    $content_ = App\Models\Content::find($content_id);
    if($content_) {
        $matches = [];
        preg_match_all('/@@start\.(.*?)@@(.*?)@@end\.(.*?)@@/s' , $content_->content, $matches);
        foreach($matches[1] as $key => $m) {
            if($matches[3][$key] == $m) {
                $content[$m] = trim($matches[2][$key]);
            }
        }
    }
}
@endphp
@if($component->type == "header")
@push('header')
@include('Page.element', ["element" => $component])
@endpush
@elseif($component->type == "body")
@if($up)
@push('bodyup')
@include('Page.element', ["element" => $component])
@endpush
@else
@push('bodydown')
@include('Page.element', ["element" => $component])
@endpush
@endif
@elseif($component->type == "main")
@php $up = false; @endphp
@push('main')
@include('Page.element', ["element" => $component])
@endpush
@elseif($component->type == "footer")
@push('footer')
@include('Page.element', ["element" => $component])
@endpush
@endif
@endforeach
@if($globalScript)
@push('scripts')
{!!$globalScript->content!!}
@endpush
@endif
@if($globalStyle)
@push('styles')
{!!$globalStyle->content!!}
@endpush
@endif
@if($pageScript)
@push('scripts')
{!!$pageScript->content!!}
@endpush
@endif
@if($pageStyle)
@push('styles')
{!!$pageStyle->content!!}
@endpush
@endif
@push('metadata')
<?php
$metadata = "";
if($page->meta_id) {
    $metaContent = preg_replace_callback('/@@all(.*?)@@endall/', function($match_) use (&$metadata) {
        $metadata = $match_[1];
        return "";
    }, $page->getMetadata->content);
    echo $metaContent.$metadata;
}
?>
@endpush
@push('scripts')
$("a").click(function(e){
    e.preventDefault();
});
@endpush