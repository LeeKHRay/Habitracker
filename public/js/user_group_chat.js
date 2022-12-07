(function() {
    const fetchActivities = () => {
        $.get("/actions/user_group_chat_actions.php?action=fetchActivities", res => {
            $('#activities').html(res);
            setTimeout(fetchActivities, 5000);
        })
    };

    fetchActivities();

    const requests = {};

    // use long-polling techique to get chat history from database
    const fetchChatHistory = (activityID, host) => {
        let lastTime = 0;

        const longPolling = () => {
            const id = '#chat-history-' + activityID;

            requests[activityID] = $.get(`/actions/user_group_chat_actions.php?action=fetchChatHistory&last_time=${lastTime}&activity_id=${activityID}&host=${host}`, res => {
                try {
                    res = JSON.parse(res);
                    lastTime = res.lastTime;
                    if ($(id).length > 0) {
                        $('#chat-history-' + activityID).prepend(res.msg);
                        longPolling();
                    }
                }
                catch (e) {
                    if ($(id).length > 0) {
                        longPolling();
                    }
                }
            })
            .fail(err => {
                if ($(id).length > 0) {
                    longPolling();
                }
            });
        }

        longPolling();
    }

    // show a dialog box
    $(document).on('click', '.start-group-chat', function() {
        const activityID = $(this).data('activityId');
        const activityName = $(this).data('activityName');
        const host = $(this).data('host');
        const dialogBoxID = "#dialog-box-" + activityID;
        
        if ($(dialogBoxID).length == 0) { // avoid duplicate dialog box in DOM
            const dialogBox = `
                <div id="dialog-box-${activityID}" class="dialog-box" title="${activityName}">
                    <div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" id="msg-${activityID}">
                    <ul id="chat-history-${activityID}" class="list-unstyled"></ul>
                    </div>
                    <div class="mb-4">
                        <textarea name="msg" id="msg-input-${activityID}" class="form-control msg-input"></textarea>
                    </div>
                    <div align="right">
                        <button type="button" data-activity-id="${activityID}" class="btn btn-info send-msg-btn">Send</button>
                    </div>
                </div>`;

            $('#dialog-boxes').html(dialogBox);
            $(dialogBoxID).dialog({
                autoOpen:false,
                width: 400,
                close: function() {
                    $(this).dialog('destroy').remove(); // remove element from DOM when closing dialog box
                    requests[activityID].abort(); // abort request sent in fetchChatHistory()
                }
            }).dialog('open');

            fetchChatHistory(activityID, host);
        }
    });

    // send message
    $(document).on('click', '.send-msg-btn', function() {
        const activityID = $(this).data('activityId');
        const msg = $('#msg-input-' + activityID).val(); // fetch data from chat area field and store in this variable

        if (msg.length > 0) {
            $.post("/actions/user_group_chat_actions.php?action=sendMessage", {activityID, msg}, res => {
                $('#msg-input-' + activityID).val('');   //clear text area value
                $('#msg-' + activityID).scrollTop(0);
                
                // fetchChatHistory() will show the sent message
            });
        }
    });
})();
