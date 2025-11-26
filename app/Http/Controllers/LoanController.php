<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Loan;

class LoanController extends Controller
{
    public function index()
    {
        return view('loan.list');
    }

    public function load(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Loan::query();
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('customer_name', 'like', "%{$searchValue}%");
                    $q->where('loan_account_no', 'like', "%{$searchValue}%");
                    $q->where('customer_id', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Loan::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'desc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
                    $actions .= '<a href="' . url('plans/'.$row->id.'/edit/') . '" class="me-2 edit-icon p-2 text-success" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>';
                    $actions .= '<a href="javascript:;" onclick="remove_row(\'' . url('plans/' . $row->id) . '\')" data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2" title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </a>';
                $actions .= '</div>';
                $formattedData[] = [
                    'id' => $start + $index + 1,
                    'name' => $row->loan_account_no,
                    'name' => $row->customer_id,
                    'name' => $row->customer_name,
                    'actions' => $actions
                ];
            }
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        $plan = null;
        return view('plan.add_edit',compact('plan'));
    }

    public function store(Request $request)
    {
        try {
            $post = $request->all();

            $row = new Plan;
            $row->name = trim($post['name']);
            $row->duration = trim($post['duration']);
            $row->amount = trim($post['amount']);
            $row->note = trim($post['note']);
            $row->whatsapp = trim($post['whatsapp']);
            $row->is_multiple_file_allow = $post['is_multiple_file_allow'];
            $row->is_active = $post['is_active'];
            $row->created_at = date("Y-m-d H:i:s");
            $row->save();

            return response()->json(['success' => true,'message' => "Plan added successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function edit($id)
    {
        $plan = Plan::find($id);
        if(!$plan) {
            return redirect()->route("admin.dashboard");
        }
        return view('plan.add_edit',compact('plan'));   
    }

    public function update(Request $request,$id)
    {
        try {
            $post = $request->all();

            $row = Plan::find($id);
            $row->name = trim($post['name']);
            $row->duration = trim($post['duration']);
            $row->amount = trim($post['amount']);
            $row->whatsapp = trim($post['whatsapp']);
            $row->note = trim($post['note']);
            $row->is_multiple_file_allow = $post['is_multiple_file_allow'];
            $row->is_active = $post['is_active'];
            $row->updated_at = date("Y-m-d H:i:s");
            $row->save();

            return response()->json(['success' => true,'message' => "Plan edited successfully."], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => $e->getMessage()], 200);
        }
    }

    public function destroy($id)
    {
        Plan::destroy($id);
        return response()->json(['success' => true,'message' => "Plan removed successfully."], 200);
    }

    public function import(Request $request)
    {
        $file = "";
        if ($request->hasFile('excel')) {
            $image = $request->file('excel');

            // generate random file name
            $excelFile = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $path = $image->move(public_path('uploads'), $excelFile);
            $file = asset("uploads/".$excelFile);
        }        

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        preview(count($rows));
        array_shift($rows);

        $result = [];
        foreach ($rows as $row) {
            echo $row[0]."<br>";
        }

        Storage::delete($uploadedPath);
    }
}
