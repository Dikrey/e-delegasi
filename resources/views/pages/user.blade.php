@extends('layout.main')

@push('script')
    <script>
        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $('#editModal form').attr('action', '{{ route('user.index') }}/' + id);
            $('#editModal input#edit-id').val(id);
            $('#editModal input#edit-name').val($(this).data('name'));
            $('#editModal input#edit-phone').val($(this).data('phone'));
            $('#editModal input#edit-email').val($(this).data('email'));
            $('#editModal select#edit-role').val($(this).data('role'));
            $('#editModal input#edit-password').val('');
            $('#editModal input#edit-password_confirmation').val('');
            $('#editModal input#edit-is_active').prop('checked', $(this).data('active') == 1);
            $('#editModal select#edit-department').val($(this).data('department'));
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb
        :values="[__('menu.users')]">
        <button
            type="button"
            class="btn btn-primary btn-create"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            {{ __('menu.general.create') }}
        </button>
    </x-breadcrumb>

    @if(auth()->user()->role == 'admin')
        <div class="alert alert-info d-flex align-items-start gap-3 mb-4 shadow-sm" role="alert" style="border-radius: 1rem;">
            <i class="bx bx-info-circle fs-3 lh-1"></i>
            <div>
                <strong>{{ __('user.role_explainer_title') }}</strong>
                <ul class="mb-0 mt-1 ps-3">
                    <li><strong>{{ __('model.user.admin') }}</strong> – {{ __('user.role_admin_description') }}</li>
                    <li><strong>{{ __('model.user.sekretaris') }}</strong> – {{ __('user.role_sekretaris_description') }}</li>
                    <li><strong>{{ __('model.user.staff') }}</strong> – {{ __('user.role_staff_description') }}</li>
                </ul>
            </div>
        </div>
    @endif

    <div class="card mb-5 shadow-sm" style="border-radius: 1rem; border: none;">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('model.user.name') }}</th>
                    <th>{{ __('model.user.email') }}</th>
                    <th>{{ __('model.user.phone') }}</th>
                    <th>{{ __('model.user.role') }}</th>
                    <th>{{ __('model.user.is_active') }}</th>
                    <th>{{ __('model.department.name') }}</th>
                    <th>{{ __('menu.general.action') }}</th>
                </tr>
                </thead>
                @if($data->count())
                    <tbody>
                    @foreach($data as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="{{ $user->profile_picture }}" alt class="rounded-circle h-auto w-px-32"/>
                                    </div>
                                    <div>{{ $user->name }}</div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-label-primary me-1"><i class="bx bxs-user-badge me-1"></i>{{ __('model.user.admin') }}</span>
                                @elseif($user->role == 'sekretaris')
                                    <span class="badge bg-label-success me-1"><i class="bx bxs-id-card me-1"></i>{{ __('model.user.sekretaris') }}</span>
                                @else
                                    <span class="badge bg-label-info me-1"><i class="bx bxs-user me-1"></i>{{ __('model.user.staff') }}</span>
                                @endif
                            </td>
                            <td><span
                                    class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-danger' }} me-1">{{ __('model.user.' . ($user->is_active ? 'active' : 'nonactive')) }}</span>
                            </td>
                            <td>
                                @if($user->department)
                                    <span class="badge bg-label-secondary">{{ $user->department->name }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm btn-edit"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-role="{{ $user->role }}"
                                        data-active="{{ $user->is_active }}"
                                        data-department="{{ $user->department_id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    {{ __('menu.general.edit') }}
                                </button>
                                <form action="{{ route('user.destroy', $user) }}" class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-delete"
                                            type="button">{{ __('menu.general.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            {{ __('menu.general.empty') }}
                        </td>
                    </tr>
                    </tbody>
                @endif
                <tfoot class="table-border-bottom-0">
                <tr>
                    <th>{{ __('model.user.name') }}</th>
                    <th>{{ __('model.user.email') }}</th>
                    <th>{{ __('model.user.phone') }}</th>
                    <th>{{ __('model.user.role') }}</th>
                    <th>{{ __('model.user.is_active') }}</th>
                    <th>{{ __('model.department.name') }}</th>
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
            <form class="modal-content" method="post" action="{{ route('user.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalTitle">{{ __('menu.general.create') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <x-input-form name="name" :label="__('model.user.name')"/>
                    <x-input-form name="email" :label="__('model.user.email')" type="email"/>

                    <div class="mb-3">
                        <label class="form-label" for="role">{{ __('model.user.role') }}</label>
                        <select class="form-select" name="role" id="role" required>
                            <option value="" disabled selected>{{ __('user.select_role') }}</option>
                            <option value="admin">{{ __('model.user.admin') }}</option>
                            <option value="sekretaris">{{ __('model.user.sekretaris') }}</option>
                            <option value="staff">{{ __('model.user.staff') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="department">{{ __('model.department.name') }}</label>
                        <select class="form-select" name="department_id" id="department">
                            <option value="">-</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-input-form name="phone" :label="__('model.user.phone')"/>
                    <x-input-form name="password" :label="__('model.user.password')" type="password"/>
                    <x-input-form name="password_confirmation" :label="__('model.user.password_confirmation')" type="password"/>

                    <div class="form-text">
                        <i class="bx bx-info-circle me-1"></i>{{ __('user.password_rule') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
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
                    <h5 class="modal-title" id="editModalTitle">{{ __('menu.general.edit') }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id" value="">
                    <x-input-form name="name" id="edit-name" :label="__('model.user.name')"/>
                    <x-input-form name="email" id="edit-email" :label="__('model.user.email')" type="email"/>

                    <div class="mb-3">
                        <label class="form-label" for="edit-role">{{ __('model.user.role') }}</label>
                        <select class="form-select" name="role" id="edit-role" required>
                            <option value="admin">{{ __('model.user.admin') }}</option>
                            <option value="sekretaris">{{ __('model.user.sekretaris') }}</option>
                            <option value="staff">{{ __('model.user.staff') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="edit-department">{{ __('model.department.name') }}</label>
                        <select class="form-select" name="department_id" id="edit-department">
                            <option value="">-</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-input-form name="phone" id="edit-phone" :label="__('model.user.phone')"/>

                    <hr>

                    <div class="mb-2">
                        <span class="fw-semibold">{{ __('user.change_password_title') }}</span>
                        <div class="form-text mb-2">{{ __('user.change_password_hint') }}</div>
                    </div>
                    <x-input-form name="password" id="edit-password" :label="__('model.user.password')" type="password"/>
                    <x-input-form name="password_confirmation" id="edit-password_confirmation" :label="__('model.user.password_confirmation')" type="password"/>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="true" id="edit-is_active">
                        <label class="form-check-label" for="edit-is_active">{{ __('model.user.is_active') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('menu.general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('menu.general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection