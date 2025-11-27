<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Loan;
use Auth;
use DB;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.list');
    }

    public function load(Request $request)
    {
        try {
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Loan::select(
                'customer_id',
                'customer_name',
                DB::raw('SUM(emi) as total_emi'),
                DB::raw('SUM(loan_amount) as total_loans'),
                DB::raw('COUNT(*) as total_loan'),
            )
            ->groupBy('customer_id', 'customer_name')->orderBy('customer_name','asc');
            if (!empty($searchValue)) {
                if (!empty($searchValue)) {
                    $query->havingRaw('(customer_name LIKE ? OR customer_id LIKE ?)', ["%{$searchValue}%", "%{$searchValue}%"]);
                }

            }
            $recordsTotal = Loan::distinct('customer_id')->count('customer_id');
            $filteredRows = $query->get() ?? collect();
            $recordsFiltered = $filteredRows->count();
            $rows = $filteredRows->slice($start, $length);

            $formattedData = [];
            if(!$rows->isEmpty()) {            
                foreach ($rows as $index => $row) {
                    $encryptedId = Crypt::encrypt($row->customer_id);

                    $actions = '<div class="edit-delete-action">';
                        // $actions .= '<a href="' . url('plans/'.$row->id.'/edit/') . '" class="me-2 edit-icon p-2 text-success" title="Edit">
                        //     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                        //         <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        //         <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        //     </svg>
                        // </a>';
                        $actions .= '<a href="'.url('customers/'.$encryptedId).'" class="p-2" title="View"><i class="ti ti-user fs-16"></i></a>';
                    $actions .= '</div>';
                    $formattedData[] = [
                        'id' => $start + $index + 1,
                        'customer_name' => $row->customer_name,
                        'total_loan' => $row->total_loan,
                        'total_loan_amount' => currency()." ".number_format($row->total_loans,2),
                        'total_emi' => currency()." ".number_format($row->total_emi,2),
                        'actions' => $actions
                    ];
                }
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

    public function show($encryptedId)
    {
        $customer_id = Crypt::decrypt($encryptedId);
        $customer = Loan::where("customer_id",$customer_id)->first();
        if($customer) {
            return view('customer.show',compact('customer_id','customer'));
        }
    }

    public function customer_loans(Request $request)
    {
        try {
            $customer_id = $request->customer_id;
            $draw = intval($request->get('draw', 0));
            $start = intval($request->get('start', 0));
            $length = intval($request->get('length', 10));
            $searchValue = $request->input('search.value', '');

            $query = Loan::query();
            $query = $query->where("customer_id",$customer_id);
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('customer_name', 'like', "%{$searchValue}%");
                    $q->where('loan_account_no', 'like', "%{$searchValue}%");
                    $q->where('customer_id', 'like', "%{$searchValue}%");
                });
            }
            $recordsTotal = Loan::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'asc')->get();

            $formattedData = [];
            if(!$rows->isEmpty()) {            
                foreach ($rows as $index => $row) {
                    $encryptedId = Crypt::encrypt($row->customer_id);
                    $formattedData[] = [
                        'id' => $start + $index + 1,
                        'emi' => $row->emi,
                        'loan_amount' => $row->loan_amount,
                        'pos' => $row->pos,
                        'actions' => ""
                    ];
                }
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

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID')
        ->setCellValue('B1', 'Customer Name')
        ->setCellValue('C1', 'Total Loans')
        ->setCellValue('D1', 'Total Loan Amount')
        ->setCellValue('E1', 'Total EMIs');

        $query = Loan::select(
            'customer_id',
            'customer_name',
            DB::raw('COUNT(*) as total_loan'),
            DB::raw('SUM(loan_amount) as total_loan_amount'),
            DB::raw('SUM(emi) as total_emi')
        )
        ->groupBy('customer_id', 'customer_name')->orderBy('customer_name','asc');
        $customers = $query->get() ?? collect();
        $row = 2;
        $no = 0;
        foreach($customers as $customer) {
            $no++;
            $sheet->setCellValue('A'.$row, $no)
            ->setCellValue('B'.$row, $customer->customer_name)
            ->setCellValue('C'.$row, $customer->total_loan)
            ->setCellValue('D'.$row, currency()." ".number_format($customer->total_loan_amount,2))
            ->setCellValue('E'.$row, currency()." ".number_format($customer->total_emi,2));
            $row++;
        }
        $writer = new Xlsx($spreadsheet);
        $fileName = 'customers.xlsx';
        $writer->save(public_path($fileName));

        return response()->download(public_path($fileName));
    }

    public function customer_export($customer_id)
    {
        $customer = Loan::where("customer_id",$customer_id)->first();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID')
        ->setCellValue('B1', 'EMI')
        ->setCellValue('C1', 'Loan Amount')
        ->setCellValue('D1', 'POS');

        $query = Loan::query();
        $query = $query->where("customer_id",$customer_id);
        $customers = $query->get() ?? collect();
        $row = 2;
        $total_loan_amount = 0;
        $no = 0;
        foreach($customers as $customer) {
            $no++;
            $total_loan_amount = $total_loan_amount + $customer->loan_amount;
            $sheet->setCellValue('A'.$row, $no)
            ->setCellValue('B'.$row, $customer->emi)
            ->setCellValue('C'.$row, number_format($customer->loan_amount,2))
            ->setCellValue('D'.$row, $customer->pos);
            $row++;
        }
        $sheet->setCellValue('C'.$row, number_format($total_loan_amount,2));
        $writer = new Xlsx($spreadsheet);
        
        $file = Str::slug($customer->customer_name,'_');
        $fileName = $file.'.xlsx';
        $writer->save(public_path($fileName));

        return response()->download(public_path($fileName));
    }
}
