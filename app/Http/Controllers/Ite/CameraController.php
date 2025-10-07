<?php

namespace App\Http\Controllers\Ite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Image;
use File;
use Carbon\Carbon;
use App\Models\Ite\Nvr;
use App\Models\Ite\Camera;
use App\Models\Ite\CameraTrash;
use App\Models\Ite\Department;
use App\Models\Ite\Check;
use App\Models\Ite\CheckCamera;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CameraImport;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;


class CameraController extends Controller
{
    protected $client;
    protected $timeout;
    protected $allowRedirects = true;

    public function finishCheck(Request $request)
    {
        $check = Check::find($request->check_id);
        $check->total_count = $request->total_count;
        $check->offline_count = $request->offline_count;
        if ($check->save()) {
            return response()->json(['success' => 1, 'message' => 'Camera check done']); 
        }else{
            return response()->json(['success' => 0, 'message' => 'Camera check done, but with some error']);
        }
    }
    public function createCheck(Request $request)
    {
        $check = new Check;
        $check->check_time = date('Y-m-d H:i:s');
        $check->check_by = Auth::user()->name;
        if ($check->save()) {
            return response()->json(['success' => 1, 'check_id' => $check->id]);
        }else{
            return response()->json(['success' => 0]);
        }
    }

    public function check($url,$timeout)
    {
        $this->timeout = $timeout;
        $this->client = new Client([
            'timeout' => $this->timeout,
            'allow_redirects' => $this->allowRedirects
        ]);

        try {
            $response = $this->client->get($url);
            $this->responseCode = $response->getStatusCode();
        } catch(ClientException $e) {
            $response = $e->getResponse();
            $this->responseCode = $response->getStatusCode();
        } catch(ConnectException $e) {
            $this->responseCode = $e->getCode();
        } catch(ServerException $e) {
            $this->responseCode = $e->getCode();
        }

        return $this->responseCode;

    }
    public function offline_check(Request $request)
    {

        $health = $this->check($request->ip_address, 1);
        // dd($health);
        if($health != 200) {
            // Cek ulang
            $second_check = $this->check($request->ip_address, 5);
            if ($second_check != 200) {
                $status = 'offline';
                // Save to server
                $check_camera = new CheckCamera;
                $check_camera->check_id = $request->check_id;
                $check_camera->camera_id = $request->camera_id;
                $check_camera->save();
            }else{
                $status = 'online';
            }
        }else{
            $status = 'online';
        }

        $camera = Camera::where('ip_address', $request->ip_address)
                        ->update(['current_condition' => $status]);
        $camera = Camera::where('ip_address', $request->ip_address)->first();
        return response()->json(['success' => 1, 'status' => $status, 'skip_check' => $camera->skip_check]);
    }

    public function dailyOfflineCheck()
    {
        $offline_count = 0;
        // Buat pengecekan baru
        $check = new Check;
        $check->check_time = date('Y-m-d H:i:s');
        $check->check_by = 'system';

        $cameras = Camera::all();
        // Updaing total camera check count
        $check->total_count = count($cameras);
        $check->save();

        foreach ($cameras as $camera) {
            $health = $this->check($camera->ip_address, 1);
            // dd($health);
            if($health != 200) {
                // Cek ulang
                $second_check = $this->check($camera->ip_address, 5);
                if ($second_check != 200) {
                    $status = 'offline';
                    // Save to server
                    $check_camera = new CheckCamera;
                    $check_camera->check_id = $check->id;
                    $check_camera->camera_id = $camera->id;
                    $check_camera->save();
                    if($camera->skip_check == 'N')
                    {
                        $offline_count = $offline_count+1;
                    }
                }else{
                    $status = 'online';
                }
            }else{
                $status = 'online';
            }
            $update_camera = Camera::where('ip_address', $camera->ip_address)
                        ->update(['current_condition' => $status]);
        }
        $check->offline_count = $offline_count;
        $check->save();
    }
    public function get_all_ip()
    {
        $camera_ip = [];
        $camera_id = [];
        $cameras = Camera::all();
        foreach ($cameras as $camera) {
            $camera_ip[] = $camera->ip_address;
            $camera_id[] = $camera->id;
        }
        return response()->json([
            'success' => 1,
            'camera_ip' => $camera_ip,
            'camera_id' => $camera_id
        ]);
    }

    public function index()
    {
    	$nvrs = [];
        $nvr_only = Nvr::all();
    	$nvr = Nvr::with(['cameras' => function ($q) {
    		$q->orderBy('channel_number', 'asc');
    	}])->orderBy('ip_address', 'asc')->get();
        $departments = Department::where('status', 1)->orderBy('name', 'asc')->get();
        $check = Check::orderBy('check_time','desc')->first();

        // Untuk mengisi chart jumlah kamera per department
        $chart_department_name = [];
        $chart_department_value = [];

        foreach ($departments as $department) {
            if (count($department->cameras) != 0) {
                $chart_department_name[] = $department->name;
                $chart_department_value[] = count($department->cameras);   
            }
         }

         // dd($chart_department_value);

        if ($check != null) {
            $offline_count = $check->offline_count;
            $total_count = $check->total_count;
            $check_id = $check->id;
        }else{
            $offline_count = 0;
            $total_count = 0;
            $check_id = '';
        }
    	foreach ($nvr as $data) {
    		$_cameras = [];
    		$channel_number = 1;
    		foreach ($data->cameras as $key => $camera) {
    			$_camera = [];
    			if ($channel_number != $camera->channel_number) {
    				// $i = ;
    				while ($channel_number < $camera->channel_number) {
    					$_camera['id'] = '0';
    					$_camera['image'] = '';
    					$_camera['channel_number'] = $channel_number;
		    			$_camera['ip_address'] =  '';
		    			$_camera['name'] =  '';
		    			$_camera['department'] =  '';
		    			$_camera['merk'] =  '';
		    			$_camera['type'] =  '';
		    			$_camera['model'] =  '';
                        $_camera['current_condition'] =  '';
		    			$_cameras[] = $_camera;
		    			$_camera = [];
		    			$channel_number++;
    				}
    			}
    			$_camera['id'] = $camera->id;
    			$_camera['image'] = $camera->image;
    			$_camera['channel_number'] = $camera->channel_number;
    			$_camera['ip_address'] =  $camera->ip_address;
    			$_camera['name'] =  $camera->name;
    			$_camera['department'] =  $camera->department != null ? $camera->department->name : '';
    			$_camera['merk'] =  $camera->merk;
    			$_camera['type'] =  $camera->type;
    			$_camera['image'] =  $camera->image;
    			$_camera['model'] =  $camera->model;
                $_camera['current_condition'] =  $camera->current_condition;
    			$_cameras[] = $_camera;
    			// Jika sudah di penghujung jumlah kamera di NVR ini
    			if ($key+1 == count($data->cameras)) {
    				if ($channel_number != $data->jumlah_channel) {
    					while ($channel_number < $data->jumlah_channel) {
			     			$_camera = [];
			     			$_camera['id'] = '0';
			     			$_camera['image'] = '';
			     			$_camera['channel_number'] = $channel_number+1;
			    			$_camera['ip_address'] =  '';
			    			$_camera['name'] =  '';
			    			$_camera['department'] =  '';
			    			$_camera['merk'] =  '';
			    			$_camera['type'] =  '';
			    			$_camera['model'] =  '';
                            $_camera['current_condition'] =  '';
			    			$_cameras[] = $_camera;
			    			$channel_number++;
			    		}
		     		}
    			}
    			$channel_number++;
     		}
            $nvrs[$data->ip_address]['ip_address'] = $data->ip_address;
            $nvrs[$data->ip_address]['id'] = $data->id;
    		$nvrs[$data->ip_address]['cameras'] = $_cameras; 
    	}

        // Untuk chart 
        $offline_department_label = [];
        $offline_department_count = [];

        $_offline_department_count = DB::connection('ite')->table('cameras')
                    ->select('departments.name as dept_name', DB::raw('count(*) as count'))
                    ->join('departments', 'cameras.dept_id', '=', 'departments.id')
                    ->where('current_condition', 'offline')
                    ->groupBy('dept_id')
                    ->get();

        foreach ($_offline_department_count as $count) {
            $offline_department_label[] = $count->dept_name;
            $offline_department_count[] = $count->count;
        }

        // dd(json_encode( $offline_department_count ));

    	return view('ite.manage.cameras', [
            'nvrs'                      => $nvrs,
            'nvr_only'                  => $nvr_only,
            'departments'               => $departments,
            'offline_count'             => $offline_count,
            'total_count'               => $total_count,
            'check_id'                  => $check_id,
            'chart_department_name'     => json_encode($chart_department_name),
            'chart_department_value'    => json_encode($chart_department_value),
            'offline_department_label'  => json_encode(str_replace("\r\n", "", $offline_department_label)),
            'offline_department_count'  => json_encode($offline_department_count),
        ]);
    }

