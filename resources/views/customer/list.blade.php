@extends('include.header')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4 class="fw-bold">Customer List</h4>
            <h6></h6>
        </div>
    </div>
    <ul class="table-top-head">
		<li>
			<a href="{{ route('admin.customer.export') }}" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Excel" data-bs-original-title="Export To Excel">
				<img src="{{ asset('assets/img/icons/excel.svg') }}" alt="img">
			</a>
		</li>
	</ul>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
        <div class="search-set">
            <div class="search-input">
                <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="tblList">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="50%">Name</th>
                        <th width="10%">Total Loans</th>
                        <th width="10%">Total Loan Amount</th>
                        <th width="10%">Total EMI</th>
                        <th width="10%" class="no-sort"></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
	var page_title = "Customer List";
	$(document).ready(function(){
		$(document).ready(function(){
	        $('#tblList').DataTable({
	            "processing": true,
	            "serverSide": true,
	            "pageLength": 25,
	            "ajax": {
	                "url": "{{ route('admin.customers.load') }}",
	                "type": "GET",
	                "data": function(d) {
	                    // You can send extra parameters if needed
	                    // d.extraParam = 'value';
	                }
	            },
	            "bFilter": true,
	            "sDom": 'fBtlpi',
	            "ordering": true,
	            "columns": [
	                { data: 'id' },
	                { data: 'customer_name' },
	                { data: 'total_loan' },
	                { data: 'total_loan_amount' },
	                { data: 'total_emi' },
	                { 
	                    data: 'actions', 
	                    orderable: false, 
	                    searchable: false,
	                    createdCell: function(td, cellData, rowData, row, col) {
	                        $(td).addClass('action-table-data'); // Add custom class to <td>
	                    }
	                }
	            ],
	            "language": {
	                search: ' ',
	                sLengthMenu: '_MENU_',
	                searchPlaceholder: "Search",
	                // sLengthMenu: 'Row Per Page _MENU_ Entries',
	                info: "_START_ - _END_ of _TOTAL_ items",
	                paginate: {
	                    next: ' <i class="fa fa-angle-right"></i>',
	                    previous: '<i class="fa fa-angle-left"></i>'
	                },
	            },
	            initComplete: (settings, json) => {
	                $('.dataTables_filter').appendTo('#tableSearch');
	                $('.dataTables_filter').appendTo('.search-input');
	            }  
	        });
	    });
	});
	function open_modal()
    {
    	$("#import-loan").modal("show");
    }
</script>
@endsection
