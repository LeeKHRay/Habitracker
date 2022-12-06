(function() {
    const getFormData = () => {
        return $("form").serializeArray().reduce((data, {name, value}) => {
            data[name] = value;
            return data;
        }, {});
    };

    // update user's last activity every 5 seconds
    setInterval(() => {
        $.ajax({
            url: "/actions/user_actions.php?action=updateLastActivity",
            type: "PUT"
        });
    }, 5000);
    
    // remove error message
    $(document).on("keypress", "input, textarea", () => {
        $("#err-msg").html("");
        $("#msg").html("");
    });

    // handle click event for logout button in navbar
    $(document).on("click", "#logout", e => {
        e.preventDefault();
        
        $.post("/actions/user_actions.php?action=logout", res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
        })
    });

    // select avatar to upload
    $(document).on("change", "#avatar-upload", ({target}) => {
        const val = $(target).val();
        if (val == "") {
            $("#avatar-label").html("Choose jpg, jpeg, png file");
        }
        else {
            let tokens = $(target).val().split('\\');
            const filename = tokens[tokens.length - 1];
            console.log(filename);
            $("#avatar-label").html(filename);
        }
    });

    // update profile
    $(document).on("click", "#edit-profile-btn", e => {
        e.preventDefault();
        
        const data = getFormData();
        console.log(data)
        const formData = new FormData();
        formData.append('avatar', $("#avatar-upload").prop("files")[0]);

        const ajax1 = $.ajax({
            url: "/actions/user_actions.php?action=editProfile",
            type: "PUT",
            contentType: "json",
            data
        })

        const ajax2 = $.ajax({
            url: "/actions/user_actions.php?action=uploadAvatar",
            type: "POST",
            contentType: false,
            cache: false,
            processData:false,
            data: formData
        });

        $.when(ajax1, ajax2)
        .done(([res1], [res2]) => {
            res1 = JSON.parse(res1);
            res2 = JSON.parse(res2);
            
            if (res1.success && res2.success) {
                location.href = res1.data;
            }
            else if (!res2.success) {
                $("#err-msg").html(res2.data);
            }
        });
    });

    // update settings
    $(document).on("click", "#change-settings-btn", e => {
        e.preventDefault();

        const data = getFormData();
        
        $.ajax({
            url: "/actions/user_actions.php?action=changeSettings",
            type: "PUT",
            contentType: "json",
            data
        })
        .done(res => {
            console.log(res);
            res = JSON.parse(res);
            if (res.success) {
                $("#msg").html(res.data);
            }
        });
    });

    $(document).on("click", "#change-password-btn", e => {
        e.preventDefault();
        
        const data = getFormData();

        $.ajax({
            url: "/actions/user_actions.php?action=changePassword",
            type: "PUT",
            contentType: "json",
            data
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                $("#msg").html(res.data);
                $("form").trigger("reset");
            }
            else {
                $("#err-msg").html(res.data);
            }
        });
    });
})();