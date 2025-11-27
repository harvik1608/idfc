@extends('include.header')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Plan List</h4>
        <h6>(<span class='mandadory'>*</span>) indicates required field.</h6>
    </div>
</div>
<form action="{{ is_null($plan) ? url('plans') : url('plans/'.$plan->id) }}" method="POST" enctype="multipart/form-data" id="mainForm">
    @csrf
    @if(!is_null($plan))
        <input type="hidden" name="_method" value="PUT" />
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ is_null($plan) ? "New" : "Edit" }} Plan</h4>
                </div>
                <div class="card-body profile-body">
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ is_null($plan) ? '' : $plan->name }}" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label">Duration<span class="text-danger ms-1">*</span> <small>(in month)</small></label>
                            <input type="text" class="form-control" name="duration" id="duration" value="{{ is_null($plan) ? '' : $plan->duration }}" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label">Amount<span class="text-danger ms-1">*</span></label>
                            <input type="number" class="form-control" name="amount" id="amount" value="{{ is_null($plan) ? '' : $plan->amount }}" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label">Total Whatsapp Message<span class="text-danger ms-1">*</span></label>
                            <input type="number" class="form-control" name="whatsapp" id="whatsapp" value="{{ is_null($plan) ? '' : $plan->whatsapp }}" />
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Multiple File Allow</label>
                            <select class="select" name="is_multiple_file_allow" id="is_multiple_file_allow">
                                <option value="0" {{ !is_null($plan) && $plan->is_multiple_file_allow == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ !is_null($plan) && $plan->is_multiple_file_allow == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="select" name="is_active" id="is_active">
                                <option value="1" {{ !is_null($plan) && $plan->is_active == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !is_null($plan) && $plan->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" name="note" id="note">{{ is_null($plan) ? '' : $plan->note }}</textarea>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                        <a href="{{ url('plans') }}" class="btn btn-secondary" id="backBtn">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
	var page_title = "Plan List";
    $(document).ready(function(){
        $("#mainForm").validate({
            rules:{
                name:{
                    required: true
                },
                duration:{
                    required: true
                },
                amount:{
                    required: true
                },
                whatsapp:{
                    required: true
                }
            },
            messages:{
                name:{
                    required: "<small class='text-danger'><b>Plan Name is required.</b></small>"
                },
                duration:{
                    required: "<small class='text-danger'><b>Plan Duration is required.</b></small>"
                },
                amount:{
                    required: "<small class='text-danger'><b>Plan Amount is required.</b></small>"
                },
                whatsapp:{
                    required: "<small class='text-danger'><b>Total Whatsapp Message is required.</b></small>"
                }
            }
        });
        $("#mainForm").submit(function(e){
            e.preventDefault();

            if($("#mainForm").valid()) {
                $.ajax({
                    url: $("#mainForm").attr("action"),
                    type: $("#mainForm").attr("method"),
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    beforeSend:function(xhr){
                        xhr.setRequestHeader("csrf-token", $("input[name=_csrf]").val());
                        $("#mainForm button[type=submit]").html('<div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>').attr("disabled",true);
                    },
                    success:function(response){
                        if(response.success) {
                            show_toast("Success!",response.message,"success");
                            setTimeout(function(){
                                window.location.href = $("#backBtn").attr("href");
                            },3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        $("#mainForm button[type=submit]").html("SUBMIT").attr("disabled",false);
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
</script>
@endsection
