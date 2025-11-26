@extends('include.header')
@section('content')
<div class="welcome-wrap mb-4">
    <div class=" d-flex align-items-center justify-content-between flex-wrap">
        <div class="mb-3">
            <h2 class="mb-1 text-white">Hello {{ Auth::user()->name }}</h2>
            <p class="text-light"><b>Welcome to {{ config('constant.app_name') }} - {{ date('d M, Y') }}</b></p>
        </div>
    </div>
    <div class="welcome-bg">
        <img src="{{ asset('assets/img/welcome-bg-02.svg') }}" alt="img" class="welcome-bg-01">
        <img src="{{ asset('assets/img/welcome-bg-01.svg') }}" alt="img" class="welcome-bg-03">
    </div>
</div>
<script>
	var page_title = "Dashboard";
</script>
@endsection
