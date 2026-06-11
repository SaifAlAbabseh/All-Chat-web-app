<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../../");
}
require_once(dirname(__DIR__, 2) . '/common.php');
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/x-icon" href="../../Extra/images/favicon.ico">
    <title>
        All Chat | Add Friend
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="<?= asset('../../Extra/styles/cssFiles/themes.css') ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?= asset('../../scripts/commonMethods.js') ?>"></script>
    <script src="<?= asset('../../scripts/add_friend.js') ?>"></script>
    <style>
        body,
        html {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: sans-serif;
        }
        #whole_box {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .modern-add-outer {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            z-index: 10;
            box-sizing: border-box;
        }
        .modern-add-box {
            width: 100%;
            height: auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            box-sizing: border-box;
        }
        body.light-mode .modern-add-box {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .modal-title {
            color: #fff;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        body.light-mode .modal-title {
            color: #333;
            text-shadow: none;
        }
        .modern-form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }
        .modern-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px 15px;
            border-radius: 12px;
            font-size: 1.1rem;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .modern-input:focus {
            border-color: #0162AF;
            box-shadow: 0 0 10px rgba(1, 98, 175, 0.3);
        }
        body.light-mode .modern-input {
            background: #f8f9fa;
            border: 1px solid #ddd;
            color: #333;
        }
        .modern-submit-btn {
            background: linear-gradient(135deg, #0162AF 0%, #004d8c 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(1, 98, 175, 0.4);
            width: 100%;
            margin-top: 10px;
        }
        .modern-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(1, 98, 175, 0.6);
        }
        #sug_box {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            width: 100%;
            background: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 100;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            display: none;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            padding: 15px;
            gap: 15px;
            border: 1px solid rgba(255,255,255,0.1);
            box-sizing: border-box;
        }
        .modern-input:focus + #sug_box, #sug_box:active, #sug_box:hover {
            display: flex;
        }
        body.light-mode #sug_box {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        .no_sug_label {
            color: #aaa;
            padding: 15px;
            margin: 0;
            font-size: 1rem;
        }
        body.light-mode .no_sug_label {
            color: #666;
        }

        #addfriendLabel {
            margin-top: 15px;
            margin-bottom: 0;
            font-size: 1.1rem;
            color: #22c55e !important;
            min-height: 24px;
        }
        #exit-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(247, 109, 87, 0.2);
            color: #F76D57;
            border: 2px solid rgba(247, 109, 87, 0.5);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        #exit-btn:hover {
            background: #F76D57;
            color: white;
            border-color: #F76D57;
            transform: rotate(90deg);
        }
    </style>
    <script>
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            document.addEventListener('DOMContentLoaded', () => {
                document.body.classList.add('light-mode');
            });
        }
    </script>
    <script>
        function hideMainBoxBeforeLoading(box_id) {
            $("#" + box_id + "").css({
                'pointer-events': 'none',
                'user-select': 'none'
            });
            $("#" + box_id + "").animate({
                opacity: 0
            }, 300);
        }

        function showLoadingBox() {
            let temp = document.createElement('div');
            temp.setAttribute('id', 'loading_box_outer_id');
            temp.setAttribute('class', 'loading_box_outer');
            temp.innerHTML = '<div class=loading_bar></div><h3 class=loading_label>Loading</h3>';
            temp.style.opacity = '0';
            document.body.appendChild(temp);
            $('#loading_box_outer_id').animate({
                opacity: 1
            }, 1000);
        }

        function returnAfterLoading(box_id) {
            setTimeout(function() {
                $("#" + box_id + "").css({
                    'pointer-events': 'all'
                });
                $("#" + box_id + "").animate({
                    opacity: 1
                }, 1000);
                $('#loading_box_outer_id').animate({
                    opacity: 0
                }, 1000);
                $('#loading_box_outer_id').css({
                    'z-index': '-100'
                });
            }, 5000);
        }

        function startLoading(box_id) {
            hideMainBoxBeforeLoading(box_id);
            showLoadingBox();
            returnAfterLoading(box_id);
        }
    </script>
</head>

<body>
    <div id="whole_box" style="height:100%;">
        <div style="position: absolute; top: 20px; left: 20px; z-index: 1000;">
            <a href="../" class="backButton">🔙 Back</a>
        </div>
        <div class="modern-add-outer">
            <div class="modern-add-box">
                <a href="../" id="exit-btn" title="Close">✖</a>
                <h2 class="modal-title">Add New Friend</h2>
                <form action="../Add/" method="post" class="modern-form-container">
                    <div style="position: relative;">
                        <input type="text" class="modern-input" name="friendUsername" id="friendUsername_field" required placeholder="Enter username..." onkeyup="getSuggesstions(this.value, false)" autocomplete="off" />
                        <div class="friends_result" id="sug_box">
                            <h2 class="no_sug_label" id="no_sug_label_box">
                                No Suggestions.
                            </h2>
                        </div>
                    </div>
                    
                    <input type="submit" name="addFriendButton" class="modern-submit-btn" id="addFriendButton" value="Send Request">
                    
                    <h2 id="addfriendLabel"></h2>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<?php
