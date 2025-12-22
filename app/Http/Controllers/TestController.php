<?php

namespace App\Http\Controllers;

use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function uploadImage()
    {
        return view('test.test-upload');
    }

    public function doUpload(Request $request)
    {
        $image = $request->file('image');
        $contents = $image->openFile()->fread($image->getSize());

        $user = DB::connection('192.168.154.44-admin')
            ->table('MSIDCARD')
            ->where('BARCODE', '040917-25749')
            ->where('RFID', '899976977')
            ->update([
                'FOTOBLOB' => $contents
            ]);

        return 'Upload image succeed';
    }

    public function sendEmail()
    {
        try {
            $recipients = [
                'damangmaulana.wirapraja@pt-bas-id.com',
                'aqmarinash@gmail.com',
            ];

            Mail::to($recipients)->send(new TestEmail());

            return response()->json([
                'success' => true,
                'message' => 'API Email test berhasil dikirim'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Send Email Failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
