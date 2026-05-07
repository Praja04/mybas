<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="form-edit" action="{{ url('master/user/prosesUbah/') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="editUserId">

                    <!-- Baris 1: Username & Name sejajar -->
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="editUsername">Username <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editUsername" name="username" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="editName">Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 2: Email -->
                    <div class="form-group">
                        <label class="font-weight-bold" for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>

                    <!-- Baris 3: Password -->
                    <div class="form-group">
                        <label class="font-weight-bold" for="editPasword">New Password</label>
                        <div class="input-group">
                            <input name="edit_password" type="password" class="form-control" id="editPasword"
                                placeholder="Enter new password">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="toggleEditPasswordVisibility()">
                                    <i id="edit-password-eye-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                    </div>

                    <!-- Baris 4: Group & Department sejajar -->
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="editAuthGroupId">Group <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="editAuthGroupId" name="auth_group_id" required>
                                    <option value="">Select Group</option>
                                    @foreach ($authGroup as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="editDepartmentId">Department <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="editDepartmentId" name="department_id" required>
                                    <option value="">Select Department</option>
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