if (isset($_POST) && isset($_POST["addFriendButton"])) {
    echo "<script>startLoading('whole_box');</script>";
    extract($_POST);
    $fusername = $friendUsername;
    $you = $_SESSION["who"];
    if (trim($fusername) != "" && $fusername != $you) {
        require_once("../../DB.php");
        $query = "SELECT * FROM users WHERE BINARY username=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $fusername);
        mysqli_stmt_execute($stmt);
        $isUserExistsResult = mysqli_stmt_get_result($stmt);
        if ($isUserExistsResult) {
            if (mysqli_num_rows($isUserExistsResult)) {
                $query2 = "SELECT * FROM friends WHERE BINARY user1=? AND BINARY user2=? OR BINARY user1=? AND BINARY user2=?";
                $stmt2 = mysqli_prepare($conn, $query2);
                mysqli_stmt_bind_param($stmt2, "ssss", $you, $fusername, $fusername, $you);
                mysqli_stmt_execute($stmt2);
                $result2 = mysqli_stmt_get_result($stmt2);
                if ($result2) {
                    if (mysqli_num_rows($result2)) {
                        echo
                        "
                        <script>
                            var errorfield=document.getElementById('addfriendLabel');
                            errorfield.style.color='white';
                            errorfield.innerHTML='Already A Friend.';
                        </script>
                        ";
                    } else {
                        $query = "SELECT * FROM friend_requests WHERE requester=? AND requested_user=?";
                        $stmt = mysqli_prepare($conn, $query);
                        $req = $_SESSION["who"];
                        $treq = $fusername;
                        mysqli_stmt_bind_param($stmt, "ss", $req, $treq);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if ($result && mysqli_num_rows($result)) {
                            echo
                            "
                            <script>
                                var errorfield=document.getElementById('addfriendLabel');
                                errorfield.style.color='white';
                                errorfield.innerHTML='Already sent friend request';
                            </script>
                            ";
                        } else {
                            $query = "INSERT INTO friend_requests(requester, requested_user) VALUES (?,?)";
                            $stmt = mysqli_prepare($conn, $query);
                            $req = $_SESSION["who"];
                            $treq = $fusername;
                            mysqli_stmt_bind_param($stmt, "ss", $req, $treq);
                            if (mysqli_stmt_execute($stmt)) {
                                echo
                                "
                                <script>
                                    var errorfield=document.getElementById('addfriendLabel');
                                    errorfield.style.color='white';
                                    errorfield.innerHTML='Sent Friend Request';

                                    // Send real-time notification
                                    var wsToken = '" . (isset($_SESSION['ws_token']) ? $_SESSION['ws_token'] : '') . "';
                                    var protocol = window.location.protocol === 'https:' ? 'wss://' : 'ws://';
                                    var wsHost = '" . (getenv('WS_URL') ?: (isset($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST'])[0] . ':'.getVarFromEnv('WS_PORT') : 'localhost:'.getVarFromEnv('WS_PORT'))) . "';
                                    var wsUrl = protocol + wsHost + '?token=' + encodeURIComponent(wsToken);
                                    var wsTemp = new WebSocket(wsUrl);
                                    wsTemp.onopen = function() {
                                        wsTemp.send(JSON.stringify({
                                            type: 'notification',
                                            to: '" . addslashes($fusername) . "',
                                            content: 'friend_request'
                                        }));
                                        setTimeout(() => wsTemp.close(), 1000);
                                    };
                                </script>
                                ";

                                //Send email to receiver

                                ob_flush();
                                flush();

                                require_once(dirname(__DIR__, 2) . '/mail.php');
                                $userEmail = mysqli_fetch_assoc($isUserExistsResult)["email"];
                                sendFriendRequestMail($conn, $userEmail, $fusername, $_SESSION["who"]);

                                ob_flush();
                                flush();

                                ////////

                            } else {
                                echo
                                "
                                <script>
                                    var errorfield=document.getElementById('addfriendLabel');
                                    errorfield.style.color='white';
                                    errorfield.innerHTML='Unknown Error..';
                                </script>
                                ";
                            }
                        }
                    }
                } else {
                    echo "<script>window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Connection Error.');</script>";
                }
            } else {
                echo
                "
                <script>
                    var errorfield=document.getElementById('addfriendLabel');
                    errorfield.style.color='white';
                    errorfield.innerHTML='Username is not valid!';
                </script>
                ";
            }
        } else {
            echo "<script>window.pendingAlerts = window.pendingAlerts || []; window.pendingAlerts.push('Connection Error.');</script>";
        }
        mysqli_close($conn);
    } else {
        echo
        "
        <script>
        var errorfield=document.getElementById('addfriendLabel');
        errorfield.style.color='white';
        errorfield.innerHTML='INVALID';
        </script>
        ";
    }
}
?>