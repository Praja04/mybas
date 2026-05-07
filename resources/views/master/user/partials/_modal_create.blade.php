{{-- Modal Create User --}}
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="createUserModalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <!-- Form membungkus body dan footer -->
            <form id="form-create" action="{{ route('master.user.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="username">Username <span
                                        class="text-danger">*</span></label>
                                <input name="username" type="text" required class="form-control" id="username"
                                    placeholder="Enter username">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="name">Name <span
                                        class="text-danger">*</span></label>
                                <input name="name" type="text" required class="form-control" id="name"
                                    placeholder="Enter name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" for="email">Email</label>
                        <input name="email" type="email" class="form-control" id="email"
                            placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" for="password">Password</label>
                        <div class="input-group">
                            <input name="password" type="password" class="form-control" id="password"
                                placeholder="Enter password">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePasswordVisibility()">
                                    <i id="password-eye-icon" class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="auth_group_id">Auth Group <span
                                        class="text-danger">*</span></label>
                                <select required name="auth_group_id" id="auth_group_id" class="form-control">
                                    <option value="">-- Select Auth Group --</option>
                                    @foreach ($authGroup as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="department_id">Department <span
                                        class="text-danger">*</span></label>
                                <select required name="department_id" id="department_id" class="form-control">
                                    <option value="">-- Select Department --</option>
                                    @foreach ($department as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
