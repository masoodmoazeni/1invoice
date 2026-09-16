@extends('layout.master')
@section('breadcrumb')
{{--    <x-breadcrumb :items="$breadcrumbs" />--}}
@endsection
@section('content')
    branch
{{--    <div class="container-fluid">--}}
{{--        <x-data-table--}}
{{--            :headers='[--}}
{{--                ["key" => "name", "label" => "name"],--}}
{{--                ["key" => "iso2", "label" => "iso2"],--}}
{{--                ["key" => "iso3", "label" => "iso3"],--}}
{{--                ["key" => "numeric_code", "label" => "numeric_code"],--}}
{{--                ["key" => "phone_code", "label" => "phone_code"],--}}
{{--                ["key" => "capital", "label" => "capital"],--}}
{{--                ["key" => "created_at", "label" => "created_at"],--}}
{{--            ]'--}}
{{--            :rows="$country"--}}
{{--            :paginations="$country"--}}
{{--            id="item-table"--}}
{{--            title="list country"--}}
{{--            title-create="create_item"--}}
{{--            resource-name="country"--}}
{{--            :actions="[--}}
{{--                [--}}
{{--//                    'permission' => 'item_show',--}}
{{--                    'route' => 'country.show',--}}
{{--                    'label' => 'show',--}}
{{--                    'class' => 'menu-link px-3',--}}
{{--                    'id' => 'btn-show',--}}
{{--                    'type' => 'link',--}}
{{--                    'icon_class' => 'text-light-blue',--}}
{{--                    'icon' => ' icon-331',--}}
{{--                ],--}}
{{--                [--}}
{{--//                    'permission' => 'item_edit',--}}
{{--                    'route' => 'country.edit',--}}
{{--                    'label' => 'edit',--}}
{{--                    'class' => 'menu-link px-3',--}}
{{--                    'id' => 'btn-show',--}}
{{--                    'type' => 'link',--}}
{{--                   'icon_class' => ' text-light-green',--}}
{{--                    'icon' => 'icon-Edit3',--}}
{{--                ],--}}
{{--                [--}}
{{--//                    'permission' => 'item_delete',--}}
{{--                    'route' => 'country.destroy',--}}
{{--                    'label' => 'delete',--}}
{{--                    'class' => 'menu-link px-3',--}}
{{--                    'id' => 'btn-delete',--}}
{{--                    'type' => 'delete',--}}
{{--                    'delete' => true,--}}
{{--                    'icon_class' => 'text-red',--}}
{{--                    'icon' => 'icon-Close3',--}}
{{--                ]--}}
{{--            ]"--}}
{{--        />--}}
{{--    </div>--}}
@endsection

@section('script')

@endsection
