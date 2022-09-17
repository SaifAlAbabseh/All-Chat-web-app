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
        });
    </script>
</head>

<body>
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
mysqli_close($conn);
?>