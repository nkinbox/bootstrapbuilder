@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Template.Page.Component', ['page_id' => $page->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add Component</a>
        <h2 class="card-title">{{$page->Template->title}} | {{$page->title}}</h2>
        <div class="card-text">
        <form action="{{ route('Template.Page.Component.order') }}" method="post">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="page_id" value="{{$page->id}}">
                @csrf
            <table class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col" style="width: 30px"><button type="submit" class="btn btn-warning">Order</button></th>
                    <th scope="col">Type</th>
                    <th scope="col">Name</th>
                    <th scope="col">GeoBased</th>
                    <th scope="col">Visibility</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page->Components as $component)
                    <?php
                        $geolocation = false;
                        $checkGeolocation($component, $geolocation);
                    ?>
                        <tr>
                            <th scope="row">
                                        <select class="component_order" data-value="{{$component->order}}" tabindex="1" name="order[{{$component->id}}]">
                                            @foreach($page->Components as $order)
                                            <option {{(old("order[".$component->id."]", $component->order) == $order->order)?' selected':''}}>{{$order->order}}</option>
                                            @endforeach
                                        </select>
                            </th>
                            <td>{{$component->type}}</td>
                            <td>{{$component->name}}</td>
                            <td{!! ($geolocation)?' class="bg-success text-light"':'' !!}>{{($geolocation)?'yes':''}}</td>
                            <td{!! ($component->visibility == "none")?' class="bg-danger text-light"':'' !!}>{{ucwords($component->visibility)}}</td>
                            <td>
                                <a href="{{ route('Template.Page.Component', ['page_id' => $page->id, 'operation' => 'edit', 'id' => $component->id]) }}"><i class="fa fa-edit"></i> Edit</a>
                                /
                                <a href="{{ route('Template.Page.Component.delete', ['id' => $component->id]) }}"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(".component_order").change(function(){
        var prev = $(this).attr('data-value');
        var val = $(this).val();
        $('.component_order').each(function(){
            if($(this).attr('data-value') == val) {
                $(this).attr('data-value', prev);
                $(this).val(prev);
            }
        });
        $(this).attr('data-value', val);
    });
</script>
@endpush
@push('styles')
<style>
    .component_order {
        border: 0;
    }
</style>
@endpush
@push('title')
<title>{{$page->Template->title}} | {{$page->title}}</title>
@endpush