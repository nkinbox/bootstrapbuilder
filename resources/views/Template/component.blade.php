@extends('Template.layout')
@section('card')
<div class="card">
    <div class="card-body">
        <a class="nav-link pull-right" href="{{ route('Template.Component', ['template_id' => $template->id, 'operation' => 'add']) }}"><i class="fa fa-plus-circle"></i> Add Component</a>
        <a class="nav-link pull-right" href="{{ route('Template.Component.view', ['template_id' => $template->id]) }}" target="_blank"><i class="fa fa-edit"></i> Content</a>
        <h2 class="card-title">{{$template->title}} | Components</h2>
        <div class="card-text">
            <table class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Type</th>
                    <th scope="col">Name</th>
                    <th scope="col">GeoBased</th>
                    <th scope="col">Visibility</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($template->Components as $component)
                    <?php
                        $geolocation = false;
                        $checkGeolocation($component, $geolocation);
                    ?>
                        <tr>
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>{{$component->type}}</td>
                            <td>{{$component->name}}</td>
                            <td{!! ($geolocation)?' class="bg-success text-light"':'' !!}>{{($geolocation)?'yes':''}}</td>
                            <td{!! ($component->visibility == "none")?' class="bg-danger text-light"':'' !!}>{{ucwords($component->visibility)}}</td>
                            <td>
                                <a href="{{ route('Component.Edit', ['name' => $component->name]) }}" target="_blank" title="Component Editor"><i class="fa fa-cubes"></i></a>
                                /
                                <a href="{{ route('Template.Component', ['template_id' => $template->id, 'operation' => 'edit', 'id' => $component->id]) }}" title="Code Edit"><i class="fa fa-edit"></i></a>
                                /
                                <a href="{{ route('Component.Delete', ['name' => $component->name]) }}" title="Delete"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
<title>{{$template->title}} | Components</title>
@endpush