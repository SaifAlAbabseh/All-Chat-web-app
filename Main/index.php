<?php
session_start();
if (!(isset($_SESSION) && isset($_SESSION["who"]))) {
    header("Location:../");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>
        Welcome To All Chat
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="../Extra/styles/cssFiles/themes.css" />
    <style>
        body,
        html {
            height: 100%;
        }

        @media only screen and (max-width:1430px) {
            body {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mainHeaderImage {
                display: none;
            }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../scripts/main.js"></script>
    <script>
        function hideMainBoxBeforeLoading(box_id) {
            $("#" + box_id + "").css({
                'pointer-events': 'none',
                'user-select': 'none',
                'opacity': '0'
            });
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

        function showLoadingBoxForLogout() {
            document.body.removeChild(document.getElementById("loading_box_outer_id"));
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

        function returnAfterLoadingForLogout() {
            setTimeout(function() {
                window.location.replace('Logout/');
            }, 5000);
        }

        function startLoadingToLogout(box_id) {
            hideMainBoxBeforeLoading(box_id);
            showLoadingBoxForLogout();
            returnAfterLoadingForLogout();
        }

        function startLoading(box_id) {
            hideMainBoxBeforeLoading(box_id);
            showLoadingBox();
            returnAfterLoading(box_id);
        }
        $(document).ready(function() {
            startLoading('whole_box_id');
            $("#create_group_button").click(function() {
                $("#whole_box_id").css({
                    "pointer-events": "none",
                    "opacity": "0.2",
                    "user-select": "none"
                });
                $("#createGroupBox").show();
            });
        });
    </script>
</head>

<body>
    <div id="createGroupBox">
        <div class="outer_reqs_box" onclick="hide()">
            <div class="reqs" id="username_reqs">
                <p>
                    Group name shoud not contain symbols and it should be at least 6 characters in length and at most 12.
                </p>
            </div>
            <div class="reqs" id="image_reqs">
                <p>
                    Group image shoud not be bigger than 1 MB and it should be of type PNG.
                </p>

            </div>
        </div>
        <button id="exit">X</button>
        <div id="createGroupBox-inner">
            <form action="" method="post" enctype="multipart/form-data" class="createGroupForm">
                <center>
                    <table>
                        <tr>
                            <td>
                                <label>Group Name: </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="group_name" class="inputfield" style="width:70%;">
                            </td>
                            <td>
                                <input type="button" value=" i " class="i" id="userreq">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Group Profile Image: </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="file" name="image" id="picFieldGroup" class="inputfield" style="width:70%;">
                            </td>
                            <td>
                                <input type="button" value=" i " class="i" id="imagereq">
                            </td>
                        </tr>
                    </table>
                </center>
                <br>
                <div class="createGroupButton">
                    <input type="submit" class="link" value="Create" name="create_group_button" style="cursor:pointer;width:90%;font-weight:bolder;">
                </div>
            </form>
        </div>
    </div>
    <div class="whole-box" id="whole_box_id">
        <div class="menuBar">
            <img style="cursor:pointer" id="m" width="40px" height="40px" src="../Extra/styles/images/menu.png" alt="menu icon">
        </div>
        <div class="mainHeaderImage" id="mainHeader">
            <img src="../Extra/styles/images/main.png" alt="All Chat Image" width="100%" height="30%" />
            <a href="Add/" class="link" id="addLink">Add New Friend</a>
            <br /><br /><br /><br /><br /><br />
            <a href="Profile/" class="link" id="editLink">Edit Profile</a>
            <br /><br /><br /><br />
            <hr class="hrLine" />
            <?php
            require_once("../DB.php");
            $query = "SELECT picture FROM users WHERE BINARY username='" . $_SESSION["who"] . "'";
            $result = mysqli_query($conn, $query);
            if ($result) {
                if (mysqli_num_rows($result)) {
                    $row = mysqli_fetch_row($result);
                    echo
                    "
                    <div class='profileBox'>
                        <img src='View Image/?u=" . $_SESSION["who"] . "' width='100px' height='100px' style='border-radius:50%'/>
                        <br />
                        <h2 style='color:yellow'>" . $_SESSION["who"] . "</h2>
                        <br />
                        <button onclick=startLoadingToLogout('whole_box_id'); class='link' style='cursor:pointer;font-size:1.5rem;font-weight:bold'>Logout</button>
                    </div>
                    ";
                } else {
                    destroy();
                }
            } else {
                destroy();
            }
            function destroy()
            {
                session_destroy();
                header("Location:../");
            }
            ?>
            <br />
            <div id="exit_menu_button_box">
                <div id="exit_menu_button">X</div>
            </div>
        </div>

        <div class="groupsListBox" id="groupsBox">
            <table>
                <thead>
                    <tr>
                        <th colspan="3" style="color:red">
                            > Groups :
                            <button class="plus_button" id="create_group_button" title="Create Group">+</button>
                        </th>
                    </tr>
                </thead>
                <tbody id="groupsInnerData">
                </tbody>
            </table>
        </div>

        <div class="friendsListBox" id="friendBox">
            <table>
                <thead>
                    <tr>
                        <th colspan="3" style="color:red">
                            > Your Friends :
                        </th>
                    </tr>
                </thead>
                <tbody id="innerData">
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<?php

function checkGroupName($group_name)
{
    if (trim($group_name) == "") {
        return false;
    }
    $everythingIsGood = true;
    if (!(strlen($group_name) >= 6 && strlen($group_name) <= 12)) {
        $everythingIsGood = false;
    }
    if ($everythingIsGood) {
        for ($i = 0; $i < strlen($group_name); $i++) {
            if (!((ord(substr($group_name, $i, 1)) == 32) || (ord(substr($group_name, $i, 1)) >= 48 && ord(substr($group_name, $i, 1)) <= 57) || (ord(substr($group_name, $i, 1)) >= 65 && ord(substr($group_name, $i, 1)) <= 90) || (ord(substr($group_name, $i, 1)) >= 97 && ord(substr($group_name, $i, 1)) <= 122))) {
                $everythingIsGood = false;
                break;
            }
        }
    }
    return $everythingIsGood;
}

function generateGroupID($conn)
{
    $id_query = "SELECT * FROM all_chat_groups";
    $id_result = mysqli_query($conn, $id_query);
    if ($id_result) {
        $new_id = mysqli_num_rows($id_result) + 1;
        return $new_id;
    }
    return "error";
}

function createGroup($conn, $group_name)
{
    $group_id = generateGroupID($conn);
    if ($group_id == "error") {
        die("Unknown Error");
    }
    $tmp_name = $_FILES["image"]["tmp_name"];
    $image_name = "i" . $group_id . ".png";
    $path = "../Extra/styles/images/groups images/$image_name";
    move_uploaded_file($tmp_name, $path);

    $insert_query = "INSERT INTO all_chat_groups VALUES ('" . $group_id . "', '" . $group_name . "', '" . $_SESSION["who"] . "', '" . $image_name . "')";
    $insert_result = mysqli_query($conn, $insert_query);

    if ($insert_result) {
        createTables($conn, $group_id, $group_name);
    } else {
        die("Error");
    }
}

function createTables($conn, $group_id, $group_name)
{
    $users_table_name = "g" . $group_id . "_users";
    $users_table_query = "CREATE TABLE " . $users_table_name . " (
        username VARCHAR(255),
        user_type VARCHAR(255)
    )
    ";
    $leader_insertion_result = mysqli_query($conn, $users_table_query);
    if ($leader_insertion_result) {
        $users2_query = "INSERT INTO $users_table_name VALUES ('" . $_SESSION["who"] . "', 'leader')";
        $users2_result = mysqli_query($conn, $users2_query);
        if ($users2_result) {
            $normal_group_table_name = "g" . $group_id;
            $normal_group_table_query = "CREATE TABLE " . $normal_group_table_name . " (
            fromwho VARCHAR(255),
            message VARCHAR(1000),
            pos VARCHAR(255)
            ) ";
            $normal_group_query_result = mysqli_query($conn, $normal_group_table_query);
            if ($normal_group_query_result) {
                echo
                "
                <script>
                alert('Successfully created group: " . $group_name . "');
                </script>
                ";
            } else {
                die("Error");
            }
        } else {
            die("Error");
        }
    } else {
        die("Unknown Error");
    }
}

if (isset($_POST) && isset($_POST["create_group_button"])) {
    extract($_POST);
    $isGroupNameGood = checkGroupName($group_name);
    if ($_FILES['image']['name'] != "" && $isGroupNameGood) {
        $name = $_FILES["image"]["name"];
        $type = substr($name, strlen($name) - 3, strlen($name) - 1);
        if ($type == "png") {
            define('MB', 1024000);
            $size = round((($_FILES["image"]["size"]) / MB), 0);
            if ($size <= 1) {
                createGroup($conn, $group_name);
            } else {
                echo
                "
                            <script>
                            alert('Image size is bigger than 1 MB');
                            </script>
                            ";
            }
        } else {
            echo
            "
                            <script>
                            alert('Image type is not PNG');
                            </script>
                            ";
        }
    } else {
        echo "<script>
            alert('Check group name requirements or image file is not present.');
        </script>";
    }
}
mysqli_close($conn);
?>