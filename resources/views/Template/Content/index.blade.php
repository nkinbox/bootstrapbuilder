@extends('Template.Content.layout')
@push('title')
<title>{{$template->title}} | Component Content</title>
@endpush
@php
$globalStyle = $template->getCSS;
@endphp
@foreach($template->Components as $component)
@push('body')
<h3 class="text-center">{{$component->name}}</h3>
<div class="border p-2 mx-2 mt-2 mb-4" style="box-shadow: 0px 0px 10px 0px #000">
@include('Template.Content.element', ["element" => $component])
</div>
<hr>
@endpush
@endforeach
@push('scripts')
window.addEventListener('click', function (evt) {
    if (evt.detail === 3) {
        if(confirm('Click OK to save changes'))
        collectdata();
    }
});
var content = {"template_id":{{$template->id}}, "_token":"{{csrf_token()}}", "content":{}};
function collectdata() {
    content['content'] = {};
    $(".component").each(function(){
        content['content'][this.id.split("_")[1]] = $(this).html().trim();
    });
    $.ajax({
        type: "POST",
        url: '{{route('Template.Component.content')}}',
        data: JSON.stringify(content),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data){
            if(data.success) {
                alert("Updated Successfully!");
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
$("a").click(function(e){
    e.preventDefault();
});
@endpush
@if($globalStyle)
@push('styles')
{!!$globalStyle->content!!}
@endpush
@endif