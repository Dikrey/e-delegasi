@extends('layout.main')

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('agenda.list')" :subtitle="__('agenda.list_subtitle')" icon="bx-calendar">
                <x-slot:actions>
                    <a href="{{ route('agenda-pimpinan.index') }}" class="btn btn-hero"><i class="bx bx-calendar-event me-1"></i>{{ __('agenda.calendar') }}</a>
                    <a href="{{ route('agenda-pimpinan.create') }}" class="btn btn-hero"><i class="bx bx-plus me-1"></i>{{ __('agenda.create_btn') }}</a>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>{{ __('agenda.agenda') }}</th>
                                    <th>{{ __('agenda.type') }}</th>
                                    <th>{{ __('agenda.date') }}</th>
                                    <th>{{ __('agenda.time') }}</th>
                                    <th>{{ __('agenda.location') }}</th>
                                    <th>{{ __('delegation.agenda') }}</th>
                                    <th>{{ __('delegation.status') }}</th>
                                    <th class="text-end">{{ __('menu.general.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('agenda-pimpinan.show', $item->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $item->title }}</a>
                                        </td>
                                        <td><span class="badge bg-label-primary">{{ $item->agenda_type_label }}</span></td>
                                        <td class="small">{{ $item->date?->isoFormat('DD MMM YYYY') }}</td>
                                        <td class="small">{{ $item->start_time }} – {{ $item->end_time }}</td>
                                        <td class="small">{{ $item->location ?? '-' }}</td>
                                        <td class="small">
                                            @if($item->delegation)
                                                <a href="{{ route('delegation.show', $item->delegation->id) }}" class="text-decoration-none">{{ $item->delegation->title }}</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><span class="badge {{ \App\Enums\AgendaStatus::badge($item->status) }}">{{ $item->agenda_status_label }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('agenda-pimpinan.show', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-show"></i></a>
                                            <a href="{{ route('agenda-pimpinan.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bx bx-edit"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">{{ __('agenda.empty') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">{{ $data->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection