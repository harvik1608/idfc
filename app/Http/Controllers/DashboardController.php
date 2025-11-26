<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function general_settings()
    {
        $general_settings = General_setting::all();
        $data = array();
        foreach($general_settings as $general_setting) {
            $data[$general_setting->setting_key] = $general_setting->setting_val;
        }
        return view('admin.general_settings',$data);
    }

    public function submit_general_settings(Request $request)
    {
        try {
            $post = $request->all();
            unset($post['_token']);
            
            $data = array();
            foreach($post as $key => $val) {
                $data[] = ['setting_key' => $key,'setting_val' => $val,"created_at" => date("Y-m-d H:i:s")];
            }
            General_setting::truncate();
            General_setting::insert($data);
            return response()->json(['success' => true,'message' => "General Settings updated."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }
}
