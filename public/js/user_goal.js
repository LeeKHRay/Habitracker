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

    // create a goal
    $(document).on("click", "#create-goal-btn", e => {
        e.preventDefault();

        if ($("form input[name='goalName']")[0].reportValidity() && $("form input[name='duration']")[0].reportValidity()) {
            const data = getFormData();

            $.post("/actions/user_goal_actions.php?action=createGoal", data, res => {
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

    // edit a goal
    $(document).on("click", "#edit-goal-btn", e => {
        e.preventDefault();

        if ($("form input[name='goalName']")[0].reportValidity() && $("form input[name='duration']")[0].reportValidity()) {
            const data = getFormData();

            $.ajax({
                url: "/actions/user_goal_actions.php?action=editGoal",
                type: "PUT",
                contentType: "json",
                data
            })
            .done(res => {
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

    // delete a goal
    $(document).on("click", ".delete-goal", e => {
        e.preventDefault();

        $.ajax({
            url: "/actions/user_goal_actions.php?action=deleteGoal",
            type: "DELETE",
            contentType: "json",
            data: {
                goalID: $(e.target).data("goalId")
            }
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                location.href = res.data;
            }
        });
    });

    // search goals
    $(document).on("click", "#search-goal-btn", e => {
        e.preventDefault();
        
        if ($("form input[name='keyword']")[0].reportValidity()) {
            const queryString = $("form").serialize();
        
            $.get("/actions/user_goal_actions.php?action=searchGoals&" + queryString, res => {
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

    // update goal progress
    $(document).on("click", "#update-goal-progress-btn", () => {
        // get goal id of the completed goals
        const goalIDs = $("table input[type='checkbox']:checked").map(function() { 
            return this.dataset.goalId;
        }).get();
        
        $.ajax({
            url: "/actions/user_goal_actions.php?action=updateGoalCompletion",
            type: "PUT",
            contentType: "json",
            data: {goalIDs}
        })
        .done(res => {
            res = JSON.parse(res);
            if (res.success) {
                $("#msg").html(res.data);
            }
        });
    });

    // report inappropriate goal
    $(document).on("click", "#report-goal-btn", e => {
        e.preventDefault();

        if ($("form textarea[name='reason']")[0].reportValidity()) {
            const data = getFormData();
            
            $.post("/actions/user_goal_actions.php?action=reportGoal", data, res => {
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