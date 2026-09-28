@extends('layout.main')

@section('content')
    <x-page-hero
        :title="__('menu.gallery.menu')"
        :subtitle="__('menu.gallery.incoming_subtitle')"
        icon="bx-images"
    >
        @slot('actions')
            <form action="{{ route('gallery.incoming') }}" method="GET" class="d-flex gap-2">
                <div class="position-relative">
                    <input type="search" name="search" value="{{ $search }}" class="form-control hero-search" placeholder="{{ __('menu.general.search') }}">
                    <i class="bx bx-search hero-search-icon"></i>
                </div>
                <button type="submit" class="btn btn-hero">
                    <i class="bx bx-search"></i>
                </button>
            </form>
        @endslot
    </x-page-hero>

    <div class="alert alert-light border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
        <i class="bx bx-photo-album text-primary" style="font-size: 1.6rem;"></i>
        <div>
            <span class="fw-bold">{{ $data->total() }}</span>
            <span class="text-secondary small">{{ __('menu.gallery.total_attachments') }}</span>
        </div>
        <span class="ms-auto badge rounded-pill text-bg-light border">{{ __('menu.gallery.preview_hint') }}</span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4 mb-5">
        @forelse($data as $attachment)
            <div class="col">
                <x-gallery-card
                    :filename="$attachment->filename"
                    :extension="$attachment->extension"
                    :path="$attachment->path_url"
                    :letter="$attachment->letter"
                />
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bx bx-photo-album text-secondary" style="font-size: 4rem;"></i>
                <h5 class="mt-3">{{ __('menu.general.empty') }}</h5>
            </div>
        @endforelse
    </div>

    {!! $data->appends(['search' => $search])->links() !!}
@endsection