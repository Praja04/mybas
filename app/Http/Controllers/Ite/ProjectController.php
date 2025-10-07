<?php

namespace App\Http\Controllers\Ite;
use App\Models\Ite\Project;
use App\Models\Ite\JenisProject;
use App\Models\Ite\ProjectJob;
use App\Models\Ite\Department;
use App\Models\Ite\Order;
use App\Models\Ite\OrderItem;
use App\Models\Ite\Material;
use App\Models\Ite\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $jenis_projects = JenisProject::all();
        $materials = Material::where('status', '1')->get();
        $projects = Project::orderBy('status', 'asc')->orderBy('priority', 'desc')->where('status', '!=', '5')->get();
        return view('ite.projects.project-list', [
            'projects' => $projects,
            'departments' => $departments,
            'jenis_projects' => $jenis_projects,
            'materials' => $materials
        ]);
    }

    public function change_status(Request $request)
    {
        $project = Project::find($request->id);
        $project->status = $request->status;
        if ($project->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 0], 200);
        }
    }

    public function project_schedule()
    {
        $schedules = Schedule::all();
        $planning = [];
        foreach ($schedules as $row) {
            $plan_end_date = new \DateTime($row->plan_end_date);
            $plan_end_date = $plan_end_date->modify('+1 day');
             $enddate = $row->plan_and_date. " 24:00:00";
             $planning[] = \Calendar::event(
                'Plan - '.$row->project->name.' | '.$row->name,
                true,
                new \DateTime($row->plan_start_date),
                $plan_end_date,
                $row->id,
                [
                    'color' => $row->color
                ]
             );
         } 
        $schedule_ = \Calendar::addEvents($planning)
        ->setCallbacks([ //set fullcalendar callback options (will not be JSON encoded)
            'eventRender' => 'function(event, element) {
                var tooltip = new Tooltip(element[0], {
                title: event.title,
                placement: "top",
                trigger: "hover",
                container: "body"
              });
        }']);
        $actual_end_date = new \DateTime($row->actual_end_date);
        $actual_end_date = $actual_end_date->modify('+1 day');
        $actual = [];
        foreach ($schedules as $row) {
            if ( $row->actual_start_date != null) {
                $actual[] = \Calendar::event(
                'Actual - '.$row->project->name.' | '.$row->name,
                true,
                new \DateTime($row->actual_start_date),
                $actual_end_date,
                $row->id,
                [
                    'color' => $row->color
                ]
             );
            }
         } 
        $schedule_ = \Calendar::addEvents($actual);
        $projects = Project::orderBy('priority', 'desc')->orderBy('status', 'desc')->where('status', '!=', '3')->get();
        return view('transaction.project-schedule', [
            'schedule_' => $schedule_,
            'projects' => $projects
        ]);
    }

    public function create_planning(Request $request)
    {
        $tanggal = explode('-', $request->tanggal);
        $plan_tanggal_start = date('Y-m-d', strtotime($tanggal[0]));
        $plan_tanggal_end = date('Y-m-d', strtotime($tanggal[1]));

        $schedule = new Schedule;
        $schedule->project_id = $request->id;
        $schedule->name = $request->planning;
        $schedule->color = $request->color;
        $schedule->plan_start_date = $plan_tanggal_start;
        $schedule->plan_end_date = $plan_tanggal_end;
        $schedule->status = 1;
        if ($schedule->save()) {
            return response()->json(['status' => 1], 200);
        }else{
            return response()->json(['status' => 0], 200);
        }

    }

    public function add_material(Request $request)
    {
        $order = new OrderItem;
        $order->material_id = $request->material;
        $order->order_id = $request->order_id;
        $order->quantity = $request->jumlah;
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 0], 200);
        }
    }

    public function material_arrive(Request $request)
    {
        $order = OrderItem::where('order_id', $request->order_id)->where('material_id', $request->item_id)->first();
        $order->arrive = 1;
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 0], 200);
        }
    }

    public function delete_order(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = '9';
        if ($order->update()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 0], 200);
        }
    }

    public function job_list()
    {
        $project_jobs = ProjectJob::all();
        $projects = Project::all();
        return view('transaction.project-job-list', [
            'project_jobs' => $project_jobs,
            'projects' => $projects
        ]);
    }

    public function get($_filter)
    {
        $_Projects = [];
        $_no = 1;
        $filter = explode(',', $_filter);
        $Projects = Project::whereIn('status', $filter)->orderBy('status')->get();
        foreach ($Projects as $Project) {
            if ($Project->order) {
                $material = $Project->order->status;
            } else {
                $material = '';
            }
            array_push($_Projects,[
                'edit' => false,
                'delete' => false,
                'no' => $_no,
                'id' => $Project->id,
                'start_date' => $Project->start_date,
                'target_date' => $Project->target_date,
                'material' => $material,
                'dept_id' => $Project->dept_id,
                'department' => $Project->department->name,
                'name' => $Project->name,
                'notes' => $Project->notes,
                'pic' => $Project->pic,
                'priority' => $Project->priority,
                'status' => $Project->status
            ]);
            $_no++;
        }
        return response()->json($_Projects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    function order_barang(Request $request)
    {
        $order = new Order;
        $order->project_id = $request->id;
        $order->status = '0';
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 2], 200);
        }
    }

    function pr_barang(Request $request)
    {
        $order = Order::find($request->id);
        $order->pr_number = $request->no_pr;
        $order->status = '1';
        $order->pr_date = date("Y-m-d");
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 2], 200);
        }
    }

    function po_barang(Request $request)
    {
        $order = Order::find($request->id);
        $order->po_number = $request->no_po;
        $order->status = '2';
        $order->po_date = date("Y-m-d");
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 2], 200);
        }
    }

    function datang_barang(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = '3';
        $order->arrive_date = date("Y-m-d");
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 2], 200);
        }
    }

    function reservasi_barang(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = '4';
        $order->reservation_date = date("Y-m-d");
        if ($order->save()) {
            return response()->json(['success' => 1], 200);
        }else{
            return response()->json(['success' => 2], 200);
        }
    }

    function get_order(Request $request)
    {
        $orders = Order::where('project_id', $request->id)->where('status','!=', 9)->get();
        $_material = '';
        foreach ($orders as $order) {
            $_material = $order->materials;
        }
        return response()->json(['success' => 1, 'orders' => $orders, 'materials' => $_material], 200);
    }

    public function add_note(Request $request, $id)
    {
        $Project = Project::find($id);
        $Project->notes = $request->notes;
        if ($Project->save()) {
            return response()->json(array('success' => 1), 200);
        }
    }
    public function store(Request $request)
    {
        $validation = $request->validate([
            'name'          => 'required',
            'jenis_project' => 'required',
            'start_date'    => 'required',
            'target_end'    => 'required',
            'department'    => 'required',
            'pic'           => 'required',
            'priority'      => 'required',
            'status'        => 'required'
        ]);
        // return $request->all();
        $Project = new Project;
        $Project->name = $request->name;
        $Project->jenis_project = $request->jenis_project;
        $Project->start_date = $request->start_date;
        $Project->target_date = $request->target_end;
        $Project->dept_id = $request->department;
        $Project->pic = $request->pic;
        $Project->priority = $request->priority;
        $Project->status = $request->status;
        if ($Project->save()) {
            return response()->json(['success' => 1], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $id;
    }

    public function view($id)
    {
        $ProjectJob = ProjectJob::find($id);
        return response()->json(array('data' => $ProjectJob), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $Project = Project::find($id);
        $Project->name = $request->name;
        $Project->start_date = $request->start_date;
        $Project->target_date = $request->target_date;
        $Project->dept_id = $request->department;
        $Project->pic = $request->pic;
        $Project->priority = $request->priority;
        $Project->status = $request->status;
        if ($Project->save()) {
            return response()->json(array('success' => 1), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Project::destroy($id)) {
            return response()->json(array('success' => 1), 200);
        }
    }
}
