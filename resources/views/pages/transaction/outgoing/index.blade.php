@extends('layout.main')

@section('content')
    <x-page-hero
        :title="__('menu.transaction.outgoing_letter')"
        :subtitle="__('menu.transaction.outgoing_subtitle')"
        icon="bx-mail-send"
    >
        @slot('actions')
            <form action="{{ route('transaction.outgoing.index') }}" method="GET" class="d-flex gap-2">
                <div class="position-relative">
                    <input type="search" name="search" value="{{ $search }}" class="form-control hero-search"
                           placeholder="{{ __('menu.general.search') }}">
                    <i class="bx bx-search hero-search-icon"></i>
                </div>
                <a href="{{ route('transaction.outgoing.create') }}" class="btn btn-hero">
                    <i class="bx bx-plus me-1"></i>{{ __('menu.general.create') }}
                </a>
            </form>
        @endslot
    </x-page-hero>

    <div class="alert alert-light border-0 shadow-sm d-flex align-items-center gap-3 mb-4 py-3 px-4">
        <i class="bx bx-data text-primary" style="font-size: 1.8rem;"></i>
        <div>
            <span class="fw-bold fs-5">{{ $data->total() }}</span>
            <span class="text-secondary small ms-1">{{ __('menu.agenda.total_records') }}</span>
        </div>
        @if($search)
            <a href="{{ route('transaction.outgoing.index') }}" class="ms-auto btn btn-sm btn-outline-secondary">
                <i class="bx bx-x me-1"></i>{{ __('menu.general.all') }}
            </a>
        @endif
    </div>

    <div class="d-flex flex-column gap-4">
        @forelse($data as $letter)
            <x-letter-card :letter="$letter"/>
        @empty
            <div class="text-center py-5">
                <i class="bx bx-mail-send text-secondary" style="font-size: 4.5rem;"></i>
                <h4 class="mt-3 fw-bold">{{ __('menu.general.empty') }}</h4>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {!! $data->appends(['search' => $search])->links() !!}
    </div>
@endsection
