<div class="card mb-4">
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between flex-column flex-sm-row gap-2">
            <div class="card-title">
                <h5 class="text-nowrap mb-0 fw-bold">{{ $disposition->status?->status }}</h5>
                <small class="text-black">{{ $disposition->to }}</small>
            </div>
            <div class="card-title d-flex align-items-center flex-wrap gap-2">
                <div class="text-sm-start text-sm-end text-black">
                    <small class="d-block text-secondary">{{ __('model.disposition.due_date') }}</small>
                    <small class="fw-semibold">{{ $disposition->formatted_due_date }}</small>
                </div>
                <div class="dropdown d-inline-block">
                    <button class="btn p-0" type="button" id="dropdown-disposition-{{ $disposition->id }}" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-disposition-{{ $disposition->id }}">
                        <a class="dropdown-item"
                           href="{{ route('transaction.disposition.edit', [$letter, $disposition]) }}">{{ __('menu.general.edit') }}</a>
                        <form action="{{ route('transaction.disposition.destroy', [$letter, $disposition]) }}" class="d-inline"
                              method="post">
                            @csrf
                            @method('DELETE')
                            <span
                                class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <hr>
        <p>{{ $disposition->content }}</p>

        <div class="d-flex flex-wrap gap-3 mb-2">
            @if($disposition->formatted_received_at)
                <div>
                    <small class="text-secondary fw-bold d-block">{{ __('model.disposition.received_at') }}</small>
                    <small>{{ $disposition->formatted_received_at }}</small>
                </div>
            @endif
            @if($disposition->note)
                <div>
                    <small class="text-secondary fw-bold d-block">{{ __('model.disposition.note') }}</small>
                    <small>{{ $disposition->note }}</small>
                </div>
            @endif
        </div>

        @if(count($disposition->forwarded_labels))
            <div class="mb-1">
                <small class="text-secondary fw-bold d-block mb-1">{{ __('model.disposition.forwarded_to') }}</small>
                <div>
                    @foreach($disposition->forwarded_labels as $label)
                        <span class="badge bg-label-primary me-1 mb-1">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if(count($disposition->honor_labels))
            <div class="mb-1">
                <small class="text-secondary fw-bold d-block mb-1">{{ __('model.disposition.honor') }}</small>
                <div>
                    @foreach($disposition->honor_labels as $label)
                        <span class="badge bg-label-info me-1 mb-1">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($disposition->instruction)
            <div class="mb-1">
                <small class="text-secondary fw-bold d-block mb-1">{{ __('model.disposition.instruction') }}</small>
                <div>{{ $disposition->instruction }}</div>
            </div>
        @endif

        <hr>

        {{-- Status verifikasi --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            @if($disposition->verified_at)
                <span class="badge bg-label-success">
                    <i class="bx bx-check-double me-1"></i>{{ __('model.disposition.verified') }}
                </span>
                <small class="text-muted">
                    {{ $disposition->verifier?->name }}
                    &middot; {{ $disposition->verified_at->isoFormat('D MMMM YYYY, HH:mm') }}
                </small>
                @if($disposition->is_received === true || $disposition->is_received === false)
                    <span class="badge {{ $disposition->is_received ? 'bg-label-success' : 'bg-label-danger' }}">
                        {{ $disposition->is_received ? __('model.disposition.received_yes') : __('model.disposition.received_no') }}
                    </span>
                @endif
            @else
                <span class="badge bg-label-warning">
                    <i class="bx bx-time me-1"></i>{{ __('model.disposition.not_verified') }}
                </span>
            @endif
        </div>

        {{-- Aksi --}}
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-sm btn-outline-primary"
               href="{{ route('transaction.disposition.print', [$letter, $disposition]) }}"
               target="_blank">
                <i class="bx bx-printer me-1"></i>{{ __('model.disposition.print_sheet') }}
            </a>
            @if(in_array(auth()->user()->role, ['admin', 'sekretaris']))
                <a class="btn btn-sm btn-success"
                   href="{{ route('transaction.disposition.verify', [$letter, $disposition]) }}">
                    <i class="bx bx-shield-quarter me-1"></i>{{ __('model.disposition.verify_sheet') }}
                </a>
            @endif
        </div>

        {{ $slot }}
    </div>
</div>