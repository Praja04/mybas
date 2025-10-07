<?php

namespace App\Http\Controllers\HaloSecurity;

use App\HaloSecurity\TemplateBaiItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = TemplateBaiItems::paginate(10);

        return view('pages.halo-security.list-template', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.halo-security.create-template');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'pertanyaan_introgasi' => 'required',
            'jawaban_introgasi' => 'required',
        ],[
            'pertanyaan_introgasi.required' => 'Data Pertanyaan Introgasi wajib di isi',
            'jawaban_introgasi.required' => 'Data Jawaban Introgasi wajib di isi',
        ]);

        $template = new TemplateBaiItems;
        $template->pertanyaan_introgasi = $request->input('pertanyaan_introgasi');
        $template->jawaban_introgasi = $request->input('jawaban_introgasi');
        
        $template->save();

        return redirect()->route('template')->with(['success'=>'Data Template Tanya Jawab Introgasi berhasil ditambahkan']);
        
        // $template = new TemplateBaiItems();
        // $template->pertanyaan_introgasi = $request->pertanyaan_introgasi;
        // $template->jawaban_introgasi = $request->jawaban_introgasi;
        // $template->save();
        // return response()->json($template);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateBaiItems $template, $id)
    {
        $template = TemplateBaiItems::find($id);

        return view('pages.halo-security.edit-template', ['template' => $template]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('_method','_token','submit');
  
        $validator = Validator::make($request->all(), [
            'pertanyaan_introgasi' => 'required',
            'jawaban_introgasi' => 'required',
        ],[
            'pertanyaan_introgasi.required' => 'Data Pertanyaan Introgasi wajib di isi',
            'jawaban_introgasi.required' => 'Data Jawaban Introgasi wajib di isi',
        ]);
  
        if ($validator->fails()) {
           return redirect()->Back()->withInput()->withErrors($validator);
        }

        $template = TemplateBaiItems::find($id);
  
        if($template->update($data)){
            return redirect()->route('template')->with(['success'=>'Data Template Tanya Jawab Introgasi berhasil diubah']);
        }else{
            return redirect()->route('edit-template')->with(['success'=>'Data Template Tanya Jawab Introgasi gagal diubah']);
        }
  
        return Back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = TemplateBaiItems::findOrFail($id);
        $item->delete();

        return redirect()->route('template')->with(['success'=>'Data Template Tanya Jawab Introgasi berhasil dihapus']);
    }
}
