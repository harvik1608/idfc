<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Loan;
use Auth;

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
                $query->whereRaw('(customer_name LIKE ? OR customer_id LIKE ?)', ["%{$searchValue}%", "%{$searchValue}%"]);
                // $query->where(function ($q) use ($searchValue) {
                //     $q->where('customer_name', 'like', "%{$searchValue}%");
                //     $q->where('loan_account_no', 'like', "%{$searchValue}%");
                //     $q->where('customer_id', 'like', "%{$searchValue}%");
                // });
            }
            $recordsTotal = Loan::count();
            $recordsFiltered = $query->count();
            $rows = $query->offset($start)->limit($length)->orderBy('id', 'asc')->get();

            $formattedData = [];
            foreach ($rows as $index => $row) {
                $actions = '<div class="edit-delete-action">';
                    // $actions .= '<a href="' . url('plans/'.$row->id.'/edit/') . '" class="me-2 edit-icon p-2 text-success" title="Edit">
                    //     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                    //         <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    //         <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    //     </svg>
                    // </a>';
                    // $actions .= '<a href="javascript:;" onclick="remove_row(\'' . url('plans/' . $row->id) . '\')" data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2" title="Delete">
                    //     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                    //         <polyline points="3 6 5 6 21 6"></polyline>
                    //         <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    //         <line x1="10" y1="11" x2="10" y2="17"></line>
                    //         <line x1="14" y1="11" x2="14" y2="17"></line>
                    //     </svg>
                    // </a>';
                $actions .= ' <a href="'.route('admin.customer.export',['customer_id' => $row->customer_id]).'" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Excel" data-bs-original-title="Export To Excel"><img src="'.asset('assets/img/icons/excel.svg').'" alt="img"></a>';
                $actions .= '</div>';
                $formattedData[] = [
                    'id' => $start + $index + 1,
                    'loan_account_no' => $row->loan_account_no,
                    'customer_id' => $row->customer_id,
                    'customer_name' => format_text($row->customer_name),
                    'email_id' => $row->email_id,
                    'emi' => $row->emi,
                    'location' => $row->city.", ".$row->state,
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
            $file = public_path('uploads') . '/' . $excelFile;
        } 
        $total_rows = 0;       
        $rows = [];
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rowIndex = 0;
        foreach ($sheet->getRowIterator() as $row) {
            if ($rowIndex === 0) {
                $rowIndex++;
                continue;
            }
            $total_rows++;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            $rows[] = array(
                "loan_account_no" => $rowData[1],
                "customer_id" => $rowData[2],
                "customer_cif" => $rowData[4],
                "count" => $rowData[5],
                "customer_name" => $rowData[6],
                "product" => $rowData[7],
                "emi" => $rowData[8],
                "pos" => $rowData[9],
                "loan_amount" => $rowData[10],
                "pennanent_address" => $rowData[11],
                "permanent_address" => $rowData[12],
                "communication_address" => $rowData[13],
                "pincode" => $rowData[14],
                "city" => $rowData[15],
                "state" => $rowData[16],
                "email_id" => $rowData[17],
                "mobile_no1" => $rowData[18],
                "mobile_no2" => $rowData[19],
                "lok_adalat" => $rowData[20],
                "advocate" => $rowData[21],
                "court_location" => $rowData[22],
                "rcm_name" => $rowData[23],
                "contact_no" => $rowData[24],
                "created_by" => Auth::user()->id,
                "created_at" => date("Y-m-d H:i:s"),
            );
        }
        foreach (array_chunk($rows, 300) as $chunk) {
            Loan::insert($chunk);
        }

        if (file_exists($file)) {
            unlink($file);
        }
        return response()->json(['success' => true,'message' => "Total $total_rows rows added."], 200);
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Sr. No')
        ->setCellValue('B1', 'Loan Account Number')
        ->setCellValue('C1', 'Cust Id')
        ->setCellValue('D1', 'Cust CIF')
        ->setCellValue('E1', 'Count')
        ->setCellValue('F1', 'Customer Name')
        ->setCellValue('G1', 'Product')
        ->setCellValue('H1', 'EMI')
        ->setCellValue('I1', 'POS')
        ->setCellValue('J1', 'Loan Amount')
        ->setCellValue('K1', 'Pennanent Address')
        ->setCellValue('L1', 'Permanent Address')
        ->setCellValue('M1', 'Communication Address')
        ->setCellValue('N1', 'Pincode')
        ->setCellValue('O1', 'City')
        ->setCellValue('P1', 'State')
        ->setCellValue('Q1', 'Email ID')
        ->setCellValue('R1', 'Mobile No 1')
        ->setCellValue('S1', 'Mobile No 2')
        ->setCellValue('T1', 'Lok Adalat YES/ NO')
        ->setCellValue('U1', 'Advocate')
        ->setCellValue('V1', 'Court Location')
        ->setCellValue('W1', 'CM/ACM/RCM Name')
        ->setCellValue('X1', 'Contact No');

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // white text
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'efefef'], // black background (use any hex color)
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        // Apply to header row
        $sheet->getStyle('A1:X1')->applyFromArray($headerStyle);

        foreach (range('A', 'X') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $loans = Loan::get();
        $row = 2;
        $no = 0;
        foreach($loans as $loan) {
            $no++;
            $sheet->setCellValue('A'.$row, $no);
            $sheet->setCellValue('B'.$row, $loan->loan_account_no);
            $sheet->setCellValue('C'.$row, $loan->customer_id);
            $sheet->setCellValue('D'.$row, $loan->customer_cif);
            $sheet->setCellValue('E'.$row, $loan->count);
            $sheet->setCellValue('F'.$row, $loan->customer_name);
            $sheet->setCellValue('G'.$row, $loan->product);
            $sheet->setCellValue('H'.$row, $loan->emi);
            $sheet->setCellValue('I'.$row, $loan->pos);
            $sheet->setCellValue('J'.$row, $loan->loan_amount);
            $sheet->setCellValue('K'.$row, $loan->pennanent_address);
            $sheet->setCellValue('L'.$row, $loan->permanent_address);
            $sheet->setCellValue('M'.$row, $loan->communication_address);
            $sheet->setCellValue('N'.$row, $loan->pincode);
            $sheet->setCellValue('O'.$row, $loan->city);
            $sheet->setCellValue('P'.$row, $loan->state);
            $sheet->setCellValue('Q'.$row, $loan->email_id);
            $sheet->setCellValue('R'.$row, $loan->mobile_no1);
            $sheet->setCellValue('S'.$row, $loan->mobile_no2);
            $sheet->setCellValue('T'.$row, $loan->lok_adalat);
            $sheet->setCellValue('U'.$row, $loan->advocate);
            $sheet->setCellValue('V'.$row, $loan->court_location);
            $sheet->setCellValue('W'.$row, $loan->rcm_name);
            $sheet->setCellValue('X'.$row, $loan->contact_no);
            $row++;
        }
        $writer = new Xlsx($spreadsheet);
        $fileName = 'loans.xlsx';
        $writer->save(public_path($fileName));

        return response()->download(public_path($fileName));
    }
}
