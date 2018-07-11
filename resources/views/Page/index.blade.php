@extends('Page.layout')
@php
$globalScript = $page->Template->getScript;
$globalStyle = $page->Template->getCSS;
$pageScript = $page->getScript;
$pageStyle = $page->getCSS;
@endphp
@foreach($page->Components as $component)
@if($component->type == "header")
@push('header')
@include('Page.element', ["element" => $component])
@endpush
@elseif($component->type == "body")
@push('body')
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