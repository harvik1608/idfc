<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">
	<head>
		<meta charset="utf-8" />
    	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    	<title>{{ config('constant.app_name') }}</title>
    	<link rel="icon" type="image/x-icon" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/img/favicon/favicon.ico" />
    	<link rel="stylesheet" href="{{ asset('auth/vendor/css/core.css') }}" class="template-customizer-core-css" />
	    <link rel="stylesheet" href="{{ asset('auth/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
	    <link rel="stylesheet" href="{{ asset('auth/css/demo.css') }}" />
	    <link rel="stylesheet" href="{{ asset('auth/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
	    <link rel="stylesheet" href="{{ asset('auth/vendor/css/pages/page-auth.css') }}">
	    <link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toast.css') }}">
		<link rel="stylesheet" href="{{ asset('custom.css') }}">
	</head>
	<body>
		<div class="container-xxl">
			<div class="authentication-wrapper authentication-basic container-p-y">
				<div class="authentication-inner">
					<div class="card px-sm-6 px-0">
						<div class="card-body">
							<div class="app-brand justify-content-center">
								<a href="index-2.html" class="app-brand-link gap-2">
									<span class="app-brand-text demo text-heading fw-bold">{{ config('constant.app_name') }}</span>
								</a>
							</div>
							<!-- <h4 class="mb-1">Sign In! 👋</h4> -->
							<p class="mb-6">Please sign-in to your account and start the adventure</p>
							<form id="formAuthentication" class="mb-6" action="{{ route('check.login') }}" method="POST">
								@csrf
								<div class="mb-6">
									<label for="email" class="form-label">Email</label>
									<input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus />
								</div>
								<div class="mb-6 form-password-toggle">
									<label class="form-label" for="password">Password</label>
									<div class="input-group input-group-merge">
										<input type="password" id="password" class="form-control" name="password" placeholder="Enter your password" aria-describedby="password" required />
										<span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
									</div>
								</div>
								<div class="mb-6">
									<button class="btn btn-primary d-grid w-100" type="submit">Sign In</button>
									<!-- <div class="spinner-border spinner-border-sm text-secondary" role="status">
										<span class="visually-hidden">Loading...</span>
							        </div> -->
								</div>
								<a href="{{ route('forget.password') }}" style="float: right;"><span>Forgot Password?</span></a>
								@if ($errors->has('error'))
							    	<div class="alert alert-danger" hidden>
							        	{{ $errors->first('error') }}
							    	</div>
								@endif
								@if ($errors->has('success'))
							    	<div class="alert alert-success" hidden>
							        	{{ $errors->first('success') }}
							    	</div>
								@endif
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
		<script src="{{ asset('auth/vendor/js/helpers.js') }}"></script>
	    <script src="{{ asset('auth/js/config.js') }}"></script>
	    <script src="{{ asset('assets/js/toast.js') }}"></script>
	    <script>
	    	$(document).ready(function(){
	    		if($(".alert-danger").length > 0) {
	    			show_toast("Oops!",$(".alert-danger").text(),"error");
	    		}
	    		if($(".alert-success").length > 0) {
	    			show_toast("Success!",$(".alert-success").text(),"success");
	    		}
	    	});
	    	function show_toast(title,msg,type,second = 3000)
            {
                $.toast({
                    heading: title,
                    text: msg,
                    showHideTransition: 'fade',
                    icon: type,
                    position: 'top-right',
                    hideAfter: second
                });
            }
	    </script>
	</body>
</html>