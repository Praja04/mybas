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

        return response()->json([
            'success' => 1,
            'message' => 'Image uploaded successfully',
            'data' => [
                'id' => $attachment->id,
                'original_file_name' => $original_file_name,
                'encode_file_name' => $encode_file_name
            ]
        ]);
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

        if (!$attachment) {
            return response("File tidak ditemukan di database.", 404);
        }

        // Cek secara lokal di storage/app/public karena public/storage symlink mungkin tidak ada
        $filePath = storage_path(
            'app/public/' .
                $attachment->transaction_type . '/' .
                $attachment->encode_file_name
        );

        if (file_exists($filePath)) {
            $typefile = mime_content_type($filePath);
            $fileName = $attachment->encode_file_name;

            return response()->file($filePath, [
                'Content-Type' => $typefile,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        }

        // Jika file tidak ditemukan di server lokal, coba ambil dari server 172.21.5.105 (Proxy)
        $fileName = $attachment->encode_file_name;
        $fallbackUrls = [
            'http://172.21.5.105/storage/' . $attachment->transaction_type . '/' . $fileName,
            'http://172.21.5.105/attachment/download/' . $id
        ];

        foreach ($fallbackUrls as $url) {
            try {
                // Gunakan tanpa redirecting agar tidak mendownload halaman login secara tidak sengaja
                $response = \Illuminate\Support\Facades\Http::timeout(10)
                    ->withoutRedirecting()
                    ->get($url);

                if ($response->successful()) {
                    $contentType = $response->header('Content-Type') ?: 'application/octet-stream';
                    $body = $response->body();

                    // Cek apakah response berupa halaman HTML (misalnya login page)
                    if (strpos($contentType, 'text/html') !== false ||
                        stripos($body, '<!DOCTYPE html>') !== false ||
                        stripos($body, '<html') !== false) {
                        continue;
                    }

                    return response($body, 200, [
                        'Content-Type' => $contentType,
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal fetch dari fallback URL ({$url}): " . $e->getMessage());
            }
        }

        return response("File tidak ditemukan di server.", 404);
    }


    public function delete($id)
    {
        $attachment = LocalAttachment::find($id);

        if (!$attachment) {
            return response()->json([
                'success' => 0,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $filePath = $attachment->transaction_type . '/' . $attachment->encode_file_name;

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $attachment->delete();

        return response()->json([
            'success' => 1,
            'message' => 'File berhasil dihapus'
        ]);
    }

    public function deleteAll($transactionId)
    {
        $attachments = LocalAttachment::where('transaction_id', $transactionId)->get();

        if ($attachments->isEmpty()) {
            return response()->json([
                'success' => 0,
                'message' => 'Tidak ada file untuk dihapus'
            ], 404);
        }

        $deleted = 0;
        $failed = 0;

        foreach ($attachments as $attachment) {
            $filePath = $attachment->transaction_type . '/' . $attachment->encode_file_name;

            try {
                if (!Storage::disk('public')->exists($filePath)) {
                    $failed++;
                    continue;
                }

                if (!Storage::disk('public')->delete($filePath)) {
                    $failed++;
                    continue;
                }
                $attachment->delete();
                $deleted++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        return response()->json([
            'success' => $failed === 0 ? 1 : 0,
            'message' => "Hapus file selesai",
            'deleted' => $deleted,
            'failed' => $failed
        ]);
    }
}
