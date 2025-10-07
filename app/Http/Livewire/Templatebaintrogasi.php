<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HaloSecurity\TemplateBaiItems;
use Livewire\Component;

class Templatebaintrogasi extends Component
{
    public function fetchtemplate()
    {
        $templates = TemplateBaiItems::all();
        return response()->json([
            'templates'=>$templates,
        ]);
    }

    public function AddTemplateBaIntrogasi(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'pertanyaan_introgasi'=>'required',
            'jawaban_introgasi'=>'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $template = new TemplateBaiItems;
            $template->pertanyaan_introgasi = $request->input('pertanyaan_introgasi');
            $template->jawaban_introgasi = $request->input('jawaban_introgasi');
            $template->save();
            return response()->json([
                'status'=>200,
                'message'=>'Data Template Tanya Jawab Introgasi berhasil ditambahkan',
            ]);
        }
    }

    // public function edittemplate($id)
    // {
    //     $template = TemplateBaiItems::find($id);

    //     if($template)
    //     {
    //         return response()->json([
    //             'status'=>200,
    //             'template'=>$template,
    //         ]);
    //     }
    //     else
    //     {
    //         return response()->json([
    //             'status'=>404,
    //             'message'=>'Data Template Tanya Jawab Introgasi tidak ditemukan',
    //         ]);
    //     }
    // }

    public function render()
    {
        return view('livewire.templatebaintrogasi');
    }
}
