(function() {
    const getFormData = () => {
        return $("form").serializeArray().reduce((data, {name, value}) => {
            data[name] = value;
            return data;
        }, {});
    };

    // remove error message
    $(document).on("keypress", "input", () => {
        $("#err-msg").html("");
        $("#msg").html("");
    });

    $(document).on("click", "#signup-btn", e => {
        e.preventDefault();

        const data = getFormData();
        
        $.post("/actions/user_actions.php?action=signup", data, res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
            else {
                $("#err-msg").html(res.data);
            }
        });
    });

    $(document).on("click", "#login-btn", e => {
        e.preventDefault();

        const data = getFormData();
        
        $.post("/actions/user_actions.php?action=login", data, res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
            else {
                $("#err-msg").html(res.data);
            }
        });
    });

    $(document).on("click", "#reset-password-request-btn", e => {
        e.preventDefault();

        const data = getFormData();
        
        $.post("/actions/user_actions.php?action=resetPasswordRequest", data, res => {
            res = JSON.parse(res);
            if (res.success) {
                $("form").trigger("reset");
                $("#msg").html(res.data);
            }
            else {
                $("#err-msg").html(res.data);
            }
        });
    });

    $(document).on("click", "#reset-password-btn", e => {
        e.preventDefault();

        const data = getFormData();
        
        $.post("/actions/user_actions.php?action=resetPassword", data, res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
            else {
                $("#err-msg").html(res.data);
            }
        });
    });

    $(document).on("click", "#admin-login-btn", e => {
        e.preventDefault();
        
        const data = getFormData();

        $.post("/actions/admin_actions.php?action=login", data, res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
            else {
                $("#err-msg").html(res.data);
            }
        });
    });
})();