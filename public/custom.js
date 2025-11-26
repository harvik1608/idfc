$(document).ready(function(){
	if($("#flashMsg").length) {
	    show_toast("success",$("#flashMsg").text());
	}
	$("a.userset").click(function(){
	    if($(this).hasClass("show")) {
	        $(this).removeClass("show");
	        $(this).attr("aria-expanded",false);
	        $(".menu-drop-user").removeClass("show");
	    } else {
	        $(this).addClass("show");
	        $(this).attr("aria-expanded",true);
	        $(".menu-drop-user").addClass("show");
	        $(".menu-drop-user").css({
	            position: "absolute",
	            inset: "0px 0px auto auto",
	            margin: "0px",
	            transform: "translate(0px, 34px)"
	        });
	    }
	});
	$("li.submenu-open").each(function(){
	    if($(this).find("ul li").length == 0) {
	        $(this).remove();
	    }
	});
	if($('.summernote').length > 0) {
	    $('.summernote').summernote({
	        placeholder: 'Type here...',
	        tabsize: 2,
	        height: "auto"
	    });
	}
	$("#main_menu_list li").each(function(){
	    if($.trim($(this).text()) == page_title) {
	        $(this).addClass("active");
	    }
	});

	$("#contactForm").submit(function(e){
		e.preventDefault();

		var isError = 0;
		if($("#contactForm #name").val().trim() == "") {
			isError = 1;
			$(".name-error").html("<small><i class='fas fa-warning'></i> Enter your name</small>");
		} else {
			$(".name-error").html("");
		}
		if($("#contactForm #email").val().trim() == "") {
			isError = 1;
			$(".email-error").html("<small><i class='fas fa-warning'></i> Enter your email</small>");
		} else {
			$(".email-error").html("");
		}
		if($("#contactForm #phone").val().trim() == "") {
			isError = 1;
			$(".phone-error").html("<small><i class='fas fa-warning'></i> Enter your phone</small>");
		} else {
			$(".phone-error").html("");
		}
		if($("#contactForm #message").val().trim() == "") {
			isError = 1;
			$(".message-error").html("<small><i class='fas fa-warning'></i> Enter your message</small>");
		} else {
			$(".message-error").html("");
		}

		if(isError == 0) {
			$.ajax({
				url: $("#contactForm").attr("action"),
				type: $("#contactForm").attr("method"),
				data: new FormData(this),
				processData: false,
				contentType: false,
				cache: false,
				beforeSend:function(){
					$("#contactForm button[type=submit]").text("Submitting").attr("disabled",true);
				},
				success:function(response) {
					if(response.status == 200) {
						$("#contactForm button[type=submit]").text("Submit").attr("disabled",false);
						$("#msgSubmit").html(response.message);
						setTimeout(function(){
							$("#msgSubmit").html("");
							$("#name,#email,#phone,#message").val("");
						},3000);
					}
				}
			});
		}
	});
});
function remove_row(deleteUrl)
{
    $.confirm({
        title: 'Confirm!',
        content: 'Are you sure you want to delete?',
        buttons: {
            confirm: function () {
                $.ajax({
                    url: deleteUrl,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // or use hidden input
                    },
                    success:function(response) {
                        if(response.success) {
                            show_toast("Success!",response.message,"success");
                            setTimeout(function(){
                                window.location.reload();
                            },3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 400) {
                            const res = xhr.responseJSON;
                            show_toast("Oops!",res.message,"error");
                        } else {
                            show_toast("Oops!","Something went wrong","error");
                        }
                    }
                });
            },
            cancel: function () {
                
            }
        }
    });
}