<?php

namespace App\Http\Controllers\PosSecurity\Datatable\Security;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaDataSecurity;

class SecurityDatatable extends Controller
{
    public function index()
    {
        $query = GaDataSecurity::query()->orderBy('nama_security', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($item) {
                if (!$item->foto) {
                    return '-';
                }

                $url = asset($item->foto);

                // return '
                //     <button 
                //         type="button"
                //         class="btn btn-sm btn-primary preview-image"
                //         data-preview="' . $url . '">
                //         Lihat Foto
                //     </button>               
                // ';

                return '
                    <img src="' . $url . '" 
                        class="img-thumbnail preview-image" 
                        style="max-height:80px; cursor:pointer"
                        data-preview="' . $url . '"
                        alt="Foto Security">
                ';

            })
            ->addColumn('status', function ($item) {
                if ($item->status == 'active') {
                    return '<span class="badge bg-primary">Active</span>';
                }

                return '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function ($item) {

                if ($item->status === 'active') {
                    $label   = 'Nonaktifkan';
                    $icon    = 'mdi-close';
                    $class   = 'text-danger';
                } else {
                    $label   = 'Aktifkan';
                    $icon    = 'mdi-check';
                    $class   = 'text-success';
                }

                return '
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-soft-secondary btn-sm dropdown-toggle"
                                type="button" data-bs-toggle="dropdown">
                            <i class="ri-more-fill align-middle"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="#!" class="dropdown-item"
                                onclick="openEditSecurityModal(' . $item->id . ')">
                                    <i class="mdi mdi-tooltip-edit me-2 text-muted"></i>Edit
                                </a>
                            </li>
                            <li>
                                <a href="#!" class="dropdown-item ' . $class . '"
                                onclick="toggleSecurity(' . $item->id . ')">
                                    <i class="mdi ' . $icon . ' me-2"></i>' . $label . '
                                </a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['foto', 'action', 'status'])
            ->make(true);
    }
}
