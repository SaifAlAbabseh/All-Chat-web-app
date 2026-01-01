<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../../");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        All Chat | Add Friend
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="../../Extra/styles/cssFiles/themes.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../../scripts/commonMethods.js"></script>
    <script src="../../scripts/add_friend.js"></script>
    <style>
        body {
            background-color: rgb(247, 247, 247);
        }

        body,
        html {
            height: 100%;
        }
    </style>
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
        <div class="backButton">
            <a href="../"><img class="backbuttonlink" src="../../Extra/styles/images/backButton.png" alt="Back Button" width="50px" height="50px" /></a>
        </div>
        <div class="add_box_outer">
            <div class="add_box">
                <form action="../Add/" method="post">
                    <table>
                        <tr>
                            <td>
                                <input type="text" class="inputfield" name="friendUsername" id="friendUsername_field" required placeholder="Username" onkeyup="getSuggesstions(this.value, false)" autocomplete="off" />
                            </td>
                            <td>
                                <input type="submit" name="addFriendButton" class="buttontag" id="addFriendButton" value="ADD">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="friends_result" id="sug_box">
                                    <h2 class="no_sug_label" id="no_sug_label_box">
                                        No Suggestions.
                                    </h2>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <h2 id="addfriendLabel">
                                </h2>
                            </th>
                        </tr>
                    </table>
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
        $query = "SELECT * FROM users WHERE BINARY username='" . $fusername . "'";
        $isUserExistsResult = mysqli_query($conn, $query);
        if ($isUserExistsResult) {
            if (mysqli_num_rows($isUserExistsResult)) {
                $query2 = "SELECT * FROM friends WHERE BINARY user1='" . $you . "' AND BINARY user2='" . $fusername . "' OR BINARY user1='" . $fusername . "' AND BINARY user2='" . $you . "'";
                $result2 = mysqli_query($conn, $query2);
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
                        $query = "SELECT * FROM friend_requests WHERE requester='" . $_SESSION["who"] . "' AND requested_user='" . $fusername . "'";
                        $result = mysqli_query($conn, $query);
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
                            $query = "INSERT INTO friend_requests(requester, requested_user) VALUES ('" . $_SESSION["who"] . "', '" . $fusername . "')";
                            $result = mysqli_query($conn, $query);
                            if ($result) {
                                echo
                                "
                                <script>
                                    var errorfield=document.getElementById('addfriendLabel');
                                    errorfield.style.color='white';
                                    errorfield.innerHTML='Sent Friend Request';
                                </script>
                                ";

                                //Send email to receiver

                                require_once(dirname(__DIR__, 2) . '/common.php');
                                $everythingIsOk = false;
                                $result = updateUserImageToken($conn, $fusername);
                                if ($result[0]) {
                                    require_once(dirname(__DIR__, 2) . '/mail.php');
                                    $userEmail = mysqli_fetch_assoc($isUserExistsResult)["email"];
                                    if (sendFriendRequestMail($urlMainPath, $userEmail, $fusername, $_SESSION["who"], $result[1])) {
                                        $everythingIsOk = true;
                                    }
                                }

                                //Change token so it can't be used again
                                if($everythingIsOk)
                                    updateUserImageToken($conn, $fusername);

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
                    echo "<script>window.alert('Connection Error.');</script>";
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
            echo "<script>window.alert('Connection Error.');</script>";
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