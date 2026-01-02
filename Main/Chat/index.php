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
                                <div id="ava" style='border-radius:50%;background-color:red;width:10px;height:10px'></div>
                            </td>
                            <td>
                                <?php
                                if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                                    echo "<img src='../View_Image/?u=" . $_REQUEST["with"] . "' width='50px' height='50px' style='border-radius:50%'/>";
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
                    <table>
                        <tr>
                            <td colspan="2">
                                <div id="messages" class="at-top" onscroll="showBlurOnTop()">
                                    <div class="blur-top"></div>
                                    <table id="msg" style="width:100%">
                                        <?php
                                        require_once("../../DB.php");
                                        function getname($you, $withwho, $conn)
                                        {
                                            $query = "SELECT user1 FROM friends WHERE BINARY user1='" . $you . "' AND BINARY user2='" . $withwho . "' OR BINARY user1='" . $withwho . "' AND BINARY user2='" . $you . "'";
                                            $result = mysqli_query($conn, $query);
                                            $after = "";
                                            $res = "";
                                            if ($result) {
                                                if (mysqli_num_rows($result)) {
                                                    $row = mysqli_fetch_row($result);
                                                    $after .= $row[0];
                                                }
                                                if ($after == $you) {
                                                    $res .= $you . "" . $withwho;
                                                } else {
                                                    $res .= $withwho . "" . $you;
                                                }
                                            }
                                            return $res;
                                        }
                                        $you = $_SESSION["who"];
                                        if (isset($_REQUEST) && isset($_REQUEST["with"])) {
                                            $withwho = $_REQUEST["with"];
                                        }

                                        $tablename = getname($you, $withwho, $conn);
                                        $tname = $tablename;


                                        $query = "SELECT * FROM " . $tablename . "";
                                        $result = mysqli_query($conn, $query);
                                        if ($result) {
                                            if (mysqli_num_rows($result)) {
                                                while ($row = mysqli_fetch_row($result)) {
                                                    $from = "" . $row[0];
                                                    $messageitself = "" . $row[1];
                                                    $lastmessageindex++;
                                                    $date = "" . $row[3];
                                                    $OddOrEvenMessage = ($lastmessageindex % 2 == 0) ? "Odd" : "Even";
                                                    if ($from == $you) {
                                                        echo "
                                                            <tr style='text-align:right' class='chat".$OddOrEvenMessage."Message'>
                                                            <td class='messageDate'> " . $date . " </td>
                                                            <td style='color:gold'> YOU </td>
                                                            </tr>
                                                            <tr class='chat".$OddOrEvenMessage."Message'>
                                                            <td style='color:white;' colspan='2'><p style='inline-size: 150px;overflow-wrap: break-word;text-align:right;float:right;'>
                                                                " . nl2br(formatMessage(htmlspecialchars(urldecode($messageitself)))) . "
                                                                </p>
                                                            </td>
                                                            </tr>
                                                        ";
                                                    } else {
                                                        echo "
                                                            <tr style='text-align:left' class='chat".$OddOrEvenMessage."Message'>
                                                            <td style='color:yellow'>From :  " . $from . " </td>
                                                            <td class='messageDate'> " . $date . " </td>
                                                            </tr>
                                                            <tr class='chat".$OddOrEvenMessage."Message'>
                                                            <td style='color:white' colspan='2'><p style='inline-size: 150px;overflow-wrap: break-word;text-align:left;'>
                                                                " . nl2br(formatMessage(htmlspecialchars(urldecode($messageitself)))) . "
                                                                </p>
                                                            </td>
                                                            </tr>
                                                        ";
                                                    }
                                                }
                                                echo "
                                                <script>
                                                var box=document.getElementById('messages');
                                                box.scrollTop=box.scrollHeight;
                                                </script>";
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea name="messageField" id="messageField" placeholder="Type A Message.." class="messageField"></textarea>
                            </td>
                            <td>
                                <input name="sendButton" id="sendButton" type="button" value="Send" class="buttontag" style="background-color:red" />
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <h2 id="ErrorField" style="color:red">
                                </h2>
                            </th>
                        </tr>
                    </table>
                </div>
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

                var messageEncoded=encodeURIComponent(message);

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
                            var errorField=document.getElementById("ErrorField");
                            errorField.innerHTML="";
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
                    if (this.readyState == 4 && this.status == 200) {
                        var res = this.responseText;
                        if (res != "nomore" && res != "error" && !res.includes("*")) {
                            var arr = JSON.parse(res);
                            var ava = arr.ava;
                            var pos = arr.pos;
                            var messageDate = arr.date;
                            var mess = arr.message;
                            var lastwho = arr.lastwho;
                            if (ava == "1") {
                                $("#ava").css({
                                    "background-color": "lightgreen"
                                });
                            } else {
                                $("#ava").css({
                                    "background-color": "red"
                                });
                            }
                            var you = "<?php if (isset($_SESSION) && isset($_SESSION["who"])) {echo "" . $_SESSION["who"];} ?>";
                            if (oldpos != pos) {
                                var newrow1 = document.createElement("tr");
                                var newrow2 = document.createElement("tr");
                                const newMessageOddOrEven = ( oldpos % 2 === 0 ) ? "Even" : "Odd";
                                newrow1.classList.add("chat" + newMessageOddOrEven + "Message");
                                newrow2.classList.add("chat" + newMessageOddOrEven + "Message");
                                if (lastwho == you) {
                                    newrow1.setAttribute("style", "text-align:right");
                                    newrow1.innerHTML = "<td class='messageDate'> " + messageDate + " </td><td style='color:gold'> YOU </td>";
                                    newrow2.innerHTML = "<td style='color:white' colspan='2'><p style='inline-size: 150px;overflow-wrap: break-word;text-align:right;float:right;'>" + mess + "</p></td>";
                                } else {
                                    newrow1.setAttribute("style", "text-align:left");
                                    newrow1.innerHTML = "<td style='color:yellow'>From : " + lastwho + "</td><td class='messageDate'> " + messageDate + " </td>";
                                    newrow2.innerHTML = "<td style='color:white' colspan='2'><p style='inline-size: 150px;overflow-wrap: break-word;text-align:left;'>" + mess + "</p></td>";
                                }
                                var msg = document.getElementById("msg");
                                msg.append(newrow1);
                                msg.append(newrow2);
                                var box = document.getElementById("messages");
                                box.scrollTop = box.scrollHeight;
                                oldpos++;
                            }
                        } else {
                            if (res == "nomore") {
                                var newrow1 = document.createElement("tr");
                                newrow1.setAttribute("style", "text-align:center");
                                newrow1.innerHTML = "<td style='color:red'><h1>Not A Friend</h1></td>";
                                var msg = document.getElementById("msg");
                                msg.append(newrow1);
                                clearInterval(loo);
                            } else if (res == "error") {
                                var newrow1 = document.createElement("tr");
                                newrow1.setAttribute("style", "text-align:center");
                                newrow1.innerHTML = "<td style='color:red'><h1>Connection Error</h1></td>";
                                var msg = document.getElementById("msg");
                                msg.append(newrow1);
                                clearInterval(loo);
                            } else {
                                var all = this.responseText.split("*");
                                var ava = all[1];
                                if (ava == "1") {
                                    $("#ava").css({
                                        "background-color": "lightgreen"
                                    });
                                } else {
                                    $("#ava").css({
                                        "background-color": "red"
                                    });
                                }
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