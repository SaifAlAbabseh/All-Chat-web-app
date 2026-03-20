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
    <style>
        body {
            background-color: rgb(247, 247, 247);
        }

        body,
        html {
            height: 100%;
        }
    </style>
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script>
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
    </script>
</head>

<body>
    <?php

    if ($check_result_row[1] == $_SESSION["who"]) {
        echo
        "
            <div id='groupEditBox' class='friendsListBox'>
                <button onclick='editExitDialog()' id='exit'>X</button>
                <center>
                    <table style='width:100%'>
                        <caption style='color:red'>
                            Image should be at most 1MB and should be of type PNG
                        </caption>
                        <form action='' method='post' enctype='multipart/form-data'>
                            <tbody class='changeBox'>
                                <tr>
                                    <td id='imageRenderOuterBox'>
                                        <input type='file' style='width:60%;font-size:1rem;' onchange='renderImage()' name='pic' class='inputfield' id='picField' required />
                                        <br>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class='footOfPic' style='background:red'>
                                <tr>
                                    <td>
                                        <input type='submit' value='Change' class='buttontag' name='changePicButton' />
                                    </td>
                                </tr>
                            </tfoot>
                        </form>
                    </table>
                </center>
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
                <a href='Destroy_Group/?group_id=" . $_REQUEST["group_id"] . "' class='link leaveGroupLink'>Destroy Group</a>
                &nbsp;
                <a href='#' class='link leaveGroupLink' onclick='showEditGroupPicBox()'>Edit Picture</a>
                ";
            } else {
                echo "<a href='Leave_Group/?group_id=" . $_REQUEST["group_id"] . "' class='link leaveGroupLink'>Leave Group</a>";
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
        <div class="backButton">
            <a href="../"><img class="backbuttonlink" src="../../Extra/styles/images/backButton.png" alt="Back Button" width="50px" height="50px" /></a>
        </div>
        <center>
            <div class="main_chat_box">
                <div class="main_chat_inner_box">
                    <div class="header">
                        <table style="background-color:#034780">
                            <tr>
                                <td>
                                    <?php
                                    echo "<img src='../View_Group_Image/?group_id=" . $_REQUEST["group_id"] . "' width='50px' height='50px' style='border-radius:50%'/>";
                                    ?>
                                </td>
                                <td>
                                    <h2 style="color:white">
                                        <?php
                                        echo $check_result_row[0];
                                        ?>
                                    </h2>
                                </td>
                                <td>
                                    <a onclick="showGroupInfoBox()" title="Group Info" class="settingsIconClickable" href="#"><img class="settingsIcon" src="../../Extra/styles/images/settings.png" alt="settings icon"></a>
                                </td>
                                <?php

                                if ($check_result_row[1] == $_SESSION["who"]) {
                                    echo "<td><button onclick='showAddMemberBox()' title='Add New Member' id='add_member_button'>+</button></td>";
                                }

                                ?>
                            </tr>
                        </table>
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

                                            <div class='message-container-outer'>
                                                <div class='message-container <?php echo ($from == $you) ? "right-message" : "left-message"; ?>' title='<?php echo $date; ?>'>
                                                    <div class='message-header'>
                                                        <div class='message-from'>
                                                            <?php echo ($from == $you) ? "YOU" : "From: " . $from; ?>
                                                        </div>
                                                    </div>
                                                    <div class='message-text'>
                                                        <?php echo nl2br(formatMessage((urldecode($messageitself)))); ?>
                                                    </div>
                                                </div>
                                            </div>
                                <?php
                                        }
                                        echo "
                                                    <script>
                                                    var box=document.getElementById('messages');
                                                    box.scrollTop=box.scrollHeight;
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
                                    <button class="icon-btn">😊</button>
                                    <button class="icon-btn">📎</button>
                                </div>
                            </div>
                            <button class="send-btn" name="sendButton" id="sendButton">
                                ➤
                            </button>
                        </div>
                        <h2 id="ErrorField" style="color:red"></h2>
                    </div>
                </div>
        </center>
    </div>
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script>
        $(document).ready(function() {
            var oldpos = "<?php echo "" . $lastmessageindex; ?>";
            $("#sendButton").click(function() {
                var message = "" + document.getElementById("messageField").value;
                var tname = "<?php echo "" . $group_table_id; ?>";

                var messageEncoded = encodeURIComponent(message);

                var xmlhttp2;
                if (XMLHttpRequest) {
                    xmlhttp2 = new XMLHttpRequest();
                } else {
                    xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp2.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var res = "" + this.responseText;
                        if (res != "ok") {
                            var errorField = document.getElementById("ErrorField");
                            errorField.innerHTML = "" + res;
                        } else {
                            var messageField = document.getElementById("messageField");
                            messageField.value = "";
                            var errorField = document.getElementById("ErrorField");
                            errorField.innerHTML = "";
                        }
                    }
                }
                xmlhttp2.open("GET", "../../sendMessage.php?message=" + messageEncoded + "&lastI=" + oldpos + "&tname=" + tname, true);
                xmlhttp2.send();
            });
            var loo = setInterval(function() {
                var xmlhttp;
                if (XMLHttpRequest) {
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    let msg = document.getElementById("msg");
                    if (this.readyState == 4 && this.status == 200) {
                        var res = this.responseText;
                        if (res != "nomore" && res != "error" && res != "empty") {
                            checkChatIfEmpty(msg);
                            var arr = JSON.parse(res);
                            var pos = arr.pos;
                            var messageDate = arr.date;
                            var mess = arr.message;
                            var lastwho = arr.lastwho;
                            var you = "<?php echo "" . $_SESSION["who"]; ?>";
                            if (oldpos != pos) {
                                const messageElement = document.createElement("div");
                                messageElement.classList.add("message-container-outer");
                                messageElement.innerHTML = `
                                    <div class='message-container ${(lastwho == you) ? "right-message" : "left-message"}' title='${messageDate}'>
                                        <div class='message-header'>
                                            <div class='message-from'>
                                                    ${(lastwho == you) ? "YOU" : "From: " + lastwho}
                                                </div>
                                            </div>
                                            <div class='message-text'>
                                                ${mess}
                                            </div>
                                        </div>
                                    </div>
                                `;
                                msg.appendChild(messageElement);
                                const box = document.getElementById("messages");
                                box.scrollTop = box.scrollHeight;
                                oldpos++;
                            }
                        } else {
                            if (res == "nomore") {
                                msg.innerHTML = "<div style='color:red'><h1>Not A Friend</h1></div>";
                            } else if (res == "error") {
                                msg.innerHTML = "<div style='color:red'><h1>Connection Error</h1></div>";
                            } else if (res == "empty") {
                                msg.innerHTML = "<div style='color:red'><h1>No Messages</h1></div>";
                            } else {
                                clearInterval(loo);
                                window.location.replace('../');
                            }
                        }
                    }
                };
                var group_id = "<?php echo "" . $_REQUEST["group_id"]; ?>";
                xmlhttp.open("GET", "../../syncGroupChat.php?group_id=" + group_id, true);
                xmlhttp.send();
            }, 1000);
        });
    </script>
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