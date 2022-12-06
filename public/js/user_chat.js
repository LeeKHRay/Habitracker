(function() {
    const fetchUsers = () => {
        $.get("/actions/user_chat_actions.php?action=fetchUsers", res => {
            $('#users').html(res);
            setTimeout(fetchUsers, 5000);
        })
    };

    fetchUsers();

    const requests = {};

    // use long-polling techique to get chat history from database
    const fetchChatHistory = userID => {
        let lastTime = 0;

        const longPolling = () => {
            const id = '#chat-history-' + userID;

            requests[userID] = $.get(`/actions/user_chat_actions.php?action=fetchChatHistory&last_time=${lastTime}&user_id=${userID}`, res => {
                try {
                    res = JSON.parse(res);
                    console.log(res);
                    lastTime = res.lastTime;
                    if ($(id).length > 0) {
                        $('#chat-history-' + userID).prepend(res.msg);
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
    $(document).on('click', '.start-chat', function() {
        const userID = $(this).data('userId');
        const username = $(this).data('username');
        const dialogBoxID = "#dialog-box-" + userID;
        
        if ($(dialogBoxID).length == 0) { // avoid duplicate dialog box in DOM
            const dialogBox = `
                <div id="dialog-box-${userID}" class="dialog-box" title="${username}">
                    <div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" id="msg-${userID}">
                    <ul id="chat-history-${userID}" class="list-unstyled"></ul>
                    </div>
                    <div class="mb-4">
                        <textarea name="msg" id="msg-input-${userID}" class="form-control msg-input"></textarea>
                    </div>
                    <div>
                        <button type="button" data-user-id="${userID}" class="btn btn-info send-msg-btn">Send</button>
                    </div>
                </div>`;

            $('#dialog-boxes').html(dialogBox);
            $(dialogBoxID).dialog({
                autoOpen:false,
                width: 400,
                close: function() {
                    $(this).dialog('destroy').remove(); // remove element from DOM when closing dialog box
                    requests[userID].abort(); // abort request sent in fetchChatHistory()
                }
            }).dialog('open');

            fetchChatHistory(userID);
        }
    });

    // send message
    $(document).on('click', '.send-msg-btn', function() {
        const toUserID = $(this).data('userId');
        const msg = $('#msg-input-' + toUserID).val(); // fetch data from chat area field and store in this variable

        if (msg.length > 0) {
            $.post("/actions/user_chat_actions.php?action=sendMessage", {toUserID, msg}, res => {
                $('#msg-input-' + toUserID).val('');   //clear text area value
                $('#msg-' + toUserID).scrollTop(0);

                // fetchChatHistory() will show the sent message
            });
        }
    });

    //function for sensing one's cursor activity in the dialog box for displaying the typing status of a certain user
    $(document).on('focus', '.msg-input', () => {  //execute code if cursor come into text area field
        $.ajax({
            url:"/actions/user_chat_actions.php?action=updateIsTyping",
            method:"PUT",
            data:{isTyping: 1}
        })
    });

    $(document).on('blur', '.msg-input', () => {  //execute code if cursor come into text area field
        $.ajax({
            url:"/actions/user_chat_actions.php?action=updateIsTyping",
            method:"PUT",
            data:{isTyping: 0}
        })
    });
})();
