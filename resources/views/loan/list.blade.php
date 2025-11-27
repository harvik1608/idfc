@extends('include.header')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4 class="fw-bold">Loan List</h4>
            <h6></h6>
        </div>
    </div>
    <ul class="table-top-head">
		<li>
			<a href="{{ route('admin.loan.export') }}" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Excel" data-bs-original-title="Export To Excel">
				<img src="{{ asset('assets/img/icons/excel.svg') }}" alt="img">
			</a>
		</li>
	</ul>
    <div class="page-btn">
        <a href="javascript:;" onclick="open_modal()" class="btn btn-primary text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download me-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> Import</a>
    </div>
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
                        <th width="15%">Loan Account No</th>
                        <th width="15%">Customer ID</th>
                        <th width="20%">Name</th>
                        <th width="10%">Email</th>
                        <th width="20%">Location</th>
                        <th width="10%" class="no-sort"></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="import-loan">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="page-wrapper-new p-0">
				<div class="content">
					<form action="{{ route('admin.loan.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
						@csrf
						<div class="modal-header">
							<div class="page-title">
								<h4>Import</h4>
							</div>
							<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-lg-12">
									<div class="mb-3 image-upload-down">
										<label class="form-label">Upload File</label>
										<div class="image-upload download">
											<input type="file" name="excel" id="excel" required />
											<div class="image-uploads">
												<img src="{{ asset('assets/img/download-img.png') }}" alt="img">
												<h4>Choose <span> a file</span></h4>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Upload</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
	var page_title = "Loan List";
	$(document).ready(function(){
		$(document).ready(function(){
	        $('#tblList').DataTable({
	            "processing": true,
	            "serverSide": true,
	            "ajax": {
	                "url": "{{ route('admin.loans.load') }}",
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
	                { data: 'loan_account_no' },
	                { data: 'customer_id' },
	                { data: 'customer_name' },
	                { data: 'email_id' },
	                { data: 'location' },
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

	        $("#importForm").validate({
	            rules:{
	                excel:{
	                    required: true
	                }
	            },
	            messages:{
	                excel:{
	                    required: "<small class='text-danger'><b>Please choose file</b></small>"
	                }
	            }
	        });
	        $("#importForm").submit(function(e){
	            e.preventDefault();

	            if($("#importForm").valid()) {
	                $.ajax({
	                    url: $("#importForm").attr("action"),
	                    type: $("#importForm").attr("method"),
	                    data: new FormData(this),
	                    processData: false,
	                    contentType: false,
	                    cache: false,
	                    beforeSend:function(xhr){
	                        xhr.setRequestHeader("csrf-token", $("input[name=_csrf]").val());
	                        $("#importForm button[type=submit]").html('<div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>').attr("disabled",true);
	                    },
	                    success:function(response){
	                        if(response.success) {
	                            show_toast("Success!",response.message,"success");
	                            setTimeout(function(){
	                                window.location.reload();
	                            },3000);
	                        } else {
	                        	show_toast("Oops!",response.message,"error");
	                        }
	                    },
	                    error: function(xhr, status, error) {
	                        $("#importForm button[type=submit]").html("SUBMIT").attr("disabled",false);
	                        if (xhr.status === 400) {
	                            const res = xhr.responseJSON;
	                            show_toast("Oops!",res.message,"error");
	                        } else {
	                            show_toast("Oops!","Something went wrong","error");
	                        }
	                    }
	                });
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
