@extends('layout.main')

@section('content')
    <x-breadcrumb :values="[__('menu.agenda.menu'), __('menu.agenda.outgoing_letter')]"></x-breadcrumb>

    <x-page-hero
        :title="__('menu.agenda.menu') . ' — ' . __('menu.agenda.outgoing_letter')"
        :subtitle="__('menu.agenda.subtitle_outgoing')"
        icon="bx bx-book-open">
        <div class="mt-2 d-flex flex-wrap gap-2">
            <span class="hero-chip"><i class="bx bx-spreadsheet me-1"></i>{{ __('menu.agenda.total_records') }}: {{ $data->total() }}</span>
            @if($since && $until)
                <span class="hero-chip"><i class="bx bx-calendar-event me-1"></i>{{ __('menu.agenda.agenda_range') }}: {{ $since }} — {{ $until }}</span>
            @endif
        </div>
        @slot('actions')
            <a href="{{ route('agenda.outgoing.print') . '?' . $query }}" target="_blank" class="btn btn-hero">
                <i class="bx bx-printer me-1"></i>{{ __('menu.general.print') }}
            </a>
        @endslot
    </x-page-hero>

    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 1.1rem;">
        <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bx bx-filter-alt me-1 text-primary"></i>{{ __('menu.agenda.menu') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}">
                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <x-input-form name="since" :label="__('menu.agenda.start_date')" type="date"
                                      :value="$since ? date('Y-m-d', strtotime($since)) : ''"/>
                    </div>
                    <div class="col-md-3">
                        <x-input-form name="until" :label="__('menu.agenda.end_date')" type="date"
                                      :value="$until ? date('Y-m-d', strtotime($until)) : ''"/>
                    </div>
                    <div class="col-md-3">
                        <label for="filter" class="form-label">{{ __('menu.agenda.filter_by') }}</label>
                        <select class="form-select" id="filter" name="filter">
                            <option
                                value="letter_date" @selected(old('filter', $filter) == 'letter_date')>{{ __('model.letter.letter_date') }}</option>
                            <option
                                value="created_at" @selected(old('filter', $filter) == 'created_at')>{{ __('model.general.created_at') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bx bx-search me-1"></i>{{ __('menu.general.filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 1.1rem;">
        <div class="table-responsive text-nowrap">
            <table class="table table-modern">
                <thead>
                <tr>
                    <th>{{ __('model.letter.agenda_number') }}</th>
                    <th>{{ __('model.letter.reference_number') }}</th>
                    <th>{{ __('model.letter.to') }}</th>
                    <th>{{ __('model.letter.letter_date') }}</th>
                </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $agenda)
                        <tr>
                            <td>
                                <span class="badge bg-label-primary rounded-pill px-3 py-2">
                                    <i class="bx bxs-bookmark-star me-1"></i>{{ $agenda->agenda_number }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('transaction.outgoing.show', $agenda) }}" class="fw-semibold text-primary">
                                    {{ $agenda->reference_number }}
                                </a>
                            </td>
                            <td>{{ $agenda->to }}</td>
                            <td>
                                <i class="bx bx-calendar-event text-muted me-1"></i>{{ $agenda->formatted_letter_date }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="bx bx-file-blank display-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('menu.general.empty') }}</p>
                        </td>
                    </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('model.letter.agenda_number') }}</th>
                    <th>{{ __('model.letter.reference_number') }}</th>
                    <th>{{ __('model.letter.to') }}</th>
                    <th>{{ __('model.letter.letter_date') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search, 'since' => $since, 'until' => $until, 'filter' => $filter])->links() !!}
@endsection