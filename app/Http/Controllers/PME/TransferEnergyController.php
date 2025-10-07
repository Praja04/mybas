<?php

namespace App\Http\Controllers\PME;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PME\Sloc;
use App\Models\PME\TransferEnergy;
use App\Models\PME\TransferEnergyTrash;

class TransferEnergyController extends Controller
{
    public function index()
    {
        $slocs = Sloc::where('status', '1')->get();

        $where = [];

        if(isset($_GET['filter_date']) && $_GET['filter_date'] != '')
            $where[] = ['date', explode('/', $_GET['filter_date'])[2].'-'.explode('/', $_GET['filter_date'])[1].'-'.explode('/', $_GET['filter_date'])[0]];

        if(isset($_GET['filter_source_sloc']) && $_GET['filter_source_sloc'] != '')
            $where[] = ['source_sloc', $_GET['filter_source_sloc']];

        if(isset($_GET['filter_destination_sloc']) && $_GET['filter_destination_sloc'] != '')
            $where[] = ['destination_sloc', $_GET['filter_destination_sloc']];

        $transfers = TransferEnergy::where($where)->get();
        return view('pme.transfer-energy', ['slocs' => $slocs, 'transfers' => $transfers]);
    }

    public function store(Request $request)
    {
        $transfer_energy = new TransferEnergy;
        $transfer_energy->date = explode('/', $request->date)[2].'-'.explode('/', $request->date)[1].'-'.explode('/', $request->date)[0];
        $transfer_energy->action = $request->action;
        $transfer_energy->source_sloc = explode('-', $request->source_sloc)[0];
        $transfer_energy->destination_sloc = explode('-', $request->destination_sloc)[0];
        $transfer_energy->source_quantity_id = explode('-', $request->source_sloc)[1];
        $transfer_energy->destination_quantity_id = explode('-', $request->destination_sloc)[1];
        $transfer_energy->jenis_energy = $request->jenis_energy;
        $transfer_energy->kwh = $request->kwh;
        $transfer_energy->description = $request->description;
        $transfer_energy->created_by = auth()->user()->username;
        $transfer_energy->save();

        return response()->json(['success' => 1, 'message' => 'Transfer energy store succeed']);
    }

    public function delete($id)
    {
        $transfer = TransferEnergy::find($id);

        $trash = new TransferEnergyTrash;
        $trash->date = $transfer->date;
        $trash->action = $transfer->action;
        $trash->source_sloc = $transfer->source_sloc;
        $trash->destination_sloc = $transfer->destination_sloc;
        $trash->source_quantity_id = $transfer->source_quantity_id;
        $trash->destination_quantity_id = $transfer->destination_quantity_id;
        $trash->jenis_energy = $transfer->jenis_energy;
        $trash->kwh = $transfer->kwh;
        $trash->description = $transfer->description;
        $trash->created_by = $transfer->created_by;
        $trash->deleted_by = auth()->user()->username;
        $trash->save();

        $transfer->delete();

        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

}
