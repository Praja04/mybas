<div class="bas-tab-card p-0">
    <div class="table-responsive p-6">
        <table class="table" id="table_permission">
            <thead>
                <tr>
                    <th style="min-width: 200px" class="text-left">Nama Permission</th>
                    <th class="text-center">Codename / Slug</th>
                    <th class="text-center">Tanggal Input</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($auth_permissions as $item)
                    <tr>
                        <td>
                            <div class="font-weight-bolder text-dark font-size-lg">{{ $item->name }}</div>
                        </td>
                        <td class="text-center">
                            <span class="label label-inline label-light-warning font-weight-bold py-4">
                                {{ $item->codename }}
                            </span>
                        </td>
                        <td class="text-center text-muted font-weight-bold">
                            {{ date('d/m/Y', strtotime($item->created_at)) }}
                        </td>
                        <td class="text-center">
                            @if (Auth::user()->auth_group_id == 1)
                                <button onclick="editPermission('{{ $item->id }}')"
                                    class="btn btn-icon btn-light-warning btn-sm mr-2" data-toggle="tooltip"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deletePermission('{{ $item->id }}')"
                                    class="btn btn-icon btn-light-danger btn-sm" data-toggle="tooltip" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
