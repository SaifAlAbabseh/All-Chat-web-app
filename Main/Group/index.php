<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]))) {
    header("Location:../../");
} else {
    require_once("../../DB.php");
    $group_table_name = "g" . $_REQUEST["group_id"] . "_users";

    try {
        $check_query = "SELECT g.group_name, g.leader_username FROM all_chat_groups g INNER JOIN $group_table_name gu WHERE BINARY gu.username=? AND g.group_id=?";
        $stmt = mysqli_prepare($conn, $check_query);

        if ($stmt === false) {
            header("Location:../");
            exit();
        }

        $who = $_SESSION["who"];
        $gid = $_REQUEST["group_id"];
        mysqli_stmt_bind_param($stmt, "ss", $who, $gid);

        if (!mysqli_stmt_execute($stmt)) {
            header("Location:../");
            exit();
        }

        $check_result = mysqli_stmt_get_result($stmt);
        if (!mysqli_num_rows($check_result)) {
            header("Location:../");
            exit();
        }
        $_SESSION["isGood" . $_REQUEST["group_id"]] = true;
        $check_result_row = mysqli_fetch_row($check_result);
        $lastmessageindex = 0;
        $tname = "";
    } catch (Exception $e) {
        header("Location:../");
        exit();
    }
}
require_once(dirname(__DIR__, 2) . '/common.php');
require_once(dirname(__DIR__, 2) . '/ws_auth.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        Group Chat
    </title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="<?= asset('../../Extra/styles/cssFiles/themes.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('light-mode');
            });
        }
    </script>
    <style>
        body,
        html {
            height: 100%;
        }
        /* Modern Confirmation Modal */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .custom-modal.show {
            display: flex;
            opacity: 1;
        }
        .custom-modal-content {
            background: #2B303A;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transform: scale(0.8);
            transition: transform 0.3s ease;
            min-width: 300px;
            border: 1px solid #3d4554;
        }
        .custom-modal.show .custom-modal-content {
            transform: scale(1);
        }
        .custom-modal-actions {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        .custom-modal-actions button {
            flex: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        .btn-cancel {
            background: #394240;
            color: #fff;
        }
        .btn-cancel:hover {
            background: #506C7F;
        }
        .btn-confirm {
            background: #F76D57;
            color: #fff;
        }
        .btn-confirm:hover {
            background: #ff523a;
            box-shadow: 0 0 15px rgba(247, 109, 87, 0.4);
        }
    </style>
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script>
        var ws = null;
        var tname = "<?php echo isset($_REQUEST['group_id']) ? 'g' . $_REQUEST['group_id'] : ''; ?>";
        
        function showAddMemberBox() {
            $("#main_box_parent").css({
                "pointer-events": "none",
                "opacity": "0.5",
                "user-select": "none"
            });
            $("#addfriendBox").show();
            var group_id = "<?php echo "" . $_REQUEST["group_id"]; ?>";
            generateURL("getLeaderFriends.php?group_id=" + group_id, 2).then(url => {
                $("#innerData").load(url);
            });
        }

        function showGroupInfoBox() {
            $("#main_box_parent").css({
                "pointer-events": "none",
                "opacity": "0.5",
                "user-select": "none"
            });
            $("#mainDialogBox").show();
            var group_id = "<?php echo "" . $_REQUEST["group_id"]; ?>";
            generateURL("getGroupInfo.php?group_id=" + group_id, 2).then(url => {
                $("#group_users_box").load(url);
            });
        }

        function exitDialog() {
            $("#main_box_parent").css({
                "pointer-events": "initial",
                "opacity": "1",
                "user-select": "auto"
            });
            $("#mainDialogBox").hide();
        }

        function addExitDialog() {
            $("#main_box_parent").css({
                "pointer-events": "initial",
                "opacity": "1",
                "user-select": "auto"
            });
            $("#addfriendBox").hide();
        }

        function showEditGroupPicBox() {
            $("#main_box_parent").css({
                "pointer-events": "none",
                "opacity": "0.5",
                "user-select": "none"
            });
            $("#mainDialogBox").css({
                "pointer-events": "none",
                "opacity": "0.5",
                "user-select": "none"
            });
            $("#groupEditBox").show();
        }

        function editExitDialog() {
            $("#mainDialogBox").css({
                "pointer-events": "initial",
                "opacity": "1",
                "user-select": "auto"
            });
            $("#groupEditBox").hide();
        }

        function openDestroyModal() {
            $("#destroyGroupModal").css("display", "flex");
            setTimeout(() => $("#destroyGroupModal").addClass("show"), 10);
            $("#confirmDestroyBtn").off("click").on("click", function() {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({
                        type: 'destroy_group',
                        tname: tname
                    }));
                }
                setTimeout(() => {
                    window.location.href = "Destroy_Group/?group_id=<?php echo $_REQUEST['group_id']; ?>";
                }, 100);
            });
        }

        function kickUser(memberToKick) {
            if (confirm("Are you sure you want to kick " + memberToKick + "?")) {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({
                        type: 'kick_user',
                        tname: tname,
                        target: memberToKick
                    }));
                }
                setTimeout(() => {
                    window.location.href = "../../kickMember.php?group_id=<?php echo $_REQUEST['group_id']; ?>&member=" + encodeURIComponent(memberToKick);
                }, 100);
            }
        }

        function closeDestroyModal() {
            $("#destroyGroupModal").removeClass("show");
            setTimeout(() => {
                $("#destroyGroupModal").css("display", "none");
            }, 300);
        }
    </script>
