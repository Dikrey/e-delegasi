<div class="letter-card card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex gap-4">
            <div class="letter-type-icon {{ $letter->type }}">
                <i class="bx {{ $letter->type == 'incoming' ? 'bxs-inbox' : 'bx-mail-send' }}"></i>
            </div>

            <div class="flex-grow-1 letter-card-main" style="min-width: 0;">
                <div class="d-flex justify-content-between flex-column flex-sm-row flex-wrap gap-2">
                    <div class="min-width-0">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <h6 class="mb-0 fw-bold text-break">{{ $letter->reference_number }}</h6>
                            @if($letter->classification)
                                <span class="badge rounded-pill classification-badge">{{ $letter->classification->type }}</span>
                            @endif
                            @if($letter->type == 'outgoing' && $letter->status === 'waiting_for_final_file')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">
                                    <i class="bx bx-edit me-1"></i>{{ $letter->status_label }}
                                </span>
                            @elseif($letter->type == 'outgoing' && $letter->status === 'final')
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill">
                                    <i class="bx bx-check-circle me-1"></i>{{ $letter->status_label }}
                                </span>
                            @endif
                        </div>
                        <small class="text-secondary d-block mt-1">
                            {{ $letter->type == 'incoming' ? $letter->from : $letter->to }}
                            @if($letter->agenda_number)
                                &nbsp;|&nbsp;
                                <span class="text-secondary">{{ __('model.letter.agenda_number') }}:</span>
                                <span class="fw-semibold text-dark">{{ $letter->agenda_number }}</span>
                            @endif
                        </small>
                    </div>

                    <div class="d-flex align-items-start gap-2">
                        <div class="text-end text-secondary">
                            <div class="d-flex align-items-center gap-1 justify-content-end">
                                <i class="bx bx-calendar"></i>{{ $letter->formatted_letter_date }}
                            </div>
                            @if($letter->type == 'incoming' && $letter->received_date)
                                <div class="d-flex align-items-center gap-1 justify-content-end mt-1">
                                    <i class="bx bx-time"></i>{{ __('model.letter.received_date') }}: {{ $letter->formatted_received_date }}
                                </div>
                            @endif
                        </div>

                        <div class="dropdown d-inline-block">
                            <button class="btn btn-light btn-icon btn-sm" type="button" id="dropdown-{{ $letter->type }}-{{ $letter->id }}"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-{{ $letter->type }}-{{ $letter->id }}">
                                @if($letter->type == 'incoming')
                                    @if(!\Illuminate\Support\Facades\Route::is('*.show'))
                                        <a class="dropdown-item" href="{{ route('transaction.incoming.show', $letter) }}">{{ __('menu.general.view') }}</a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('transaction.incoming.edit', $letter) }}">{{ __('menu.general.edit') }}</a>
                                    <form action="{{ route('transaction.incoming.destroy', $letter) }}" class="d-inline" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <span class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                                    </form>
                                @else
                                    @if(!\Illuminate\Support\Facades\Route::is('*.show'))
                                        <a class="dropdown-item" href="{{ route('transaction.outgoing.show', $letter) }}">{{ __('menu.general.view') }}</a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('transaction.outgoing.edit', $letter) }}">{{ __('menu.general.edit') }}</a>
                                    <form action="{{ route('transaction.outgoing.destroy', $letter) }}" class="d-inline" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <span class="dropdown-item cursor-pointer btn-delete">{{ __('menu.general.delete') }}</span>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($letter->description)
                    <p class="letter-desc mb-0">{{ $letter->description }}</p>
                @endif

                <div class="d-flex justify-content-between flex-wrap align-items-center gap-2 mt-1">
                    @if($letter->note)
                        <small class="text-secondary"><i class="bx bx-info-circle me-1"></i>{{ $letter->note }}</small>
                    @else
                        <span></span>
                    @endif

                    <div class="d-flex align-items-center gap-2">
                        @if(count($letter->attachments))
                            <div class="d-flex align-items-center gap-1">
                                @foreach($letter->attachments as $attachment)
                                    <a href="{{ $attachment->path_url }}" target="_blank" class="attachment-chip" title="{{ $attachment->filename }}">
                                        @if(in_array(strtolower($attachment->extension), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ $attachment->path_url }}" alt="attachment" width="42" height="42" class="rounded attachment-thumb">
                                        @elseif(strtolower($attachment->extension) == 'pdf')
                                            <span class="file-tile"><i class="bx bxs-file-pdf"></i></span>
                                        @else
                                            <span class="file-tile"><i class="bx bx-file"></i></span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if($letter->type == 'incoming')
                            <a href="{{ route('transaction.disposition.index', $letter) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-share-alt me-1"></i>{{ __('model.letter.dispose') }}
                                @if($letter->dispositions->count())<span class="badge text-bg-light ms-1">{{ $letter->dispositions->count() }}</span>@endif
                            </a>
                        @endif

                        @if($letter->type == 'outgoing' && $letter->status === 'waiting_for_final_file')
                            <a href="{{ route('transaction.outgoing.edit', $letter) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-upload me-1"></i>{{ __('menu.transaction.upload_final') }}
                            </a>
                            <form action="{{ route('transaction.outgoing.finalize', $letter) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bx bx-check-double me-1"></i>{{ __('menu.transaction.mark_final') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</div>