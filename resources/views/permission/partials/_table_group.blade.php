@push('styles')
    <style>
        /* 1. Gabungkan styling Popover agar konsisten dengan Tooltip lu */
        .popover {
            border: none;
            border-radius: var(--bas-radius-md);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            max-width: 280px;
        }

        .popover-header {
            background-color: var(--bas-dark);
            color: white;
            border-bottom: none;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            border-radius: var(--bas-radius-md) var(--bas-radius-md) 0 0;
        }

        .popover-body {
            padding: 12px;
            color: var(--bas-dark);
        }

        /* 2. Style khusus untuk area scroll di dalam popover */
        .scrollable-popover {
            max-height: 150px;
            overflow-y: auto;
            font-size: 10px;
            line-height: 1.6;
            font-weight: 600;
            color: #4B5563;
            /* Gray-600 */
        }

        /* Biar scrollbar di popover juga estetik */
        .scrollable-popover::-webkit-scrollbar {
            width: 4px;
        }

        .scrollable-popover::-webkit-scrollbar-thumb {
            background: var(--bas-border);
            border-radius: 10px;
        }

        /* 3. Tambahan halus untuk baris tabel yang dipilih */
        /* .permission-item.selected::before {
            content: '✓';
            position: absolute;
            top: 5px;
            right: 8px;
            font-size: 10px;
            color: var(--bas-primary);
            font-weight: bold;
        } */
    </style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card card-custom shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center table-hover mb-0" id="table_group"
                        style="width: 100%;">
                        <thead>
                            <tr class="text-uppercase">
                                <th width="80px" class="text-center pl-7">NO</th>
                                <th>NAMA GROUP & PERMISSIONS</th>
                                <th width="200px" class="text-center pr-7">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auth_groups as $key => $group)
                                <tr>
                                    <td class="text-center pl-7">
                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                            {{ $key + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="javascript:void(0)"
                                                class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg mb-1"
                                                onclick="groupPermissions({{ $group->id }})">
                                                {{ $group->name }}
                                            </a>

                                            <div class="d-flex flex-wrap">
                                                @foreach ($group->permissions->take(3) as $perm)
                                                    <span
                                                        class="label label-light-warning label-inline font-weight-bold mr-1 mb-1"
                                                        style="font-size: 10px;">
                                                        {{ strtoupper($perm->codename) }}
                                                    </span>
                                                @endforeach

                                                @if ($group->permissions->count() > 3)
                                                    <span
                                                        class="label label-light-info label-inline font-weight-bold mb-1"
                                                        data-toggle="popover" data-html="true"
                                                        data-content="<div class='scrollable-popover'>{{ $group->permissions->slice(3)->pluck('codename')->implode('<br>') }}</div>"
                                                        title="Izin Lainnya" style="cursor: pointer;">
                                                        +{{ $group->permissions->count() - 3 }} Lainnya
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center pr-7">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-sm btn-light-primary btn-icon mr-2"
                                                onclick="editGroup('{{ $group->id }}', '{{ $group->name }}')"
                                                title="Ubah Nama">
                                                <i class="flaticon2-edit"></i>
                                            </button>

                                            <button type="button" class="btn btn-sm btn-light-danger btn-icon"
                                                onclick="deleteGroup('{{ $group->id }}')" title="Hapus Group">
                                                <i class="flaticon2-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
