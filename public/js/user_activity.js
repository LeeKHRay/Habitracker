(function() {
    const getFormData = () => {
        return $("form").serializeArray().reduce((data, {name, value}) => {
            data[name] = value;
            return data;
        }, {});
    };

    $(document).on("click", "#reset-time-btn", () => {
        $("input[type='time']").val('');
    });

    // create a one-off activity
    $(document).on("click", "#create-one-off-btn", e => {
        e.preventDefault();

        if ($("#activity-name")[0].reportValidity() && $("#date")[0].reportValidity() && $("#time")[0].reportValidity()) {
            const data = getFormData();

            $.post("/actions/user_activity_actions.php?action=createOneOff", data, res => {
                res = JSON.parse(res);
                if (res.success) {
                    location.href = res.data;
                }
                else {
                    $("#err-msg").html(res.data);
                }
            });
        }
    });

    // create a recurring activity
    $(document).on("click", "#create-recurring-btn", e => {
        e.preventDefault();

        if ($("#activity-name")[0].reportValidity()) {
            const data = getFormData();

            $.post("/actions/user_activity_actions.php?action=createRecurring", data, res => {
                res = JSON.parse(res);
                if (res.success) {
                    location.href = res.data;
                }
                else {
                    $("#err-msg").html(res.data);
                }
            });
        }
    });

    // toggle delete button in activity details page
    $(document).on("change", "#activity-close", e => {
        e.preventDefault();
        $("#delete-activity-btn").toggleClass("d-none");    
    });

    // edit a activity
    $(document).on("click", "#edit-activity-btn", e => {
        e.preventDefault();

        if ($("form input[name='activityName']")[0].reportValidity()) {
            const data = getFormData();

            $.ajax({
                url: "/actions/user_activity_actions.php?action=editActivity",
                type: "PUT",
                contentType: "json",
                data
            })
            .done(res => {
                console.log(res);
                res = JSON.parse(res);
                if (res.success) {
                    location.href = res.data;
                }
                else {
                    $("#err-msg").html(res.data);
                }
            });
        }
    });

    // delete a activity
    $(document).on("click", "#delete-activity-btn", e => {
        e.preventDefault();

        $.ajax({
            url: "/actions/user_activity_actions.php?action=deleteActivity",
            type: "DELETE",
            contentType: "json",
            data: {
                activityID: $("input[name='activityID']").val()
            }
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
        });
    });

    // join a activity
    $(document).on("click", ".join-activity", e => {
        e.preventDefault();
        
        const activityID = $(e.target).data("activityId");
        
        $.post("/actions/user_activity_actions.php?action=joinActivity", {activityID}, res => {
            console.log(res);
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
        });
    });

    // quit a activity
    $(document).on("click", ".quit-activity", e => {
        e.preventDefault();
        
        $.ajax({
            url: "/actions/user_activity_actions.php?action=quitActivity",
            type: "DELETE",
            contentType: "json",
            data: {
                activityID: $(e.target).data("activityId")
            }
        })
        .done(res => {
            console.log(res);
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
        });
    });

    // search activities
    $(document).on("click", "#search-activity-btn", e => {
        e.preventDefault();
        
        if ($("form input[name='keyword']")[0].reportValidity()) {
            const queryString = $("form").serialize();
        
            $.get("/actions/user_activity_actions.php?action=searchActivities&" + queryString, res => {
                res = JSON.parse(res);
                if (res.success) {
                    $("#results").html(res.data);
                }
                else {
                    $("#err-msg").html(res.data);
                }
            });
        }
    });

    // report inappropriate activity
    $(document).on("click", "#report-activity-btn", e => {
        e.preventDefault();

        if ($("form textarea[name='reason']")[0].reportValidity()) {
            const data = getFormData();
            
            $.post("/actions/user_activity_actions.php?action=reportActivity", data, res => {
                console.log(res);
                res = JSON.parse(res);
                if (res.success) {
                    location.href = res.data;
                }
                else {
                    $("#err-msg").html(res.data);
                }
            });
        }
    });
})();
