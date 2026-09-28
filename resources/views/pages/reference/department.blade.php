@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('reference.department.index') }}/' + id);
            $('#editModal input#edit-id').val(id);
            $('#editModal input#edit-code').val($(this).data('code'));
            $('#editModal input#edit-name').val($(this).data('name'));
            $('#editModal input#edit-description').val($(this).data('description'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('menu.reference.menu'), __('menu.reference.department')]"></x-breadcrumb>

    <x-page-hero
        :title="__('menu.reference.department')"
        :subtitle="__('menu.reference.department_subtitle')"
        icon="bx bx-buildings">
        <div class="mt-2 d-flex flex-wrap gap-2">
            <span class="hero-chip"><i class="bx bx-buildings me-1"></i>{{ __('menu.agenda.total_records') }}: {{ $data->total() }}</span>
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
                    <th>{{ __('model.department.code') }}</th>
                    <th>{{ __('model.department.name') }}</th>
                    <th>{{ __('model.department.description') }}</th>
                    <th>{{ __('model.department.users_count') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $department)
                        <tr>
                            <td>
                                <span class="badge bg-label-primary rounded-pill px-3 py-2 fw-bold">{{ $department->code ?? '-' }}</span>
                            </td>
                            <td class="fw-semibold">{{ $department->name }}</td>
                            <td>{{ $department->description }}</td>
                            <td><span class="badge bg-label-info">{{ $department->users_count }}</span></td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $department->id }}"
                                        data-code="{{ $department->code }}"
                                        data-name="{{ $department->name }}"
                                        data-description="{{ $department->description }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    <i class="bx bx-edit-alt me-1"></i>{{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('reference.department.destroy', $department) }}" class="d-inline" method="post">
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
                        <td colspan="5" class="text-center py-4">
                            <i class="bx bx-buildings display-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('menu.general.empty') }}</p>
                        </td>
                    </tr>
                    </tbody>
                @endif
            </table>
        </div>
    </div>

    {!! $data->appends(['search' => $search])->links() !!}

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{ route('reference.department.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('menu.general.create') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="code" :label="__('model.department.code')"/>
                    <x-input-form name="name" :label="__('model.department.name')"/>
                    <x-input-form name="description" :label="__('model.department.description')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
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
                    <h5 class="modal-title fw-bold">{{ __('menu.general.edit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id" value="">
                    <x-input-form name="code" id="edit-code" :label="__('model.department.code')"/>
                    <x-input-form name="name" id="edit-name" :label="__('model.department.name')"/>
                    <x-input-form name="description" id="edit-description" :label="__('model.department.description')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
