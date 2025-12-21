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
        $result = mysqli_query($conn, $query);
        if ($result) {
            if (mysqli_num_rows($result)) {
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
                        $query3 = "INSERT INTO friends VALUES ('" . $you . "','" . $fusername . "')";
                        if (mysqli_query($conn, $query3)) {
                            $tablename = "" . $_SESSION["who"] . "" . $fusername;
                            $query4 = "CREATE TABLE " . $tablename . " (fromwho varchar(1000) , message varchar(1000),pos varchar(1000), whenSent DATETIME DEFAULT CURRENT_TIMESTAMP)";
                            if (mysqli_query($conn, $query4)) {
                                echo
                                "
                                <script>
                                    var errorfield=document.getElementById('addfriendLabel');
                                    errorfield.style.color='green';
                                    errorfield.innerHTML='Successfully Added.';
                                </script>
                            ";
                            }
                        } else {
                            echo "<script>window.alert('Connection Error.');</script>";
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