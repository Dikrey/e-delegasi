@extends('layout.main')

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('activity.menu')" :subtitle="__('activity.menu_subtitle')" icon="bx-history"></x-page-hero>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3">
                        <div class="col-auto">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('menu.general.search') }}">
                        </div>
                        <div class="col-auto">
                            <select name="module" class="form-select">
                                <option value="">{{ __('activity.all_module') }}</option>
                                @foreach(__('activity.module') as $key => $label)
                                    <option value="{{ $key }}" {{ $module === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary"><i class="bx bx-filter-alt me-1"></i>{{ __('menu.general.filter') }}</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset me-1"></i>{{ __('menu.general.reset') }}</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>{{ __('activity.time') }}</th>
                                    <th>{{ __('activity.user') }}</th>
                                    <th>{{ __('activity.module_name') }}</th>
                                    <th>{{ __('activity.activity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $log)
                                    <tr>
                                        <td class="small text-nowrap">{{ $log->created_at?->isoFormat('DD MMM YYYY, HH:mm') }}</td>
                                        <td><span class="fw-semibold">{{ $log->user?->name }}</span> <small class="text-muted">({{ $log->user?->role_label }})</small></td>
                                        <td>
                                            <span class="badge bg-label-primary">{{ $log->module_label }}</span>
                                            @if($log->action)
                                                <span class="badge bg-label-secondary ms-1">{{ $log->action }}</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ $log->action }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">{{ __('activity.empty') }}</td></tr>
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