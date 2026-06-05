<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["with"]))) {
    header("Location:../../");
} else {
    $lastmessageindex = 0;
    $tname = "";
}

require_once(dirname(__DIR__, 2) . '/common.php');
require_once(dirname(__DIR__, 2) . '/ws_auth.php');

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/x-icon" href="../../Extra/images/favicon.ico">
    <title>
        Chat
    </title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="<?= asset('../../Extra/styles/cssFiles/themes.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            // We'll also add it to body once it's parsed
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('light-mode');
            });
        }
    </script>
</head>

<body>
    <div class="mobile-back-btn" style="position: absolute; top: 20px; left: 20px; z-index: 1000;">
        <a href="../" class="backButton" style="text-decoration:none; font-size:1.5rem; color: #F76D57; font-weight:bold; background:rgba(255,255,255,0.1); padding:10px 20px; border-radius:20px; backdrop-filter:blur(5px); transition:all 0.3s ease;">🔙 Back</a>
    </div>
    <center>
        <div class="main_chat_box">
            <div class="main_chat_inner_box">
                <div class="header">
                    <?php
                    if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                        echo "<img src='../View_Image/?u=" . $_REQUEST["with"] . "' id='ava' width='50px' height='50px' style='border-radius:50%; border:solid 2px #F76D57; box-shadow:0 2px 10px rgba(247,109,87,0.3);'/>";
                    }
                    ?>
                    <h2 style="margin:0; font-size:1.5rem;">
                        <?php
                        if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                            echo htmlspecialchars($_REQUEST["with"]);
                        }
                        ?>
                    </h2>
                </div>
                <div id="chatBox">
                    <div id="messages" class="at-top" onscroll="showBlurOnTop()">
                        <div class="blur-top"></div>
                        <div id="msg">
                            <?php
                            require_once("../../DB.php");
                            function getname($you, $withwho, $conn)
                            {
                                $query = "SELECT user1 FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, "ssss", $you, $withwho, $withwho, $you);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $after = "";
                                $res = "";
                                if (mysqli_num_rows($result)) {
                                    $row = mysqli_fetch_row($result);
                                    $after .= $row[0];
                                }
                                if ($after == $you) {
                                    $res .= $you . "" . $withwho;
                                } else {
                                    $res .= $withwho . "" . $you;
                                }
                                return $res;
                            }
                            $you = $_SESSION["who"];
                            if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                                $withwho = $_REQUEST["with"];
                            }

                            try {
                                $tablename = getname($you, $withwho, $conn);
                                $tname = $tablename;

                                $query = "SELECT * FROM " . $tablename . "";
                                $stmt = mysqli_prepare($conn, $query);

                                if ($stmt === false) {
                                    echo "<script>window.location.href = '../';</script>";
                                    exit();
                                }

                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                if (mysqli_num_rows($result)) {
                                    while ($row = mysqli_fetch_row($result)) {
                                        $from = "" . $row[0];
                                        $messageitself = "" . $row[1];
                                        $lastmessageindex++;
                                        $date = "" . $row[3];
                            ?>
                                        <div class='message-container-outer' id='msg-pos-<?php echo $row[2]; ?>'>
                                            <div class='message-container <?php echo ($from == $you) ? "right-message" : "left-message"; ?>' title='<?php echo $date; ?>'>
                                                <div class='message-header'>
                                                    <div class='message-from'>
                                                        <?php echo ($from == $you) ? "YOU" : "From: " . $from; ?>
                                                    </div>
                                                    <div class="message-actions">
                                                        <div class="reaction-picker">
                                                            <span class="action-icon" onclick="toggleReaction('<?php echo $row[2]; ?>', '😂')">😂</span>
                                                            <span class="action-icon" onclick="toggleReaction('<?php echo $row[2]; ?>', '😭')">😭</span>
                                                            <span class="action-icon" onclick="toggleReaction('<?php echo $row[2]; ?>', '😡')">😡</span>
                                                            <span class="action-icon" onclick="toggleReaction('<?php echo $row[2]; ?>', '👍')">👍</span>
                                                        </div>
                                                        <?php if ($from == $you) { ?>
                                                        <div class="edit-delete-picker" style="display: flex; gap: 2px;">
                                                            <span class="action-icon" onclick="editPersonalMessage('<?php echo $row[2]; ?>')" title="Edit">✏️</span>
                                                            <span class="action-icon" onclick="deletePersonalMessage('<?php echo $row[2]; ?>')" title="Delete">🗑️</span>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class='message-text' id='msg-text-<?php echo $row[2]; ?>' data-raw='<?php echo htmlspecialchars(urldecode($messageitself), ENT_QUOTES); ?>'>
                                                    <?php echo nl2br(formatMessage((urldecode($messageitself)))); ?>
                                                </div>
                                                <div class="reactions-bar" id="reactions-<?php echo $row[2]; ?>">
                                                    <?php
                                                        $reactionsJson = isset($row[4]) ? $row[4] : null;
                                                        if ($reactionsJson) {
                                                            $reactionsObj = json_decode($reactionsJson, true);
                                                            if ($reactionsObj) {
                                                                foreach ($reactionsObj as $emoji => $users) {
                                                                    $count = count($users);
                                                                    $userList = htmlspecialchars(implode(", ", $users));
                                                                    echo "<span class='reaction-badge' title='{$userList}' onclick='toggleReaction(\"{$row[2]}\", \"{$emoji}\")'>{$emoji} {$count}</span>";
                                                                }
                                                            }
                                                        }
                                                    ?>
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
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script>
        function escapeHtml(unsafe) {
            return (unsafe || "").toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        $(document).ready(function() {
            var oldpos = "<?php echo "" . $lastmessageindex; ?>";
            var you = "<?php if (isset($_SESSION) && isset($_SESSION["who"])) { echo "" . $_SESSION["who"]; } ?>";
            var towho = "<?php echo "" . $_REQUEST["with"]; ?>";
            var tname = "<?php echo "" . $tname; ?>";

            var wsToken = "<?php echo isset($_SESSION['ws_token']) ? $_SESSION['ws_token'] : ''; ?>";
            var protocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
            var wsHost = "<?php echo getVarFromEnv('WS_URL') ?: (isset($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST'])[0] . ':8080' : 'localhost:8080'); ?>";
            var wsUrl = protocol + wsHost + '?token=' + encodeURIComponent(wsToken);
            var ws = new WebSocket(wsUrl);

            ws.onopen = function() {
                ws.send(JSON.stringify({
                    type: 'check_status',
                    target: towho
                }));
            };

            ws.onmessage = function(event) {
                var res = JSON.parse(event.data);
                if (res.type === 'personal' && res.tname === tname) {
                    let msg = document.getElementById("msg");
                    checkChatIfEmpty(msg);
                    var ava = res.ava;
                    var pos = res.pos;
                    var messageDate = res.date;
                    var mess = res.message;
                    var lastwho = res.lastwho;
                    let isMe = (res.lastwho == you);

                    $("#ava").css({
                        "border-color": `${ava == "1" ? "green" : "red"}`
                    });

                    // The server pushes new messages directly, so we always append them.
                    const messageElement = document.createElement("div");
                    messageElement.classList.add("message-container-outer");
                    messageElement.id = "msg-pos-" + res.pos;
                    messageElement.innerHTML = `
                        <div class='message-container ${isMe ? "right-message" : "left-message"}' title='${res.date}'>
                            <div class='message-header'>
                                <div class='message-from'>
                                    ${isMe ? "YOU" : "From: " + escapeHtml(res.lastwho)}
                                </div>
                                <div class="message-actions">
                                    <div class="reaction-picker">
                                        <span class="action-icon" onclick="toggleReaction('${res.pos}', '😂')">😂</span>
                                        <span class="action-icon" onclick="toggleReaction('${res.pos}', '😭')">😭</span>
                                        <span class="action-icon" onclick="toggleReaction('${res.pos}', '😡')">😡</span>
                                        <span class="action-icon" onclick="toggleReaction('${res.pos}', '👍')">👍</span>
                                    </div>
                                    ${isMe ? `
                                    <div class="edit-delete-picker" style="display: flex; gap: 2px;">
                                        <span class="action-icon" onclick="editPersonalMessage('${res.pos}')" title="Edit">✏️</span>
                                        <span class="action-icon" onclick="deletePersonalMessage('${res.pos}')" title="Delete">🗑️</span>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                            <div class='message-text' id='msg-text-${res.pos}' data-raw='${res.rawMessage.replace(/'/g, "&#39;")}'>
                                ${res.message}
                            </div>
                            <div class="reactions-bar" id="reactions-${res.pos}"></div>
                        </div>
                    `;
                    msg.appendChild(messageElement);
                    const box = document.getElementById("messages");
                    box.scrollTop = box.scrollHeight;
                    oldpos = pos;
                } else if (res.type === 'status_change' && res.username === towho) {
                    $("#ava").css({
                        "border-color": `${res.ava == "1" ? "green" : "red"}`
                    });
                } else if (res.type === 'reaction_updated' && res.tname === tname) {
                    var reactBar = document.getElementById("reactions-" + res.pos);
                    if (reactBar) {
                        reactBar.innerHTML = "";
                        for (const emoji in res.reactions) {
                            const users = res.reactions[emoji];
                            const count = users.length;
                            const userList = escapeHtml(users.join(", "));
                            reactBar.innerHTML += `<span class='reaction-badge' title='${userList}' onclick='toggleReaction("${res.pos}", "${emoji}")'>${emoji} ${count}</span>`;
                        }
                    }
                } else if (res.type === 'message_deleted' && res.tname === tname) {
                    const msgEl = document.getElementById("msg-pos-" + res.pos);
                    if (msgEl) msgEl.remove();
                } else if (res.type === 'message_edited' && res.tname === tname) {
                    const msgEl = document.getElementById("msg-text-" + res.pos);
                    if (msgEl) {
                        msgEl.innerHTML = res.message + " <span style='font-size:0.7em;opacity:0.7;'>(edited)</span>";
                        msgEl.setAttribute("data-raw", res.rawMessage);
                        msgEl.removeAttribute("data-editing");
                    }
                } else if (res.type === 'friend_deleted') {
                    if (res.from === towho) {
                        alert("You have been removed from this friend's list.");
                        window.location.href = "../";
                    }
                }
            };

            window.editPersonalMessage = function(pos) {
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
                    <textarea id="edit-box-${pos}" class="edit-textarea">${rawText}</textarea>
                    <div class="edit-actions">
                        <button onclick="cancelEditPersonal('${pos}')" class="btn-cancel-edit">Cancel</button>
                        <button onclick="saveEditPersonal('${pos}')" class="btn-save-edit">Save</button>
                    </div>
                `;
                textEl.innerHTML = html;
            };

            window.cancelEditPersonal = function(pos) {
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

            window.saveEditPersonal = function(pos) {
                const textEl = document.getElementById("msg-text-" + pos);
                if (!textEl) return;
                const box = document.getElementById("edit-box-" + pos);
                if (!box) return;
                let newText = box.value.trim();
                
                const fileTags = textEl.getAttribute("data-files");
                if (fileTags) {
                    newText = newText ? (newText + "\n" + fileTags) : fileTags;
                }
                
                if (newText === "") {
                    alert("Message cannot be empty.");
                    return;
                }
                
                ws.send(JSON.stringify({
                    type: 'edit_personal_message',
                    tname: tname,
                    to: towho,
                    pos: pos,
                    new_message: encodeURIComponent(newText)
                }));
            };

            window.toggleReaction = function(pos, emoji) {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({
                        type: 'toggle_reaction',
                        tname: tname,
                        pos: pos,
                        reaction: emoji,
                        isGroup: false,
                        towho: towho
                    }));
                }
            };

            let pendingDeletePos = null;

            window.deletePersonalMessage = function(pos) {
                pendingDeletePos = pos;
                $("#deleteMessageModal").css("display", "flex");
                setTimeout(() => $("#deleteMessageModal").addClass("show"), 10);
            };

            window.closeDeleteMessageModal = function() {
                $("#deleteMessageModal").removeClass("show");
                setTimeout(() => {
                    $("#deleteMessageModal").css("display", "none");
                    pendingDeletePos = null;
                }, 300);
            };

            window.confirmDeleteMessage = function() {
                if (pendingDeletePos !== null) {
                    ws.send(JSON.stringify({
                        type: 'delete_personal_message',
                        tname: tname,
                        to: towho,
                        pos: pendingDeletePos
                    }));
                    closeDeleteMessageModal();
                }
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
                    type: 'personal',
                    to: towho,
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
    <div id="deleteMessageModal" class="custom-modal">
        <div class="custom-modal-content">
            <h3 style="color:#fff; margin-bottom: 20px;">Confirm Deletion</h3>
            <p style="color:#ccc; margin-bottom: 30px;">Are you sure you want to delete this message?</p>
            <div class="custom-modal-actions">
                <button onclick="closeDeleteMessageModal()" class="btn-cancel">Cancel</button>
                <button onclick="confirmDeleteMessage()" class="btn-confirm">Delete</button>
            </div>
        </div>
    </div>
</body>
</html>