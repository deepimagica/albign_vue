$(document).ready(function () {
    $("#loginForm").submit(function (e) {
        e.preventDefault();
        $(".text-danger").text("");
        $("#login_error").hide();
        let formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                } else {
                    console.log(response, "response");
                }
            },
            error: function (response) {
                if (response.status === 401) {
                    $("#login_error").show();
                    $("#login_error_message").text(
                        response.responseJSON.message
                    );
                } else if (response.responseJSON && response.responseJSON.errors){
                    let errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $("#" + key + "_error").text(value[0]);
                    });
                }
            },
        });
    });

    $("#employee_code , #password").keypress(function (e) {
        if (e.which == 13) {
            $("#loginForm").submit();
        }
    });

    $("#loginButton").on('click',function () {
        $("#loginForm").submit();
    });
});
