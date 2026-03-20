<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["with"]))) {
    header("Location:../../");
} else {
    $lastmessageindex = 0;
    $tname = "";
}

require_once(dirname(__DIR__, 2) . '/common.php');

?>
<!DOCTYPE html>
<html>

<head>
    <title>
        Chat
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
</head>

<body>
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
                                if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                                    echo "<img src='../View_Image/?u=" . $_REQUEST["with"] . "' id='ava' width='50px' height='50px' style='border-radius:50%;border:solid 4px red'/>";
                                }
                                ?>
                            </td>
                            <td>
                                <h2 style="color:white">
                                    <?php
                                    if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                                        echo "" . $_REQUEST["with"];
                                    }
                                    ?>
                                </h2>
                            </td>
                        </tr>
                    </table>
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
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script>
        $(document).ready(function() {
            var oldpos = "<?php echo "" . $lastmessageindex; ?>";
            $("#sendButton").click(function() {
                var message = "" + document.getElementById("messageField").value;
                var towho = "<?php echo "" . $_REQUEST["with"]; ?>";
                var tname = "<?php echo "" . $tname; ?>";

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
                        if (res != "nomore" && res != "error" && !res.includes("*")) {
                            var arr = JSON.parse(res);
                            var ava = arr.ava;
                            var pos = arr.pos;
                            var messageDate = arr.date;
                            var mess = arr.message;
                            var lastwho = arr.lastwho;
                            $("#ava").css({
                                "border-color": `${ava == "1" ? "green" : "red"}`
                            });
                            var you = "<?php if (isset($_SESSION) && isset($_SESSION["who"])) {
                                            echo "" . $_SESSION["who"];
                                        } ?>";
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
                            } else if (res.includes("nomore*")) {
                                msg.innerHTML = "<div style='color:red'><h1>No Messages</h1></div>";
                                $("#ava").css({
                                    "border-color": `${res.split("*")[1] == "1" ? "green" : "red"}`
                                });
                            } else {
                                clearInterval(loo);
                                window.location.replace('../');
                            }
                        }
                    }
                };
                var withwho = "<?php if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                                    echo "" . $_REQUEST["with"];
                                } ?>";
                var tname = "<?php echo "" . $tname; ?>";
                xmlhttp.open("GET", "../../syncChat.php?f=" + withwho + "&tname=" + tname, true);
                xmlhttp.send();
            }, 1000);
        });
    </script>
</body>

</html>