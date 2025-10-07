<?php

namespace App\Http\Controllers\PME;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PME\Plant;
use App\Models\PME\MonthlyBill;
use App\Models\PME\MonthlyBillTrash;

class MonthlyBillController extends Controller
{
    public function index()
    {
        $plants = Plant::where('status', '1')->get();
        
        $where = [];

        if(isset($_GET['filter_plant']) && $_GET['filter_plant'] != '')
            $where[] = ['plant', $_GET['filter_plant']];

        if(isset($_GET['filter_month']) && $_GET['filter_month'] != '') {
            $where[] = ['year', explode('/', $_GET['filter_month'])[1]];
            $where[] = ['month', explode('/', $_GET['filter_month'])[0]];
        }

        $bills = MonthlyBill::where($where)->get();
        return view('pme.monthly-bill', ['plants' => $plants, 'bills' => $bills]);
    }

    public function store(Request $request)
    {
        // Check if data alredy exist with same month
        $check_bill = MonthlyBill::where(['plant' => $request->plant, 'month' => explode('/', $request->month)[0], 'year' => explode('/', $request->month)[1]])->first();

        if( $check_bill !== null ) return response()->json(['success' => 0, 'message' =>'Cannot store this monthly bill, the bill already exist']);

        $monthly_bill = new MonthlyBill;
        $monthly_bill->plant = $request->plant;
        $monthly_bill->year = explode('/', $request->month)[1];
        $monthly_bill->month = explode('/', $request->month)[0];
        $monthly_bill->kwh = $request->kwh;
        $monthly_bill->created_by = auth()->user()->username;
        $monthly_bill->save();

        return response()->json(['success' => 1, 'message' => 'Monthly bill store succeed']);
    }

    public function delete($id)
    {
        $bill = MonthlyBill::find($id);

        $trash = new MonthlyBillTrash;
        $trash->plant = $bill->plant;
        $trash->year = $bill->year;
        $trash->month = $bill->month;
        $trash->kwh = $bill->kwh;
        $trash->created_by = $bill->created_by;
        $trash->deleted_by = auth()->user()->username;
        $trash->save();

        $bill->delete();

        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }

    public function get($id)
    {
        $bill = MonthlyBill::find($id);

        return response()->json(['success' => 1, 'data' => $bill, 'message' => 'Get bill data succeed']);
    }

    public function update(Request $request)
    {
        $bill = MonthlyBill::find($request->id);
        $bill->plant = $request->plant;
        $bill->year = explode('/', $request->month)[1];
        $bill->month = explode('/', $request->month)[0];
        $bill->kwh = $request->kwh;
        $bill->save();

        return response()->json(['success' => 1, 'message' => 'Update bill data succeed']);
    }

}
