@php
    $safeId = preg_replace('/[^a-zA-Z0-9]+/', '-', $filename);
    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
    $isPdf = strtolower($extension) == 'pdf';
@endphp
<div class="gallery-item card border-0 shadow-sm overflow-hidden">
    <button type="button" class="gallery-preview-trigger position-relative w-100 p-0 border-0 bg-transparent"
            data-bs-toggle="modal" data-bs-target="#galleryModal-{{ $safeId }}">
        @if($isImage)
            <img src="{{ $path }}" alt="{{ $filename }}" class="gallery-cover" loading="lazy">
        @elseif($isPdf)
            <div class="gallery-pdf-tile">
                <i class="bx bxs-file-pdf"></i>
                <span class="text-uppercase small fw-bold">{{ $extension }}</span>
            </div>
        @else
            <div class="gallery-pdf-tile bg-light">
                <i class="bx bx-file text-secondary"></i>
                <span class="text-uppercase small fw-bold text-secondary">{{ $extension }}</span>
            </div>
        @endif
        <div class="gallery-overlay">
            <span class="gallery-overlay-btn"><i class="bx bx-show"></i>{{ __('menu.gallery.preview') }}</span>
        </div>
    </button>

    <div class="gallery-item-body p-3">
        @if($letter->type == 'incoming')
            <a href="{{ route('transaction.incoming.show', $letter) }}" class="fw-semibold text-truncate d-block text-decoration-none">{{ $letter->reference_number }}</a>
        @else
            <a href="{{ route('transaction.outgoing.show', $letter) }}" class="fw-semibold text-truncate d-block text-decoration-none">{{ $letter->reference_number }}</a>
        @endif
        <small class="text-secondary text-truncate d-block mt-1" title="{{ $filename }}">{{ $filename }}</small>
        <div class="small text-secondary d-flex align-items-center gap-2 mt-1">
            <i class="bx bx-calendar"></i>{{ $letter->formatted_letter_date }}
        </div>
    </div>
</div>

<div class="modal fade" id="galleryModal-{{ $safeId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 py-2">
                <div class="d-flex flex-column">
                    <strong>{{ $letter->reference_number }}</strong>
                    <small class="text-secondary text-truncate" style="max-width: 60vw;">{{ $filename }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 bg-dark bg-opacity-10 rounded-bottom-3">
                @if($isImage)
                    <div class="text-center">
                        <img src="{{ $path }}" alt="{{ $filename }}" class="img-fluid rounded-3 gallery-preview-img" style="max-height: 68vh;">
                    </div>
                @elseif($isPdf)
                    <iframe src="{{ $path }}" class="w-100 rounded-3 gallery-preview-pdf" style="height: 68vh; border: 0;" title="{{ $filename }}"></iframe>
                @else
                    <div class="text-center py-5 text-secondary">{{ __('menu.gallery.no_preview') }}</div>
                @endif
            </div>
            <div class="modal-footer border-0">
                @if($letter->type == 'incoming')
                    <a href="{{ route('transaction.incoming.show', $letter) }}" class="btn btn-light"><i class="bx bx-link-external me-1"></i>{{ __('menu.gallery.view_letter') }}</a>
                @else
                    <a href="{{ route('transaction.outgoing.show', $letter) }}" class="btn btn-light"><i class="bx bx-link-external me-1"></i>{{ __('menu.gallery.view_letter') }}</a>
                @endif
                <a href="{{ $path }}" target="_blank" class="btn btn-primary"><i class="bx bx-download me-1"></i>{{ __('menu.general.download') }}</a>
            </div>
        </div>
    </div>
</div>