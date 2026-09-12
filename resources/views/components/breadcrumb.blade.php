<div class="toolbar1" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @props(['items' => []])

        @if(count($items))
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                <i class="icon-home11"></i>  @foreach($items as $key => $item)
                    <li class="breadcrumb-item {{ !$loop->last ? 'text-muted' : 'text-dark' }}">

                        @if(!$loop->last)
                            <a href="{{ $item['url'] ?? '#' }}" class="text-muted text-hover-primary">{{ $item['label'] }}</a>

                        @else
                            {{ $item['label'] }}
                        @endif
                    </li>

                    @if(!$loop->last)
                        <li class="breadcrumb-item">
                            <span class="me-0 ms-1">/</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
<h2 class="d-flex page-header align-items-center text-dark fw-bolder fs-3 my-2 mt-5 mb-5 "><i class="h-5px w-5px bg-black ms-3"></i>{{ $items[count($items)-1]['label'] ?? '' }}</h2>
