(function() {
    // handle click event for logout button in navbar
    $(document).on("click", "#logout", e => {
        e.preventDefault();
        $.post("/actions/admin_actions.php?action=logout", res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
        })
    });

    // delete a user
    $(document).on("click", ".deleteUser", ({target}) => {
        const userID = $(target).data("id");
        const username = $(target).data("username");
        const email = $(target).data("email");
        const avatar = $(target).data("avatar")
        
        $.ajax({
            url: "/actions/admin_actions.php?action=deleteUser",
            type: "DELETE",
            contentType: "json",
            data: {userID, username, avatar}
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                $.post("/actions/admin_actions.php?action=notifyUser", {username, type: "user", email})
                location.reload();
            }
        });
    });

    // delete a goal
    $(document).on("click", ".deleteGoal", ({target}) => {
        const username = $(target).data("username");

        $.ajax({
            url: "/actions/admin_actions.php?action=deleteGoal",
            type: "DELETE",
            contentType: "json",
            data: {
                goalID: $(target).data("id"),
                username
            }
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                $.post("/actions/admin_actions.php?action=notifyUser", {itemName: res.data, type: "goal", username});
                location.reload();
            }
        });
    });

    // delete a activity
    $(document).on("click", ".deleteActivity", ({target}) => {
        const username = $(target).data("username");

        $.ajax({
            url: "/actions/admin_actions.php?action=deleteActivity",
            type: "DELETE",
            contentType: "json",
            data: {
                activityID: $(target).data("id"),
                username
            }
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                $.post("/actions/admin_actions.php?action=notifyUser", {itemName: res.data, type: "activity", username})
                location.reload();
            }
        });
    });

    // dismiss report
    $(document).on("click", ".dismiss", ({target}) => {
        $.ajax({
            url: "/actions/admin_actions.php?action=dismissReport",
            type: "PUT",
            contentType: "json",
            data: {reportID: $(target).data("id")}
        })
        .done(res => {
            location.reload();
        });
    });

    // resolve report after handling the report
    $(document).on("click", ".resolve", ({target}) => {
        $.ajax({
            url: "/actions/admin_actions.php?action=resolveReport",
            type: "PUT",
            contentType: "json",
            data: {reportID: $(target).data("id")}
        })
        .done(res => {
            location.reload();
        });
    });
})();
