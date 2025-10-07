<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LocalAttachment;
use Intervention\Image\Facades\Image;
use Session;

class LocalAttachmentController extends Controller
{
    public function upload(Request $request)
    {
        // dd($request->all());
        $file = $request->file('file');
        $extension = $file->extension();
        $encode_file_name = $this->generateUniqName() . '.' . $extension;
        $original_file_name = $file->getClientOriginalName();
        $transaction_type = $request->transaction_type;
        $is_draft = 'Y';

        // Image::make($file)->resize(500, 500, function ($constraint) {
        // $constraint->aspectRatio();
        // })->save(storage_path('app/public/' . $transaction_type . '/') . $encode_file_name);

        // Save ke local
        Storage::disk('public')->putFileAs($transaction_type, $file, $encode_file_name);

        // Insert ke database
        $attachment = new LocalAttachment;
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
        $attachment = LocalAttachment::find($id);
        // Ambil path dari penyimpanan lokal berdasarkan transaction_type dan encode_file_name
        $filePath = storage_path('app/public/' . $attachment->transaction_type . '/' . $attachment->encode_file_name);

        if (file_exists($filePath)) {
            $typefile = mime_content_type($filePath);
            // Mendapatkan nama file asli
            $originalFileName = $attachment->original_file_name;

            // Mengirimkan file sebagai respons
            return response()->file($filePath, [
                'Content-Type' => $typefile,
                'Content-Disposition' => 'attachment; filename="' . $originalFileName . '"'
            ]);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}
