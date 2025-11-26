document.addEventListener("DOMContentLoaded", function () {

    var triggerTabList = [].slice.call(document.querySelectorAll('.twitter-bs-wizard-nav .nav-link'));

    triggerTabList.forEach(function (triggerEl, idx) {
        var tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function (event) {

            let section = $("#progrss-wizard a[class*=active]").attr("data-section");
            if (section === "Personal") {

                let isError = 0;

                // List all fields to validate
                const fields = ["name", "mobile_no", "address", "state", "city"];

                fields.forEach(id => {
                    let value = $.trim($("#" + id).val());
                    if (value === "") {
                        isError = 1;
                        $("#" + id + "-error").html("Please enter " + id.replace("_", " ") + ".");
                    } else {
                        $("#" + id + "-error").html("");
                    }
                });

                if (isError === 1) {
                    // stop tab change
                    event.preventDefault();
                    return false;
                }

                // allow tab to switch
                tabTrigger.show();
            } else if(section === "Whatsapp") {
                let isError = 0;

                // List all fields to validate
                const fields = ["contact_person_name", "company_name", "industry", "service_name", "zipcode"];

                fields.forEach(id => {
                    let value = $.trim($("#" + id).val());
                    if (value === "") {
                        isError = 1;
                        $("#" + id + "-error").html("Please enter " + id.replace("_", " ") + ".");
                    } else {
                        $("#" + id + "-error").html("");
                    }
                });

                if (isError === 1) {
                    // stop tab change
                    event.preventDefault();
                    return false;
                }

                // allow tab to switch
                tabTrigger.show();
            } else if(section === "Email") {
                let isError = 0;

                // List all fields to validate
                const fields = ["email", "username", "password"];
                fields.forEach(id => {
                    let value = $.trim($("#" + id).val());
                    if (value === "") {
                        isError = 1;
                        $("#" + id + "-error").html("Please enter " + id.replace("_", " ") + ".");
                    } else {
                        $("#" + id + "-error").html("");
                    }
                });

                if (isError === 1) {
                    // stop tab change
                    event.preventDefault();
                    return false;
                }

                // allow tab to switch
                tabTrigger.show();
            } else if(section === "Payment") {
                let isError = 0;

                // List all fields to validate
                const fields = ["plan_id"];
                fields.forEach(id => {
                    let value = $.trim($("#" + id).val());
                    if (value === "") {
                        isError = 1;
                        $("#" + id + "-error").html("Please enter " + id.replace("_", " ") + ".");
                    } else {
                        $("#" + id + "-error").html("");
                    }
                });

                if (isError === 1) {
                    // stop tab change
                    event.preventDefault();
                    return false;
                }

                // allow tab to switch
                tabTrigger.show();
            }
        });

        // Update progress bar
        triggerEl.addEventListener('shown.bs.tab', function () {
            var progress = idx / (triggerTabList.length - 1) * 100;
            document.querySelector('.progress-bar').style.width = progress + '%';
        });

    });
});
