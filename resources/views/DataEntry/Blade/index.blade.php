@extends('DataEntry.Blade.layout')
@php
$globalScript = $page->Template->getScript;
$globalStyle = $page->Template->getCSS;
$pageScript = $page->getScript;
$pageStyle = $page->getCSS;
@endphp
@push('title')
<title>{{$page->Template->title}} | {{$page->title}} | {{$mode}}</title>
@endpush
@php $up = true; @endphp
@foreach($page->Components as $component)
@php $order = $component->pivot->order; @endphp
@if($component->type == "header")
@push('header')
@include('DataEntry.Blade.element', ["element" => $component])
@endpush
@elseif($component->type == "body")
@if($up)
@push('bodyup')
@include('DataEntry.Blade.element', ["element" => $component])
@endpush
@else
@push('bodydown')
@include('DataEntry.Blade.element', ["element" => $component])
@endpush
@endif
@elseif($component->type == "main")
@php $up = false; @endphp
@push('main')
@include('DataEntry.Blade.element', ["element" => $component])
@endpush
@elseif($component->type == "footer")
@push('footer')
@include('DataEntry.Blade.element', ["element" => $component])
@endpush
@endif
@endforeach
@push('scripts')
window.addEventListener('click', function (evt) {
    if (evt.detail === 3) {
        if(confirm('Click OK to save changes'))
        collectdata();
    }
});
var content = {"content_id":{{$page->id}}, "_token":"{{csrf_token()}}", "content":{}};
function collectdata() {
    content['content'] = {};
    $(".component").each(function(){
        content['content'][this.id.split("_")[1]+'_'+$(this).attr("data-order")] = $(this).text().trim();
    });
    if(!error) {
        $.ajax({
            type: "POST",
            url: '{{route('DataEntry.Page')}}',
            data: JSON.stringify(content),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data){
                console.log(data);
                if(data.success) {
                    location.reload();
                } else {
                    alert('An error occured');
                }
            },
            failure: function(errMsg) {
                console.log(errMsg);
            }
        });
    }
}
@endpush
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