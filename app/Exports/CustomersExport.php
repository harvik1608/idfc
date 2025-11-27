<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Return collection of customers.
     */
    public function collection()
    {
        // You can modify this to apply filters if needed
        return Customer::all();
    }

    /**
     * Define headings for the Excel sheet.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Email',
            'Phone',
            'City',
            'State',
            'Created At',
        ];
    }

    /**
     * Map each customer row to Excel.
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->mobile_no1,
            $customer->city,
            $customer->state,
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
