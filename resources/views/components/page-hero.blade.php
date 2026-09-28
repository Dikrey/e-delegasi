@props(['title', 'subtitle' => null, 'icon' => null])

<div class="page-hero mb-4">
    <div class="page-hero-inner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            @if($icon)
                <div class="page-hero-icon"><i class="bx {{ $icon }}"></i></div>
            @endif
            <div>
                <h4 class="mb-1 fw-bold">{{ $title }}</h4>
                @if($subtitle)
                    <p class="mb-0 page-hero-sub">{{ $subtitle }}</p>
                @endif
                {{ $slot }}
            </div>
        </div>
        @if(!empty($actions))
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>