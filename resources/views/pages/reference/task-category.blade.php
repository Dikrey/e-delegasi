@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('reference.task-category.index') }}/' + id);
            $('#editModal input#edit-id').val(id);
            $('#editModal input#edit-name').val($(this).data('name'));
            $('#editModal input#edit-color').val($(this).data('color'));
            $('#editModal input#edit-description').val($(this).data('description'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('menu.reference.menu'), __('menu.reference.task_category')]"></x-breadcrumb>

    <x-page-hero
        :title="__('menu.reference.task_category')"
        :subtitle="__('menu.reference.task_category_subtitle')"
        icon="bx bx-category">
        <div class="mt-2 d-flex flex-wrap gap-2">
            <span class="hero-chip"><i class="bx bx-category me-1"></i>{{ __('menu.agenda.total_records') }}: {{ $data->total() }}</span>
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
                    <th>{{ __('model.task_category.name') }}</th>
                    <th>{{ __('model.task_category.color') }}</th>
                    <th>{{ __('model.task_category.description') }}</th>
                    <th>{{ __('model.task_category.tasks_count') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $category)
                        <tr>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>
                                <span class="badge rounded-pill" style="background: {{ $category->color }}; color:#fff;">{{ $category->color }}</span>
                            </td>
                            <td>{{ $category->description }}</td>
                            <td><span class="badge bg-label-info">{{ $category->tasks_count }}</span></td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-color="{{ $category->color }}"
                                        data-description="{{ $category->description }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    <i class="bx bx-edit-alt me-1"></i>{{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('reference.task-category.destroy', $category) }}" class="d-inline" method="post">
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
                            <i class="bx bx-category display-4 text-muted"></i>
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
            <form class="modal-content" method="post" action="{{ route('reference.task-category.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('menu.general.create') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="name" :label="__('model.task_category.name')"/>
                    <x-input-form name="color" type="color" value="#696cff" :label="__('model.task_category.color')"/>
                    <x-input-form name="description" :label="__('model.task_category.description')"/>
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
                    <x-input-form name="name" id="edit-name" :label="__('model.task_category.name')"/>
                    <x-input-form name="color" id="edit-color" type="color" :label="__('model.task_category.color')"/>
                    <x-input-form name="description" id="edit-description" :label="__('model.task_category.description')"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
