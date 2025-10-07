<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