</head>

<body>
    <?php

    if ($check_result_row[1] == $_SESSION["who"]) {
        echo
        "
            <div id='groupEditBox' class='friendsListBox' style='max-width: 400px; padding: 2rem; border-radius: 20px; text-align: center;'>
                <button onclick='editExitDialog()' id='exit' style='position:absolute; top:15px; right:20px; background:transparent; border:none; color:var(--text-color); font-size:1.5rem; cursor:pointer; transition: transform 0.2s;'>✖</button>
                <h2 style='margin-bottom: 0.5rem; color: var(--text-color); font-weight: 600;'>Update Picture</h2>
                <p style='color: #F76D57; font-size: 0.9rem; margin-bottom: 1.5rem;'>Must be at most 1MB and type PNG</p>
                <form action='' method='post' enctype='multipart/form-data' style='display:flex; flex-direction:column; gap:1.5rem; align-items:center; width: 100%;'>
                    <div style='width: 100%; border: 2px dashed rgba(255,255,255,0.2); border-radius: 15px; padding: 2rem 1rem; background: rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s ease;' onmouseover='this.style.borderColor=\"#F76D57\"; this.style.background=\"rgba(247,109,87,0.05)\"' onmouseout='this.style.borderColor=\"rgba(255,255,255,0.2)\"; this.style.background=\"rgba(0,0,0,0.1)\"'>
                        <input type='file' name='pic' id='picField' accept='.png' required style='color: var(--text-color); width: 100%; cursor: pointer;' />
                    </div>
                    <button type='submit' class='btn-primary' name='changePicButton' style='width:100%; padding: 12px; font-size: 1.1rem; border-radius: 25px; border: none; cursor: pointer;'>Upload Picture</button>
                </form>
            </div>
            ";
    }

    ?>
    <div class="friendsListBox" id="addfriendBox">
        <button onclick="addExitDialog()" id="exit">X</button>
        <center>
            <table>
                <thead>
                    <tr>
                        <th colspan="3" style="color:white">
                            &gt; Your Friends :
                        </th>
                    </tr>
                </thead>
                <tbody id="innerData">
                </tbody>
            </table>
        </center>
    </div>
    <div class="friendsListBox" id="mainDialogBox">
        <button onclick="exitDialog()" id="exit">X</button>
        <center>
            <?php
            if ($check_result_row[1] == $_SESSION["who"]) {
                echo "
                <a href='#' onclick='openDestroyModal()' class='link btn-danger'>Destroy Group</a>
                &nbsp;
                <a href='#' class='link btn-primary' onclick='showEditGroupPicBox()'>Edit Picture</a>
                ";
            } else {
                echo "<a href='Leave_Group/?group_id=" . $_REQUEST["group_id"] . "' class='link btn-danger'>Leave Group</a>";
            }
            ?>
            <br /><br /><br />
            <table id="group_info_table">
                <tbody id="group_users_box">
                </tbody>
            </table>
        </center>
    </div>
    <div id="main_box_parent">
        <div class="mobile-back-btn" style="position: absolute; top: 20px; left: 20px; z-index: 1000;">
            <a href="../" class="backButton" style="text-decoration:none; font-size:1.5rem; color: #F76D57; font-weight:bold; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:20px; backdrop-filter:blur(5px); transition:all 0.3s ease;">🔙 Back</a>
        </div>
        <center>
            <div class="main_chat_box">
                <div class="main_chat_inner_box">
                    <div class="header">
                        <?php
                        echo "<img src='../View_Group_Image/?group_id=" . $_REQUEST["group_id"] . "' width='50px' height='50px' style='border-radius:50%; border:solid 2px #F76D57; box-shadow:0 2px 10px rgba(247,109,87,0.3);'/>";
                        ?>
                        <h2 style="margin:0; font-size:1.5rem;">
                            <?php
                            echo htmlspecialchars($check_result_row[0]);
                            ?>
                        </h2>
                        <a onclick="showGroupInfoBox()" title="Group Info" class="settingsIconClickable" href="#" style="margin-left:auto; display:flex; align-items:center;">
                            <img class="settingsIcon" src="../../Extra/styles/images/settings.png" alt="settings icon" style="width:30px; height:30px;">
                        </a>
                        <?php
                        if ($check_result_row[1] == $_SESSION["who"]) {
                            echo "<button onclick='showAddMemberBox()' title='Add New Member' id='add_member_button' style='background:#22c55e; color:white; border:none; border-radius:50%; width:35px; height:35px; font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(34,197,94,0.4);'>+</button>";
                        }
                        ?>
                    </div>
                    <div id="chatBox">
                        <div id="messages" class="at-top" onscroll="showBlurOnTop()">
                            <div class="blur-top"></div>
                            <div id="msg">
                                <?php
                                try {
                                    $group_table_id = "g" . $_REQUEST["group_id"];
                                    $query = "SELECT * FROM " . $group_table_id . "";
                                    $stmt = mysqli_prepare($conn, $query);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    if (mysqli_num_rows($result)) {
                                        while ($row = mysqli_fetch_row($result)) {
                                            $from = "" . $row[0];
                                            $messageitself = "" . $row[1];
                                            $lastmessageindex++;
                                            $date = "" . $row[3];
                                            $you = $_SESSION["who"];
                                ?>

                                            <div class='message-container-outer' id='msg-pos-<?php echo $row[2]; ?>'>
                                                <div class='message-container <?php echo ($from == $you) ? "right-message" : "left-message"; ?>' title='<?php echo $date; ?>'>
                                                    <div class='message-header'>
                                                        <div class='message-from'>
                                                            <?php echo ($from == $you) ? "YOU <span class='edit-btn' onclick='editGroupMessage(\"".$row[2]."\")' style='cursor:pointer;margin-left:10px;' title='Edit'>✏️</span> <span class='delete-btn' onclick='deleteGroupMessage(\"".$row[2]."\")' style='cursor:pointer;margin-left:5px;' title='Delete'>🗑️</span>" : "From: " . $from; ?>
                                                        </div>
                                                    </div>
                                                    <div class='message-text' id='msg-text-<?php echo $row[2]; ?>' data-raw='<?php echo htmlspecialchars(urldecode($messageitself), ENT_QUOTES); ?>'>
                                                        <?php echo nl2br(formatMessage((urldecode($messageitself)))); ?>
                                                    </div>
                                                </div>
                                            </div>
                                <?php
                                        }
                                        echo "
                                                    <script>
                                                    setTimeout(() => {
                                                        var box=document.getElementById('messages');
                                                        if (box) box.scrollTop=box.scrollHeight;
                                                    }, 100);
                                                    window.addEventListener('load', () => {
                                                        var box=document.getElementById('messages');
                                                        if (box) box.scrollTop=box.scrollHeight;
                                                    });
                                                    </script>";
                                    }
                                } catch (Exception $e) {
                                    echo "<script>window.location.href = '../';</script>";
                                    exit();
                                }
                                ?>
                            </div>
                        </div>
                        <div class="message-fields">
                            <div class="input-wrapper">
                                <textarea name="messageField" id="messageField" placeholder="Type a message..."></textarea>

                                <div class="input-actions">
                                    <button class="icon-btn" id="emojiButton" type="button">😊</button>
                                    <button class="icon-btn" id="attachButton" type="button">📎</button>
                                    <input type="file" id="chatFileInput" multiple style="display:none;" />
                                </div>
                            </div>
                            <button class="send-btn" name="sendButton" id="sendButton">
                                ➤
                            </button>
                        </div>
                        <h2 id="ErrorField" style="color:red"></h2>
                    </div>
                </div>
                
                <div id="emojiPickerContainer" style="display: none; position: absolute; bottom: 80px; right: 20px; z-index: 1000; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border-radius: 10px;">
                    <emoji-picker class="dark"></emoji-picker>
                </div>
        </center>
    </div>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script>
        $(document).ready(function() {
            var oldpos = "<?php echo "" . $lastmessageindex; ?>";
            var you = "<?php echo "" . $_SESSION["who"]; ?>";
            var group_id = "<?php echo "" . $_REQUEST["group_id"]; ?>";

            var wsToken = "<?php echo isset($_SESSION['ws_token']) ? $_SESSION['ws_token'] : ''; ?>";
            var protocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            var wsUrl = protocol + window.location.hostname + ':8080?token=' + encodeURIComponent(wsToken);
            ws = new WebSocket(wsUrl);

            ws.onmessage = function(event) {
                var res = JSON.parse(event.data);
                if (res.type === 'group' && res.tname === tname) {
                    let msg = document.getElementById("msg");
                    checkChatIfEmpty(msg);
                    var pos = res.pos;
                    var messageDate = res.date;
                    var mess = res.message;
                    var lastwho = res.lastwho;

                    // The server pushes new messages directly, so we always append them.
                    const messageElement = document.createElement("div");
                    messageElement.classList.add("message-container-outer");
                    messageElement.id = "msg-pos-" + pos;
                    messageElement.innerHTML = `
                        <div class='message-container ${(lastwho == you) ? "right-message" : "left-message"}' title='${messageDate}'>
                            <div class='message-header'>
                                <div class='message-from'>
                                        ${(lastwho == you) ? "YOU <span class='edit-btn' onclick='editGroupMessage(\""+pos+"\")' style='cursor:pointer;margin-left:10px;' title='Edit'>✏️</span> <span class='delete-btn' onclick='deleteGroupMessage(\""+pos+"\")' style='cursor:pointer;margin-left:5px;' title='Delete'>🗑️</span>" : "From: " + lastwho}
                                    </div>
                                </div>
                                <div class='message-text' id='msg-text-${pos}' data-raw='${res.rawMessage.replace(/'/g, "&#39;")}'>
                                    ${mess}
                                </div>
                            </div>
                        </div>
                    `;
                    msg.appendChild(messageElement);
                    const box = document.getElementById("messages");
                    box.scrollTop = box.scrollHeight;
                    oldpos = pos;
                } else if (res.type === 'message_deleted' && res.tname === tname) {
                    var el = document.getElementById("msg-pos-" + res.pos);
                    if (el) el.remove();
                    checkChatIfEmpty(document.getElementById("msg"));
                } else if (res.type === 'message_edited' && res.tname === tname) {
                    var textEl = document.getElementById("msg-text-" + res.pos);
                    if (textEl) {
                        textEl.innerHTML = res.message + " <span style='font-size:0.7em;opacity:0.7;'>(edited)</span>";
                        textEl.setAttribute("data-raw", res.rawMessage);
                        textEl.removeAttribute("data-editing");
                        textEl.removeAttribute("data-orig-html");
                    }
                } else if (res.type === 'group_destroyed' && res.tname === tname) {
                    alert("This group has been destroyed by the admin.");
                    window.location.href = "../";
                } else if (res.type === 'user_kicked' && res.tname === tname) {
                    alert("You have been kicked from this group.");
                    window.location.href = "../";
                }
            };

            window.editGroupMessage = function(pos) {
                const textEl = document.getElementById("msg-text-" + pos);
                if (!textEl) return;
                if (textEl.getAttribute("data-editing") === "true") return;
                textEl.setAttribute("data-editing", "true");
                
                let rawText = textEl.getAttribute("data-raw") || "";
                
                const fileRegex = /\[FILE:\s*(.+?):\s*(.+?)\]/g;
                const fileTags = [];
                let match;
                while ((match = fileRegex.exec(rawText)) !== null) {
                    fileTags.push(match[0]);
                }
                
                rawText = rawText.replace(fileRegex, "").trim();
                
                if (fileTags.length > 0) {
                    textEl.setAttribute("data-files", fileTags.join("\n"));
                }
                
                const originalHTML = textEl.innerHTML;
                textEl.setAttribute("data-orig-html", originalHTML);
                
                const html = `
                    <textarea id="edit-box-${pos}" class="edit-textarea" style="width:100%;resize:vertical;min-height:40px;color:black;">${rawText}</textarea>
                    <div style="margin-top:5px;text-align:right;">
                        <button onclick="saveEditGroup('${pos}')" style="padding:2px 10px;cursor:pointer;">Save</button>
                        <button onclick="cancelEditGroup('${pos}')" style="padding:2px 10px;cursor:pointer;">Cancel</button>
                    </div>
                `;
                textEl.innerHTML = html;
            };

            window.cancelEditGroup = function(pos) {
                const textEl = document.getElementById("msg-text-" + pos);
                if (!textEl) return;
                textEl.innerHTML = textEl.getAttribute("data-orig-html");
                textEl.removeAttribute("data-editing");
                textEl.removeAttribute("data-files");
            };

            // Emoji Picker Logic
            const emojiButton = document.getElementById('emojiButton');
            const emojiPickerContainer = document.getElementById('emojiPickerContainer');
            const picker = document.querySelector('emoji-picker');
            const messageField = document.getElementById('messageField');

            // Sync picker theme with the current theme
            if (currentTheme === 'light') {
                picker.classList.remove('dark');
                picker.classList.add('light');
            } else {
                picker.classList.remove('light');
                picker.classList.add('dark');
            }

            emojiButton.addEventListener('click', () => {
                if (emojiPickerContainer.style.display === 'none') {
                    emojiPickerContainer.style.display = 'block';
                } else {
                    emojiPickerContainer.style.display = 'none';
                }
            });

            picker.addEventListener('emoji-click', event => {
                messageField.value += event.detail.unicode;
                messageField.focus();
            });

            // Hide picker if clicking outside
            document.addEventListener('click', event => {
                if (!emojiPickerContainer.contains(event.target) && !emojiButton.contains(event.target)) {
                    emojiPickerContainer.style.display = 'none';
                }
            });

            window.saveEditGroup = function(pos) {
                const textEl = document.getElementById("msg-text-" + pos);
                if (!textEl) return;
                const box = document.getElementById("edit-box-" + pos);
                if (!box) return;
                let newText = box.value.trim();
                
                const fileTags = textEl.getAttribute("data-files");
                if (fileTags) {
                    newText = newText ? (newText + "\n" + fileTags) : fileTags;
                }
                
                ws.send(JSON.stringify({
                    type: 'edit_group_message',
                    tname: tname,
                    pos: pos,
                    new_message: encodeURIComponent(newText)
                }));
            };

            window.deleteGroupMessage = function(pos) {
                if (!confirm("Are you sure you want to delete this message?")) return;
                ws.send(JSON.stringify({
                    type: 'delete_group_message',
                    tname: tname,
                    pos: pos
                }));
            };

            ws.onerror = function() {
                document.getElementById("ErrorField").innerHTML = "WebSocket Connection Error. Please ensure the server is running.";
            };

            // File Attachment Logic
            let pendingFiles = [];
            const attachButton = document.getElementById('attachButton');
            const chatFileInput = document.getElementById('chatFileInput');

            attachButton.addEventListener('click', () => {
                chatFileInput.click();
            });

            chatFileInput.addEventListener('change', () => {
                const files = Array.from(chatFileInput.files);
                if (files.length === 0) {
                    pendingFiles = [];
                    attachButton.innerHTML = "📎";
                    attachButton.style.color = "";
                    return;
                }
                pendingFiles = files;
                attachButton.innerHTML = `📎 (${files.length})`;
                attachButton.style.color = "#22c55e"; // Green to indicate attached
            });

            $("#sendButton").click(async function() {
                var message = "" + document.getElementById("messageField").value;
                if (message.trim() === "" && pendingFiles.length === 0) return;
                
                document.getElementById("sendButton").disabled = true;

                let fileTags = [];
                if (pendingFiles.length > 0) {
                    for (let i = 0; i < pendingFiles.length; i++) {
                        const file = pendingFiles[i];
                        const formData = new FormData();
                        formData.append('chat_file', file);

                        try {
                            const response = await fetch('../../uploadChatFile.php', {
                                method: 'POST',
                                body: formData
                            });
                            
                            const result = await response.json();
                            if (result.success) {
                                fileTags.push(`[FILE:${result.path}:${result.original_name}]`);
                            } else {
                                document.getElementById("ErrorField").innerHTML = result.error || `Failed to upload ${file.name}.`;
                                document.getElementById("sendButton").disabled = false;
                                return;
                            }
                        } catch (e) {
                            document.getElementById("ErrorField").innerHTML = `Error uploading ${file.name}.`;
                            document.getElementById("sendButton").disabled = false;
                            return;
                        }
                    }
                }

                let finalMessage = message;
                if (fileTags.length > 0) {
                    const allTags = fileTags.join("\n");
                    finalMessage = finalMessage ? (finalMessage + "\n" + allTags) : allTags;
                }

                var messageEncoded = encodeURIComponent(finalMessage);

                var payload = {
                    type: 'group',
                    groupId: group_id,
                    tname: tname,
                    message: messageEncoded
                };

                if (ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify(payload));
                    document.getElementById("messageField").value = "";
                    document.getElementById("ErrorField").innerHTML = "";
                    
                    pendingFiles = [];
                    chatFileInput.value = '';
                    attachButton.innerHTML = "📎";
                    attachButton.style.color = "";
                } else {
                    document.getElementById("ErrorField").innerHTML = "WebSocket is not connected.";
                }
                
                document.getElementById("sendButton").disabled = false;
            });
        });
    </script>
    <div id="destroyGroupModal" class="custom-modal">
        <div class="custom-modal-content">
            <h3 style="color:#fff; margin-bottom: 20px;">Confirm Destruction</h3>
            <p style="color:#ccc; margin-bottom: 30px;">Are you sure you want to destroy this group? This action cannot be undone.</p>
            <div class="custom-modal-actions">
                <button onclick="closeDestroyModal()" class="btn-cancel">Cancel</button>
                <button id="confirmDestroyBtn" class="btn-confirm">Destroy</button>
            </div>
        </div>
    </div>
</body>

</html>

<?php
if (isset($_POST) && isset($_POST["changePicButton"])) {
    if ($_FILES['pic']['name'] != "") {
        $name = $_FILES["pic"]["name"];
        $type = substr($name, strlen($name) - 3, strlen($name) - 1);
        if ($type == "png") {
            define('MB', 1024000);
            $size = round((($_FILES["pic"]["size"]) / MB), 0);
            if ($size <= 1) {
                $tmp_name = $_FILES["pic"]["tmp_name"];
                $path = "../../Extra/styles/images/groups_images/i" . $_REQUEST["group_id"] . ".png";
                if (move_uploaded_file($tmp_name, $path)) {
                    echo
                    "
                        <script>
                            alert('Successfully Changed');
                        </script>
                        ";
                } else {
                    echo
                    "
                        <script>
                            alert('Error');
                        </script>
                        ";
                }
            } else {
                echo
                "
                        <script>
                            alert('Size is bigger than 1 MB');
                        </script>
                        ";
            }
        } else {
            echo
            "
                        <script>
                            alert('Type is not PNG');
                        </script>
                        ";
        }
    } else {
        echo
        "
                        <script>
                            alert('Invalid');
                        </script>
                        ";
    }
}
?>