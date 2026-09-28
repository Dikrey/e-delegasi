@extends('layout.main')

@push('style')
    <style>
        .vf-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3); }
        .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px dashed rgba(38,42,71,0.1); }
        .info-row:last-child { border-bottom: none; }
        .status-pill { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.9rem; border-radius: 999px; font-weight: 600; font-size: 0.8rem; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('delegation.verify_letter')" :subtitle="$data->reference_number" icon="bx-check-shield">
                <x-slot:actions>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2">{{ $data->verification_status_label }}</span>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-lg-7">
            <div class="card vf-card mb-4">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.letter_detail') }}</h5></div>
                <div class="card-body">
                    <div class="info-row"><span class="text-muted">{{ __('delegation.letter_number') }}</span><strong>{{ $data->reference_number }}</strong></div>
                    <div class="info-row"><span class="text-muted">{{ __('delegation.letter_subject') }}</span><strong>{{ $data->description }}</strong></div>
                    <div class="info-row"><span class="text-muted">{{ __('delegation.letter_from') }}</span><strong>{{ $data->from }}</strong></div>
                    <div class="info-row"><span class="text-muted">{{ __('delegation.letter_date') }}</span><strong>{{ $data->letter_date?->isoFormat('DD MMMM YYYY') }}</strong></div>
                    <div class="info-row"><span class="text-muted">{{ __('letters.classification') }}</span><strong>{{ $data->classification?->name ?? '-' }}</strong></div>
                    <div class="info-row"><span class="text-muted">{{ __('letters.user') }}</span><strong>{{ $data->user?->name ?? '-' }}</strong></div>
                    @if($data->verified_at)
                        <div class="info-row"><span class="text-muted">{{ __('delegation.verified_by') }}</span><strong>{{ $data->verifier?->name }} · {{ $data->verified_at->isoFormat('DD MMM YYYY, HH:mm') }}</strong></div>
                    @endif
                </div>
            </div>

            @if($delegations->count())
                <div class="card vf-card">
                    <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.follow_up') }}</h5></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead><tr>
                                    <th>{{ __('delegation.delegation') }}</th>
                                    <th>{{ __('delegation.staff') }}</th>
                                    <th>{{ __('delegation.status') }}</th>
                                </tr></thead>
                                <tbody>
                                    @foreach($delegations as $delegation)
                                        <tr>
                                            <td><a href="{{ route('delegation.show', $delegation->id) }}" class="text-decoration-none fw-semibold">{{ $delegation->title }}</a></td>
                                            <td class="small">{{ $delegation->tasks->pluck('staff.name')->implode(', ') ?: '-' }}</td>
                                            <td><span class="badge bg-label-primary">{{ $delegation->status_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center gap-2">
                    <i class="bx bx-info-circle fs-4"></i>
                    <span>{{ __('delegation.no_follow_up') }}</span>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card vf-card">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.verify_form') }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transaction.incoming.verified', $data->id) }}">
                        @csrf
                        <label class="form-label fw-semibold">{{ __('delegation.verification_status') }} <span class="text-danger">*</span></label>
                        <select name="verification_status" class="form-select mb-3" required>
                            @foreach(__('enums.letter_verification') as $key => $label)
                                <option value="{{ $key }}" {{ old('verification_status', $data->verification_status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('verification_status') <small class="text-danger d-block mb-2">{{ $message }}</small> @enderror

                        <label class="form-label fw-semibold">{{ __('delegation.verification_note') }}</label>
                        <textarea name="verification_note" rows="4" class="form-control mb-3" placeholder="{{ __('delegation.verification_note_placeholder') }}">{{ old('verification_note', $data->verification_note) }}</textarea>
                        @error('verification_note') <small class="text-danger d-block mb-2">{{ $message }}</small> @enderror

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-check-shield me-1"></i>{{ __('delegation.save_verification') }}</button>
                            <a href="{{ route('delegation.create', ['letter_id' => $data->id, 'title' => $data->description]) }}" class="btn btn-outline-primary">
                                <i class="bx bx-task me-1"></i>{{ __('delegation.create_from_letter') }}
                            </a>
                            <a href="{{ route('transaction.incoming.show', $data->id) }}" class="btn btn-outline-secondary">{{ __('menu.general.back') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection