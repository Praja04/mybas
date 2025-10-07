<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;

class AttachmentController extends Controller
{
    public function upload(Request $request)
    {
        // dd($request->all());
        $file = $request->file('file');
        $extension = $file->extension();
        $encode_file_name = $this->generateUniqName().'.'.$extension;
        $original_file_name = $file->getClientOriginalName();
        $transaction_type = $request->transaction_type;
        $is_draft = 'Y';
        // Save ke google drive
        Storage::disk('google')->putFileAs('',$file,$encode_file_name);

        // Insert ke database
        $attachment = new Attachment;
        $attachment->transaction_id = $request->transaction_id;
        $attachment->original_file_name = $original_file_name;
        $attachment->encode_file_name = $encode_file_name;
        $attachment->is_draft = 'Y';
        $attachment->transaction_type = $transaction_type;
        $attachment->save();

        return response()->json(['success' => 1, 'message' => 'Image uploaded succesfully']);
    }

    public function generateTransactionId()
    {
        return response()->json([
            'success' => 1,
            'message' => 'Transaction ID generated Successfully',
            'data' => $this->generateUniqName()
        ]);
    }

    public function download($id)
    {
        $attachment = Attachment::find($id);

        // Get seluruh konten dari drive
        $contents = collect(Storage::disk('google')->listContents('/', false));
        
        $file = $contents
        ->where('type', 'file')
        ->where('filename', pathinfo($attachment->encode_file_name, PATHINFO_FILENAME))
        ->where('extension', pathinfo($attachment->encode_file_name, PATHINFO_EXTENSION))
        ->first();

        $rawData = Storage::disk('google')->get($file['path']);

        return response($rawData, 200)
            ->header('ContentType', $file['mimetype'])
            ->header('Content-Disposition', "attachment; filename=$attachment->original_file_name");
    }
}
