<style>
    .user-perm-section-label {
        font-weight: 700;
        font-size: 12px;
        color: #F59E0B;
        margin: 12px 0 8px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .user-perm-divider {
        border-top: 1px dashed #E5E7EB;
        margin: 10px 0;
        padding: 4px 0;
        opacity: 0.7;
    }
    #modal-user-permission .modal-footer .btn-primary {
        background-color: #F59E0B !important;
        border-color: #F59E0B !important;
    }
    #modal-user-permission .modal-footer .btn-primary:hover {
        background-color: #D97706 !important;
        border-color: #D97706 !important;
    }
    #modal-user-permission .permission-row {
        padding: 8px 12px;
        border-radius: 6px;
        transition: background-color 0.15s ease;
    }
    #modal-user-permission .permission-row:hover {
        background-color: #FFFBEB;
    }
    #modal-user-permission .permission-row .text-muted {
        cursor: not-allowed;
    }
</style>

<div class="mb-3">
    <input type="text" id="searchUserPermission" class="form-control" placeholder="Cari permission...">
</div>

<div class="user-perm-modal" style="max-height: 400px; overflow-y: auto;">
    <ul class="user-auth-permissions list-unstyled mb-0">
        {{-- Section 1: Permissions dari group (read-only) --}}
        @if(count($group_permission_ids) > 0)
            <div class="user-perm-section-label">
                <i class="fas fa-shield-alt mr-1"></i> Dari Group (read-only)
            </div>
            @foreach($all_permissions as $permission)
                @if(in_array($permission->id, $group_permission_ids))
                    <li class="permission-row">
                        <label class="text-muted mb-0 d-flex align-items-center" style="cursor: not-allowed;">
                            <input class="mr-2" type="checkbox" checked disabled>
                            <span>
                                {{ $permission->codename }}
                                <small class="text-muted">(dari group)</small>
                            </span>
                        </label>
                    </li>
                @endif
            @endforeach
            <div class="user-perm-divider"></div>
        @endif

        {{-- Section 2: Permission Tambahan (checked, editable) --}}
        <div class="user-perm-section-label" style="color: #DC2626;">
            <i class="fas fa-plus-circle mr-1"></i> Permission Tambahan
        </div>
        @php
            $hasTambahan = false;
        @endphp
        @foreach($all_permissions as $permission)
            @if(!in_array($permission->id, $group_permission_ids) && in_array($permission->id, $direct_permission_ids))
                @php $hasTambahan = true; @endphp
                <li class="permission-row">
                    <label class="mb-0 d-flex align-items-center" style="cursor: pointer;">
                        <input class="mr-2" name="user_permissions[]" type="checkbox" value="{{ $permission->id }}" checked>
                        <span>{{ $permission->codename }}</span>
                    </label>
                </li>
            @endif
        @endforeach
        @if(!$hasTambahan)
            <li class="permission-row text-muted font-italic" style="font-size: 13px;">
                <em>Belum ada permission tambahan yang dipilih.</em>
            </li>
        @endif

        <div class="user-perm-divider"></div>

        {{-- Section 3: Yang belum dipilih (unchecked, editable) --}}
        <div class="user-perm-section-label" style="color: #6B7280;">
            <i class="fas fa-box-open mr-1"></i> Yang Belum Dipilih
        </div>
        @foreach($all_permissions as $permission)
            @if(!in_array($permission->id, $group_permission_ids) && !in_array($permission->id, $direct_permission_ids))
                <li class="permission-row">
                    <label class="mb-0 d-flex align-items-center" style="cursor: pointer;">
                        <input class="mr-2" name="user_permissions[]" type="checkbox" value="{{ $permission->id }}">
                        <span>{{ $permission->codename }}</span>
                    </label>
                </li>
            @endif
        @endforeach
    </ul>
</div>
