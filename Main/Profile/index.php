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
        All Chat | Edit Profile
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel="stylesheet" href="../../Extra/styles/cssFiles/themes.css" />
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
    <br /><br /><br />
    <div class="outer">
        <div class="editMainBox">
            <table>
                <?php
                require_once("../../DB.php");
                $query = "SELECT picture FROM users WHERE BINARY username='" . $_SESSION["who"] . "'";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    if (mysqli_num_rows($result)) {
                        $row = mysqli_fetch_row($result);
                        echo
                        "
                        <tr>
                        <td>
                        <img src='../View Image/?u=".$_SESSION["who"]."' width='100px' height='100px' style='border-radius:50%'/>
                        </td>
                        <td>
                        <h2 style='color:yellow'>" . $_SESSION["who"] . "</h2>
                        </td>
                        </tr>
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
                    header("Location:../../");
                }
                ?>
                <tr>
                    <th colspan="2">
                        <h2>
                            <a class="link" href="Edit Profile/">Change Picture</a>
                        </h2>
                    </th>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>


<?php
mysqli_close($conn);
?>