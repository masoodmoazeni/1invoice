@extends('layout.master')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1>{{ $title }}</h1>
        <a href="{{ route($resource . '.index') }}" class="btn btn-light">بازگشت</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ $action }}" method="POST">
                @csrf
                @if($method !== 'POST') @method($method) @endif
                <div class="row">
                    @foreach($fields as $field)
                        <div class="col-md-{{ $field['width'] ?? 6 }} mb-4">
                            <label for="{{ $field['name'] }}" class="form-label">
                                {{ $field['label'] }} @if($field['required'] ?? false)<span class="text-danger">*</span>@endif
                            </label>
                            @if(($field['type'] ?? 'text') === 'select')
                                <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-select" @if($field['required'] ?? false) required @endif>
                                    <option value="">انتخاب کنید</option>
                                    @foreach($field['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) old($field['name'], data_get($record, $field['name'])) === (string) $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif(($field['type'] ?? 'text') === 'checkbox')
                                <div class="form-check form-switch mt-2">
                                    <input type="hidden" name="{{ $field['name'] }}" value="0">
                                    <input type="checkbox" id="{{ $field['name'] }}" name="{{ $field['name'] }}" value="1" class="form-check-input" @checked(old($field['name'], data_get($record, $field['name'], $field['default'] ?? false)))>
                                    <label class="form-check-label" for="{{ $field['name'] }}">فعال</label>
                                </div>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" id="{{ $field['name'] }}" name="{{ $field['name'] }}" value="{{ old($field['name'], data_get($record, $field['name'])) }}" class="form-control" @if($field['required'] ?? false) required @endif @if(isset($field['step'])) step="{{ $field['step'] }}" @endif>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">ذخیره</button>
                <a href="{{ route($resource . '.index') }}" class="btn btn-light">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
