<?php

namespace App\Http\Controllers\Ite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ite\OrderItem;

class OrderItemController extends Controller
{
    public function get($id)
    {
        $item = OrderItem::find($id);
        $item->material;
        return response()->json([
            'success' => 1,
            'data' => $item,
            'message' => 'Get item succeed'
        ]);
    }

    public function submitPR(Request $request)
    {
        $item = OrderItem::find($request->id);
        $item->pr = '1';
        $item->pr_date = $request->pr_date;
        $item->pr_number = $request->pr_number;
        // $item->pr_keterangan = $request->pr_keterangan;
        $item->save();

        return response()->json([
            'success' => 1,
            'message' => 'Submit pr succeed'
        ]);
    }

    public function submitPO(Request $request)
    {
        $item = OrderItem::find($request->id);
        $item->po = '1';
        $item->po_date = $request->po_date;
        $item->po_number = $request->po_number;
        // $item->po_keterangan = $request->po_keterangan;
        $item->save();

        return response()->json([
            'success' => 1,
            'message' => 'Submit po succeed'
        ]);
    }

    public function submitArrive(Request $request)
    {
        $item = OrderItem::find($request->id);
        $item->arrive = '1';
        $item->arrive_date = $request->arrive_date;
        // $item->arrive_keterangan = $request->arrive_keterangan;
        $item->save();

        return response()->json([
            'success' => 1,
            'message' => 'Submit arrive succeed'
        ]);
    }

    public function submitDiambil(Request $request)
    {
        $item = OrderItem::find($request->id);
        $item->diambil = '1';
        $item->diambil_date = $request->diambil_date;
        // $item->diambil_keterangan = $request->diambil_keterangan;
        $item->save();

        return response()->json([
            'success' => 1,
            'message' => 'Submit diambil succeed'
        ]);
    }
}
