<?php

namespace App\Http\Livewire;

use App\GaMonitoring\GaPetugas;
use Livewire\Component;
use Illuminate\Http\Request;

class Listgapetugas extends Component
{
    public function render(Request $request)
    {
        
        // $petugass = GaPetugas::query();

        // filter by search
        // $petugass->when($request->search, function ($query) use ($request) {
        //     return $query
        //     ->where('nik', 'like', '%'.$request->search.'%')
        //     ->orWhere('nama', 'like', '%'.$request->search.'%')
        //     ;
        // });

        // // filter by shift
        // $petugass->when($request->active, function ($query) use ($request) {
        //     return $query->whereActive($request->active);
        // });
        
        $petugass = GaPetugas::all();

        return view('livewire.listgapetugas', compact('petugass'));
    }
}
