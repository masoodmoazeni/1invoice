@extends('layout.master')

@section('content')
<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="mb-0">{{ $title }}</h1>
        <a href="{{ route($resource . '.create') }}" class="btn btn-primary">افزودن</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-row-dashed table-hover align-middle">
                <thead><tr>
                    @foreach($columns as $column)<th>{{ $column['label'] }}</th>@endforeach
                    <th class="text-end">عملیات</th>
                </tr></thead>
                <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach($columns as $column)
                            <td>{{ data_get($row, $column['key'], '-') }}</td>
                        @endforeach
                        <td class="text-end text-nowrap">
                            <a href="{{ route($resource . '.show', $row->id) }}" class="btn btn-sm btn-light-info">نمایش</a>
                            <a href="{{ route($resource . '.edit', $row->id) }}" class="btn btn-sm btn-light-warning">ویرایش</a>
                            <form action="{{ route($resource . '.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این مورد مطمئن هستید؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + 1 }}" class="text-center py-10">رکوردی یافت نشد.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