    public function offlineCameras($check_id)
    {
        $cameras = [];
        $camera_skip = [];
        $no = 1;
        $_cameras = Check::find($check_id)->cameras()->orderBy('dept_id')->get();
        foreach ($_cameras as $camera) {
            // dd($check);
            $camera->department;
            if($camera->skip_check == 'N') {
                // Jika tidak di skip untuk di cek
                $cameras[] = $camera;
            }else{
                // Jika di skip untuk di cek
                $camera_skip[] = $camera;
            }
        }

        $offline_department = [];

        $offline_department_count = DB::connection('ite')->table('cameras')
                    ->select('departments.name as dept_name', DB::raw('count(*) as count'))
                    ->join('departments', 'cameras.dept_id', '=', 'departments.id')
                    ->where('current_condition', 'offline')
                    ->groupBy('dept_id')
                    ->get();

        foreach ($offline_department_count as $count) {
            $offline_department[] = [
                'x' => $count->dept_name,
                'y' => $count->count
            ];
        }

        $check_time = [];
        $offline_count = [];
        $check_data = [];

        // Get 20 pengecekan terakhir
        $check = Check::orderBy('check_time', 'desc')->take(10)->get();

        foreach ($check as $data) {
            $check_time[] = $data->check_time;
            $offline_count[] = $data->offline_count;
            $check_data[] = [
                'x' => $data->check_time,
                'y' => $data->offline_count
            ];
        }

        return response()->json([
            'success' => 1,
            'cameras' => $cameras,
            'camera_skip' => $camera_skip,
            'check_time' => $check_time,
            'offline_count' => $offline_count,
            'check_data' => array_reverse($check_data),
            'offline_department' => $offline_department
        ]);
    }

    public function change_image(Request $request)
    {
    	$path = storage_path('app/public/camera_images');
    	$dimensions = ['245', '300', '500'];
    	$validation = $request->validate([
    		'image' => 'required|image|mimes:jpg,png,jpeg',
    		'camera_id' => 'required'
    	]);

    	if(!File::isDirectory($path))
    	{
    		File::makeDirectory($path);
    	}

    	$image = $request->file('image');

    	$image_name = Carbon::now()->timestamp.'-'.uniqid().'.'.$image->getClientOriginalExtension();

    	// Image::make($image)->save($path.'/'.$image_name);

    	$image->move($path,$image_name);

    	// Ini untuk resize
    	// foreach ($dimensions as $row) {
    	// 	$canvas = Image::canvas($row,$row);

    	// 	$resize_image = Image::make($image)->resize($row,$row, function ($constraint) {
    	// 		$constraint->aspectRatio();
    	// 	});

    	// 	if(!File::isDirectory($path.'/'.$row)) {
    	// 		File::makeDirectory($path.'/'.$row);
    	// 	}

    	// 	$canvas->insert($resize_image, 'center');

    	// 	$canvas->save($path.'/'.$row.'/'.$image_name);
    	// }

    	$camera = Camera::find($request->camera_id);
    	$camera->image = $image_name;
    	$camera->save();


    	return response()->json(['success' => 1,'image_name' => $image_name ,'message' => 'Image Changed']);
    }

