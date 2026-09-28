@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('reference.status.index') }}/' + id);
            $('#editModal input#edit-id').val(id);
            $('#editModal input#edit-status').val($(this).data('status'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('menu.reference.menu'), __('menu.reference.status')]"></x-breadcrumb>

    <x-page-hero
        :title="__('menu.reference.status')"
        :subtitle="__('menu.reference.status_subtitle')"
        icon="bx bx-check-shield">
        <div class="mt-2 d-flex flex-wrap gap-2">
            <span class="hero-chip"><i class="bx bx-library me-1"></i>{{ __('menu.agenda.total_records') }}: {{ $data->total() }}</span>
        </div>
        @slot('actions')
            <button type="button" class="btn btn-hero" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus me-1"></i>{{ __('menu.general.create') }}
            </button>
        @endslot
    </x-page-hero>

    <div class="card mb-4 shadow-sm" style="border: none; border-radius: 1.1rem;">
        <div class="table-responsive text-nowrap">
            <table class="table table-modern">
                <thead>
                <tr>
                    <th style="width: 20%">#</th>
                    <th>{{ __('model.status.status') }}</th>
                    <th style="width: 30%">{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $status)
                        <tr>
                            <td><span class="badge bg-label-primary rounded-pill px-3 py-2 fw-bold">#{{ $status->id }}</span></td>
                            <td>{{ $status->status }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $status->id }}"
                                        data-status="{{ $status->status }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    <i class="bx bx-edit-alt me-1"></i>{{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('reference.status.destroy', $status) }}" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete"
                                            type="button"><i class="bx bx-trash me-1"></i>{{ __('menu.general.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <i class="bx bx-file-blank display-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('menu.general.empty') }}</p>
                        </td>
                    </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                <tr>
                    <th>#</th>
                    <th>{{ __('model.status.status') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!}

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('reference.status.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createModalTitle">{{ __('menu.general.create') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="status" :label="__('model.status.status')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>{{ __('menu.general.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editModalTitle">{{ __('menu.general.edit') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id" value="">
                    <x-input-form name="status" id="edit-status" :label="__('model.status.status')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection