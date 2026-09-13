@php
    $user = auth()->user();
@endphp
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <h3 class="card-label text-blue">{{ $title }}</h3>
            @php
                $hasPagination = isset($rows) && $rows instanceof \Illuminate\Pagination\LengthAwarePaginator;
                if (!$hasPagination && isset($paginations) && $paginations instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                    $hasPagination = true;
                }
            @endphp
            @if($hasPagination)
                @php
                    $currentPage = $paginations->currentPage();
                    $perPage = $paginations->perPage();
                    $total = $paginations->total();
                    $firstRecord = ($currentPage - 1) * $perPage + 1;
                    $lastRecord = min($currentPage * $perPage, $total);
                @endphp
                <span class="summary ms-3 text-muted fs-7">
                    {{__('messages.global.table.page')}}
                    {{ $firstRecord }}-{{ $lastRecord }}
                    {{ __('messages.global.table.of_the') }}
                    {{ $total }}
                    {{ __('messages.global.table.entries')}}
                </span>
            @endif
        </div>
        <!--end::Card title-->
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <div id="kt_customers_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer" id="kt_customers_table">
                    <thead>
                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        @php
//                            $hasActionColumn = false;
                            $hasActionColumn = true;

                            $isNotEmpty = function($data) {
                                if(is_null($data)) return false;
                                if(is_array($data)) return !empty($data);
                                if(method_exists($data, 'isNotEmpty')) return $data->isNotEmpty();
                                return !empty($data);
                            };

                            if($isNotEmpty($rows) && $user && isset($actions) && !empty($actions)) {
                                foreach($actions as $action) {
                                    if(empty($action['permission']) || $user->hasPermission($action['permission'])) {
                                        $hasActionColumn = true;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        {{-- ستون شماره --}}
                        <th class="text-center min-w-50px">#</th>

                        @foreach($headers as $header)
                            <th class="min-w-125px">{{ $header['label'] }}</th>
                        @endforeach

                        @if($hasActionColumn)
                            <th class="text-end min-w-70px">{{ __('messages.global.table.action') }}</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    @forelse($rows as $index => $row)
                        <tr>
                            {{-- ستون شماره --}}
                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            @foreach($headers as $header)
                                @php
                                    $value = data_get($row, $header['key']);
                                    $cellClass = $header['class'] ?? '';
                                    $hasRenderer = isset($header['render']) && is_callable($header['render']);
                                @endphp
                                <td class="{{ $cellClass }}">
                                    @if($hasRenderer)
                                        {!! $header['render']($value, $row) !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach

                            @if($hasActionColumn && !empty($actions))
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-light btn-flex btn-center show menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        {{ __('messages.global.label.option') ?? 'Actions' }}
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                        @foreach($actions as $action)
                                            @php
                                                $shouldDisplay = true;
                                                if (isset($action['condition'])) {
                                                    $condition = $action['condition'];
                                                    if (is_callable($condition)) {
                                                        if (is_array($row)) {
                                                            $rowObj = (object) $row;
                                                            $shouldDisplay = $condition($rowObj);
                                                        } else {
                                                            $shouldDisplay = $condition($row);
                                                        }
                                                    }
                                                    elseif (is_string($condition) && is_object($row) && method_exists($row, $condition)) {
                                                        $shouldDisplay = $row->{$condition}();
                                                    }
                                                    elseif (is_bool($condition)) {
                                                        $shouldDisplay = $condition;
                                                    }
                                                }
                                            @endphp
                                            @if(empty($action['permission']) || $user->hasPermission($action['permission']))
                                                @if($shouldDisplay)
                                                    @php
                                                        $routeParams = data_get($row, 'id', $index);
                                                        $label = $action['label'] ?? '';

                                                        if (isset($action['label_callback']) && is_callable($action['label_callback'])) {
                                                            $label = $action['label_callback']($row);
                                                        }

                                                        $route = $action['route'] ?? null;

                                                        if (isset($action['route_callback']) && is_callable($action['route_callback'])) {
                                                            $url = $action['route_callback']($row);
                                                        } elseif ($route) {
                                                            $url = route($route, $routeParams);
                                                        } else {
                                                            $url = '#';
                                                        }

                                                        $icon = $action['icon'] ?? '';
                                                        $iconClass = $action['icon_class'] ?? '';
                                                    @endphp

                                                    @if(isset($action['type']) && $action['type'] === 'modal')
                                                        <div class="menu-item px-3">
                                                            <a href="#"
                                                               class="{{ $action['class'] ?? 'menu-link px-3' }}"
                                                               id="{{ $action['id'] ?? '' }}"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#{{ $action['modal_id'] }}"
                                                               @if(isset($action['data_attributes']))
                                                                   @foreach($action['data_attributes'] as $key => $value)
                                                                       @php
                                                                           $dataValue = null;

                                                                           if ($key === 'action' && isset($action['route_callback']) && is_callable($action['route_callback'])) {
                                                                               $dataValue = call_user_func($action['route_callback'], $row);
                                                                           }
                                                                           elseif (is_callable($value)) {
                                                                               $dataValue = $value($row);
                                                                           }
                                                                           else {
                                                                               $dataValue = data_get($row, $value);
                                                                           }
                                                                           if ($dataValue === null) {
                                                                               $dataValue = '';
                                                                           }
                                                                       @endphp
                                                                       data-{{ $key }}="{{ e($dataValue) }}"
                                                            @endforeach
                                                            @endif

                                                            @if(isset($action['attributes']))
                                                                @foreach($action['attributes'] as $attribute => $value)
                                                                    @php
                                                                        $attributeValue = is_callable($value) ? $value($row) : $value;
                                                                    @endphp
                                                                    {{ $attribute }}="{{ e($attributeValue) }}"
                                                                @endforeach
                                                            @endif
                                                            >
                                                            @if(!empty($icon))
                                                                <i class="ms-2 fs-3 {{ $iconClass }} {{ $icon }}"></i>
                                                                @endif
                                                                {{ $label }}
                                                                </a>
                                                        </div>
                                                    @elseif(isset($action['type']) && $action['type'] == 'button')
                                                        <div class="menu-item px-3">
                                                            <a class="{{ $action['class'] ?? 'menu-link px-3' }} btn-edit-item"
                                                               id="{{ $action['id'] ?? '' }}"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#{{ $action['modal_id'] ?? 'editModal' }}"
                                                               data-id="{{ data_get($row, 'id') }}"
                                                               data-url="{{ route($action['route'] ?? '#', data_get($row, 'id')) }}"
                                                               @if(isset($action['attributes']))
                                                                   @foreach($action['attributes'] as $attribute)
                                                                       data-{{ $attribute }}="{{ data_get($row, $attribute) }}"
                                                               @endforeach
                                                               @endif
                                                               @if(isset($action['data_attributes']))
                                                                   @foreach($action['data_attributes'] as $key => $value)
                                                                       data-{{ $key }}="{{ data_get($row, $value) }}"
                                                                @endforeach
                                                                @endif
                                                            >
                                                                @if(!empty($icon))
                                                                    <i class="ms-2 fs-3 {{ $iconClass }} {{ $icon }}"></i>
                                                                @endif
                                                                {{ $action['label'] }}
                                                            </a>
                                                        </div>
                                                    @elseif(isset($action['type']) && $action['type'] == 'link')
                                                        <div class="menu-item px-3">
                                                            <a href="{{ $url }}"
                                                               class="{{ $action['class'] ?? 'menu-link px-3' }}"
                                                               id="{{ $action['id'] ?? '' }}"
                                                               @if(isset($action['target']))
                                                                   target="{{ $action['target'] }}"
                                                            @endif

                                                            @if(isset($action['attributes']))
                                                                @foreach($action['attributes'] as $attribute => $value)
                                                                    @php
                                                                        $attributeValue = is_callable($value)
                                                                            ? $value($row)
                                                                            : $value;
                                                                    @endphp
                                                                    {{ $attribute }}="{{ e($attributeValue) }}"
                                                                @endforeach
                                                            @endif

                                                            @if(isset($action['data_attributes']))
                                                                @foreach($action['data_attributes'] as $key => $value)
                                                                    @php
                                                                        $dataValue = is_callable($value)
                                                                            ? $value($row)
                                                                            : data_get($row, $value);
                                                                    @endphp
                                                                    data-{{ $key }}="{{ e($dataValue) }}"
                                                                @endforeach
                                                            @endif
                                                            >
                                                            @if(!empty($icon))
                                                                <i class="ms-2 fs-3 {{ $iconClass }} {{ $icon }}"></i>
                                                                @endif
                                                                {{ $label }}
                                                                </a>
                                                        </div>
                                                    @elseif(isset($action['type']) && $action['type'] === 'delete')
                                                        @php
                                                            $deleteUrl = '#';
                                                            if (!empty($action['route_callback']) && is_callable($action['route_callback'])) {
                                                                $deleteUrl = call_user_func($action['route_callback'], $row);
                                                            } elseif (!empty($action['route'])) {
                                                                $deleteUrl = route(
                                                                    $action['route'],
                                                                    data_get($row, 'id', $index)
                                                                );
                                                            }
                                                            $icon = $action['icon'] ?? null;
                                                            $iconClass = $action['icon_class'] ?? '';
                                                        @endphp
                                                        <div class="menu-item px-3">
                                                            <a href="#"
                                                               class="{{ $action['class'] ?? 'menu-link px-3' }}"
                                                               id="{{ $action['id'] ?? '' }}"
                                                               data-kt-remove-table-filter="delete_row"
                                                               data-delete-url="{{ $deleteUrl }}"
                                                               @if(isset($action['target'])) target="{{ $action['target'] }}" @endif
                                                            >
                                                                @if(!empty($icon))
                                                                    <i class="ms-2 fs-3 {{ $iconClass }} {{ $icon }}"></i>
                                                                @endif
                                                                {{ is_callable($action['label'] ?? null)
                                                                    ? call_user_func($action['label'], $row)
                                                                    : ($action['label'] ?? '') }}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="menu-item px-3">
                                                            <a href="{{ $url }}"
                                                               class="{{ $action['class'] ?? 'menu-link px-3' }}"
                                                               id="{{ $action['id'] ?? '' }}"
                                                               @if(isset($action['target'])) target="{{ $action['target'] }}" @endif
                                                            >
                                                                @if(!empty($icon))
                                                                    <i class="ms-2 fs-3 {{ $iconClass }} {{ $icon }}"></i>
                                                                @endif
                                                                {{ $action['label'] }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) + ($hasActionColumn && !empty($actions) ? 1 : 0) + ($hasPagination ? 1 : 0) + 1 }}"
                                class="text-center text-muted py-9">
                                <i class="fas fa-database fs-1 mb-3 d-block"></i>
                                <span>{{ __('messages.global.label.record_not_found') }}</span>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($hasPagination)
                <div class="row mt-5">
                    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="dataTables_length" id="kt_customers_table_length">
                            <label>
                                <select name="per_page" aria-controls="kt_customers_table" class="form-select form-select-sm form-select-solid" id="per_page_select">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    @php
                        $queryParams = request()->query();
                        unset($queryParams['page']);
                        foreach ($queryParams as $key => $value) {
                            if (str_contains($key, 'filter')) {
                                unset($queryParams[$key]);
                            }
                        }
                        $queryString = http_build_query($queryParams);

                        $currentPage = $paginations->currentPage();
                        $lastPage = $paginations->lastPage();
                        $pages = [];
                        if ($lastPage <= 6) {
                            for ($i = 1; $i <= $lastPage; $i++) {
                                $pages[] = $i;
                            }
                        } else {
                            $pages[] = 1;
                            if ($currentPage <= 4) {
                                for ($i = 2; $i <= 5; $i++) {
                                    $pages[] = $i;
                                }
                                $pages[] = '...';
                                $pages[] = $lastPage;
                            } elseif ($currentPage >= $lastPage - 3) {
                                $pages[] = '...';
                                for ($i = $lastPage - 4; $i <= $lastPage - 1; $i++) {
                                    $pages[] = $i;
                                }
                                $pages[] = $lastPage;
                            } else {
                                $pages[] = '...';
                                for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++) {
                                    $pages[] = $i;
                                }
                                $pages[] = '...';
                                $pages[] = $lastPage;
                            }
                        }
                    @endphp
                    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                        <div class="dataTables_paginate paging_simple_numbers" id="kt_customers_table_paginate">
                            <ul class="pagination">
                                <li class="paginate_button page-item previous {{ $paginations->onFirstPage() ? 'disabled' : '' }}">
                                    <a href="{{ $paginations->previousPageUrl() . ($queryString ? '&' . $queryString : '') }}" class="page-link">
                                        <i class="previous"></i>
                                    </a>
                                </li>
                                @foreach ($pages as $page)
                                    @if ($page === '...')
                                        <li class="paginate_button page-item disabled">
                                            <span class="page-link">…</span>
                                        </li>
                                    @else
                                        <li class="paginate_button page-item {{ $currentPage == $page ? 'active' : '' }}">
                                            <a href="{{ $paginations->url($page) . ($queryString ? '&' . $queryString : '') }}" class="page-link">
                                                {{ $page }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                                <li class="paginate_button page-item next {{ $paginations->hasMorePages() ? '' : 'disabled' }}">
                                    <a href="{{ $paginations->nextPageUrl() . ($queryString ? '&' . $queryString : '') }}" class="page-link">
                                        <i class="next"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>

<script>
    document.getElementById('per_page_select')?.addEventListener('change', function() {
        var perPage = this.value;
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    });

    const translations = {
        are_you_sure: "{{ __('messages.global.label.are_you_sure_about_removing') }}",
        yes_delete: "{{ __('messages.global.label.yes_delete') }}",
        no_return: "{{ __('messages.global.label.no_return') }}"
    };
    $(document).on('click', '[data-kt-remove-table-filter="delete_row"]', function(e) {
        e.preventDefault();

        const deleteButton = this;
        const parent = deleteButton.closest('tr');
        const invoiceNumber = parent ? parent.querySelectorAll('td')[0]?.innerText : '';

        const deleteUrl = deleteButton.getAttribute('data-delete-url');

        if (!deleteUrl) {
            console.error('Delete URL not found');
            return;
        }

        Swal.fire({
            text: translations.are_you_sure,
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: translations.yes_delete,
            cancelButtonText: translations.no_return,
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then(function (result) {
            if (result.value) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                form.style.display = 'none';

                const csrfInput = document.createElement('input');
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>