    public function view($camera_id)
    {
        $camera = Camera::find($camera_id);
        return view('manage.camera-view', [
            'ip_address' => $camera->ip_address,
            'username' => $camera->username,
            'password' => $camera->password
        ]);
    }

    public function import_excel(Request $request)
    {
        $validation = $request->validate([
            'excel'
        ]);
        $excel = $request->file('excel');
        Excel::import(new CameraImport, $excel);
        return response()->json(['success' => 1]);
    }

    public function getCamera($camera_id, $nvr, $channel_number)
    {
        if($camera_id == 0)
        {
            $camera['id'] = '0';
            $camera['image'] = '';
            $camera['channel_number'] = $channel_number;
            $camera['ip_address'] =  '';
            $camera['nvr_id'] =  $nvr;
            $camera['name'] =  '';
            $camera['department'] =  '';
            $camera['username'] = '';
            $camera['password'] = '';
            $camera['dept_id'] = '';
            $camera['skip_check'] = 'N';
            $camera['merk'] =  '';
            $camera['type'] =  '';
            $camera['model'] =  '';
            $camera['current_condition'] =  '';
        }else{
            $camera = Camera::find($camera_id);
        }
        return response()->json(['camera' => $camera]);
    }

    public function changeCamera(Request $request)
    {
        // dd($request->all());
        if($request->id == 0)
        {
            // Jika ini Jika id nya 0, maka ini adalah pembuatan baru
            $camera = new Camera;
        }else{
            // Jika id sudah lebih dari 0, maka ini adalah edit
            $camera = Camera::find($request->id);
        }
        $camera->ip_address = $request->ip_address;
        $camera->nvr_id = $request->nvr_id;
        $camera->name = $request->name;
        $camera->dept_id = $request->dept_id;
        $camera->merk = $request->merk;
        $camera->type = $request->type;
        $camera->model = $request->model;
        $camera->asset_number = $request->asset_number;
        $camera->username = $request->username;
        $camera->password = $request->password;
        $camera->location = $request->location;
        $camera->skip_check = $request->skip_check ?? 'N';
        $camera->skip_reason = $request->skip_reason;
        $camera->channel_number = $request->channel_number;
        
        if($camera->save())
        {
            return response()->json(['success' => 1, 'message' => 'Changed Success']);
        }else{
            return response()->json(['success' => 0, 'message' => 'Change Failed']);
        }

    }

    public function delete($id)
    {
        $camera_old = Camera::find($id);
        
        $camera_trash = new CameraTrash;
        $camera_trash->ip_address = $camera_old->ip_address;
        $camera_trash->nvr_id = $camera_old->nvr_id;
        $camera_trash->name = $camera_old->name;
        $camera_trash->dept_id = $camera_old->dept_id;
        $camera_trash->merk = $camera_old->merk;
        $camera_trash->type = $camera_old->type;
        $camera_trash->model = $camera_old->model;
        $camera_trash->asset_number = $camera_old->asset_number;
        $camera_trash->current_condition = $camera_old->current_condition;
        $camera_trash->image = $camera_old->image;
        $camera_trash->username = $camera_old->username;
        $camera_trash->password = $camera_old->password;
        $camera_trash->location = $camera_old->location;
        $camera_trash->channel_number = $camera_old->channel_number;
        $camera_trash->installed = $camera_old->installed;
        $camera_trash->skip_check = $camera_old->skip_check;
        $camera_trash->skip_reason = $camera_old->skip_reason;
        $camera_trash->save();

        $camera_old->delete();

        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);

    }
}
