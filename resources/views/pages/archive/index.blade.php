@extends('layout.main')

@section('content')
    <x-breadcrumb :values="[__('menu.archive.menu')]"></x-breadcrumb>

    <x-page-hero
        :title="__('menu.archive.menu')"
        :subtitle="__('menu.archive.subtitle')"
        icon="bx bx-archive">
        @slot('actions')
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($type || $since || $until)
                    <a href="{{ route('archive.index') }}" class="btn btn-hero">
                        <i class="bx bx-x me-1"></i>{{ __('menu.archive.all') }}
                    </a>
                @endif
                <a href="{{ route('archive.export', ['type' => $type ?? null, 'search' => $search ?? null, 'since' => $since ?? null, 'until' => $until ?? null]) }}" class="btn btn-hero">
                    <i class="bx bx-download me-1"></i>{{ __('menu.server.export_archive') }}
                </a>
            </div>
        @endslot
    </x-page-hero>

    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 1.1rem;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dev-glyph" style="background: linear-gradient(135deg,#6d67e4,#5448d6)">
                        <i class="bx bx-archive"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('menu.archive.total_archive') }}</div>
                        <div class="fw-bold fs-3 text-primary">{{ $data->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 1.1rem;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dev-glyph" style="background: linear-gradient(135deg,#22d3ee,#0891b2)">
                        <i class="bx bx-log-in-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('menu.archive.total_incoming') }}</div>
                        <div class="fw-bold fs-3 text-info">{{ $totalIncoming }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 1.1rem;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dev-glyph" style="background: linear-gradient(135deg,#ff6bcb,#f43f5e)">
                        <i class="bx bx-log-out-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('menu.archive.total_outgoing') }}</div>
                        <div class="fw-bold fs-3 text-danger">{{ $totalOutgoing }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 1.1rem;">
        <div class="card-header border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bx bx-filter-alt me-1 text-primary"></i>{{ __('menu.archive.menu') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="type" class="form-label">{{ __('menu.archive.type') }}</label>
                    <select class="form-select" id="type" name="type">
                        <option value="" @selected(empty($type))>{{ __('menu.archive.all') }}</option>
                        <option value="incoming" @selected($type == 'incoming')">{{ __('menu.transaction.incoming_letter') }}</option>
                        <option value="outgoing" @selected($type == 'outgoing')">{{ __('menu.transaction.outgoing_letter') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="since" class="form-label">{{ __('menu.agenda.start_date') }}</label>
                    <input type="date" class="form-control" id="since" name="since" value="{{ $since ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="until" class="form-label">{{ __('menu.agenda.end_date') }}</label>
                    <input type="date" class="form-control" id="until" name="until" value="{{ $until ?? '' }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bx bx-search me-1"></i>{{ __('menu.general.filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 1.1rem;">
        <div class="table-responsive text-nowrap">
            <table class="table table-modern">
                <thead>
                <tr>
                    <th>{{ __('menu.archive.type') }}</th>
                    <th>{{ __('model.letter.reference_number') }}</th>
                    <th>{{ __('model.letter.agenda_number') }}</th>
                    <th>{{ __('model.letter.from') }} / {{ __('model.letter.to') }}</th>
                    <th>{{ __('model.letter.letter_date') }}</th>
                    <th>{{ __('model.classification.type') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $letter)
                        <tr>
                            <td>
                                @if($letter->type == 'incoming')
                                    <span class="badge bg-label-info rounded-pill px-3 py-2">
                                        <i class="bx bx-log-in-circle me-1"></i>{{ __('menu.transaction.incoming_letter') }}
                                    </span>
                                @else
                                    <span class="badge bg-label-danger rounded-pill px-3 py-2">
                                        <i class="bx bx-log-out-circle me-1"></i>{{ __('menu.transaction.outgoing_letter') }}
                                    </span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $letter->reference_number }}</td>
                            <td>{{ $letter->agenda_number }}</td>
                            <td>{{ $letter->type == 'incoming' ? $letter->from : $letter->to }}</td>
                            <td><i class="bx bx-calendar-event text-muted me-1"></i>{{ $letter->formatted_letter_date }}</td>
                            <td>{{ $letter->classification?->type ?? '-' }}</td>
                            <td>
                                <a href="{{ $letter->type == 'incoming' ? route('transaction.incoming.show', $letter) : route('transaction.outgoing.show', $letter) }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="bx bx-show me-1"></i>{{ __('menu.general.view') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bx bx-archive display-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('menu.archive.no_archive') }}</p>
                        </td>
                    </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('menu.archive.type') }}</th>
                    <th>{{ __('model.letter.reference_number') }}</th>
                    <th>{{ __('model.letter.agenda_number') }}</th>
                    <th>{{ __('model.letter.from') }} / {{ __('model.letter.to') }}</th>
                    <th>{{ __('model.letter.letter_date') }}</th>
                    <th>{{ __('model.classification.type') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search, 'type' => $type])->links() !!}
@endsection