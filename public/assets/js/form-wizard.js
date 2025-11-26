// Bootstrap 5 native wizard implementation (no jQuery, no bootstrapWizard plugin)
document.addEventListener("DOMContentLoaded", function () {
  var triggerTabList = [].slice.call(document.querySelectorAll('.twitter-bs-wizard-nav .nav-link'));
  triggerTabList.forEach(function (triggerEl, idx) {
    var tabTrigger = new bootstrap.Tab(triggerEl);
    triggerEl.addEventListener('click', function (event,id) {
      alert($("#userForm #name").length);
      if($("#progrss-wizard a[class*=active]").attr("data-section") == "Personal") {
        var isError = 0;
        alert($("#userForm #name").val());
        if($.trim($("#userForm #name").val()) == "") {
          isError = 1;
          $("#name-error").html("Please enter name.");
        } else {
          $("#name-error").html("");
        }
        alert(isError);
        if($.trim($("#userForm #mobile_no").val()) == "") {
          isError = 1;
          $("#mobile_no-error").html("Please enter mobile no.");
        } else {
          $("#mobile_no-error").html("");
        }
        if($.trim($("#userForm #email").val()) == "") {
          isError = 1;
          $("#email-error").html("Please enter email.");
        } else {
          $("#email-error").html("");
        }
        if($.trim($("#userForm #address").val()) == "") {
          isError = 1;
          $("#address-error").html("Please enter address.");
        } else {
          $("#address-error").html("");
        }
        if($.trim($("#userForm #state").val()) == "") {
          isError = 1;
          $("#state-error").html("Please enter state.");
        } else {
          $("#state-error").html("");
        }
        if($.trim($("#userForm #city").val()) == "") {
          isError = 1;
          $("#city-error").html("Please enter city.");
        } else {
          $("#city-error").html("");
        }

        if(isError == 0) {
          event.preventDefault();
          tabTrigger.show();  
        }
      } 
    });
    // Update progress bar on tab shown
    triggerEl.addEventListener('shown.bs.tab', function () {
      // Evenly divide progress for 3 stages: 0%, 50%, 100%
      var progress = (idx) / (triggerTabList.length - 1) * 100;
      var progressBar = document.querySelector('.progress-bar');
      if (progressBar) {
        progressBar.style.width = progress + '%';
      }
    });
  });
});

function nextTab() {
  var active = document.querySelector('.twitter-bs-wizard-nav .nav-link.active');
  if (!active) return;
  var next = active.closest('li').nextElementSibling;
  if (next) {
    var nextLink = next.querySelector('.nav-link');
    if (nextLink) {
      var tab = new bootstrap.Tab(nextLink);
      tab.show();
    }
  }
}

function prevTab() {
  var active = document.querySelector('.twitter-bs-wizard-nav .nav-link.active');
  if (!active) return;
  var prev = active.closest('li').previousElementSibling;
  if (prev) {
    var prevLink = prev.querySelector('.nav-link');
    if (prevLink) {
      var tab = new bootstrap.Tab(prevLink);
      tab.show();
    }
  }
}