<?php

namespace App\Http\Controllers\MEN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MEN\MasterMaterial;
use App\Models\MEN\MaterialExclude;
use App\Imports\MENMasterMaterialImport;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\MaterialExpiredNotification;

use App\Models\MEN\MasterMaterialUpload;

class MasterMaterialController extends Controller
{
    public function index()
    {
        $master_materials = MasterMaterial::all(); 
        return view('men.master-material', [
            'master_materials' => $master_materials
        ]);
    }

    public function upload(Request $request)
    {
        // MasterMaterial::truncate();
        $excel = $request->file('file');

        // Get last master upload
        $last_master_upload = MasterMaterialUpload::orderBy('created_at', 'desc')->first();
        
        if($last_master_upload != null)
        {
            MasterMaterial::where('upload_id', $last_master_upload->id)
            ->update(['archive' => 'Y']);
        }

        // Create new master material upload
        $upload = new MasterMaterialUpload;
        $upload->upload_date = date('Y-m-d');
        $upload->upload_by = Auth::user()->name;
        $upload->save();

        $material_exclude = MaterialExclude::pluck('material')->toArray();

        Excel::import(new MENMasterMaterialImport($material_exclude, $upload->id), $excel, null, \Maatwebsite\Excel\Excel::CSV);

        $this->sendEmailExpired();
        
        return back()->with('status', 'Upload data succeed');
    }

    public function sendEmailExpired()
    {
        $emails = [];

        $group2_grouped_materials = [];
        $group3_grouped_materials = [];

        $group2_materials = collect([]);
        $group3_materials = collect([]);

        $master_notifs = $this->getNotifs();

        $receiver = DB::table('men_master_group')
        ->join('men_master_group_email', 'men_master_group_email.group_id' , 'men_master_group.id')
        ->get();

        foreach($master_notifs as $category)
        {
            foreach($category as $notif) 
            {
                foreach($notif->grouped_materials as $sloc => $grouped_materials)
                {
                    $this->sendEmail($grouped_materials, $notif->status, collect($notif->materials)->where('sloc', $sloc), $receiver->where('group_sloc', $sloc));
                }
            }

            foreach($category->where('status', 'warning2') as $notif)
            {
                foreach($notif->grouped_materials as $sloc => $grouped_materials)
                {
                    $group2_grouped_materials[] = $grouped_materials;
                }

                $group2_materials = $group2_materials->merge($notif->materials);
            }

            foreach($category->where('status', 'expired') as $notif)
            {
                foreach($notif->grouped_materials as $sloc => $grouped_materials)
                {
                    $group3_grouped_materials[] = $grouped_materials;
                }

                $group3_materials = $group3_materials->merge($notif->materials);
            }

        }

        // dd($group2_materials);
        // dd($group2_grouped_materials);

        if(count($group2_materials) > 0)
        {
            $group2_grouped_materials = collect($group2_grouped_materials)->map(function ($value, $key) {
                foreach($value as $v)
                {
                    return $v;
                }
            });

            $this->sendEmail($group2_grouped_materials, 'warning2', collect($group2_materials)->sortBy('sloc'), $receiver->where('group_number', '2'));
        }

        if(count($group3_materials) > 0)
        {
            $group3_grouped_materials = collect($group3_grouped_materials)->map(function ($value, $key) {
                foreach($value as $v)
                {
                    return $v;
                }
            });

            $this->sendEmail($group3_grouped_materials, 'expired', collect($group3_materials)->sortBy('sloc'), $receiver->where('group_number', '3'));
        }

        // dd($group3);
        
        // dd($emails);
    }

    private function sendEmail($grouped_materials, $status, $materials, $receiver)
    {
        if($status == 'standard')
            return false;

        $receiver_emails = $receiver->pluck('email');

        foreach($receiver_emails as $email)
        {
            Mail::to($email)->send(new MaterialExpiredNotification($grouped_materials, $status, $materials));
        }
    }

    public function all()
    {
        $master_materials = MasterMaterial::select('*');
        return Datatables::of($master_materials)->make(true);
    }

    public function getExpired()
    {
        $data = [];
        
        $master_notifs = $this->getNotifs();

        return response()->json(['success' => 1, 'data' => $master_notifs]);
    }

    private function getNotifs()
    {
        $master_notifs = DB::table('men_master_notif')
        ->get();

        $master_notifs = $master_notifs->groupBy('category');

        $master_notifs->map(function ($items, $key) {
            $items->map(function ($item, $key) {
                $item->materials = [];
            });
        });

        $materials = DB::table('men_master_material')
        ->join('men_master_material_type', 'men_master_material.material_type', 'men_master_material_type.type_code')
        ->where('men_master_material.archive', '!=', 'Y')
        ->orderBy('expired_date', 'desc')
        ->get();

        // Convert materials from database to collections
        $materials = collect($materials);

        $master_notifs->map( function ($items, $key) use ($materials) {

            $items->map(function ($notification, $key) use ($materials) {

                $shelf_life_min = $notification->shelf_life_min;
                $shelf_life_max = $notification->shelf_life_max;
                $days_min = $notification->days_min == 0 ? -99999 : $notification->days_min;
                $days_max = $notification->days_max;
                
                $_materials = $materials->where('shelf_life', '>=', $shelf_life_min)
                ->where('shelf_life', '<=', $shelf_life_max)
                ->all();

                foreach($_materials as $material)
                {
                    if($this->checkExpired($material->expired_date) >= $days_min && $this->checkExpired($material->expired_date) <= $days_max)
                    {
                        $material->due_date = $this->checkExpired($material->expired_date);
                        $notification->materials[] = $material;
                    }
                }
                
                $sloc_grouped_materials = collect($notification->materials)->groupBy('sloc');
                $material_type_grouped_materials = $sloc_grouped_materials->map(function($materials, $key) {
                    $grouped_materials = collect($materials)->groupBy('type_name');
                    return $grouped_materials->map(function ($material, $key) {
                        return $material->groupBy('uom')->map( function ($_material, $_key) use ($key) {
                            return [
                                'type' => $key,
                                'sloc' => $_material[0]->sloc,
                                'uom' => $_material[0]->uom,
                                'sum' => $_material->sum('qty')
                            ];
                        });
                    });
                });

                $notification->grouped_materials = $material_type_grouped_materials;
            });
            
        });

        return $master_notifs;
    }

    private function checkExpired($expired_date) {
        return (strtotime(date('Y-m-d')) - strtotime($expired_date)) / 86400;
    }
}
