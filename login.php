<?php
session_start();

require("config.php");
if (isset($_SESSION["login"]) or !$USE_PASSWORD) {
    header("Location: index.php");
}

if (!isset($_SESSION["nice_try"])) {
    $_SESSION["nice_try"] = array();
}

$wait = false;
$wait_diff = time() - key(array_slice($_SESSION["nice_try"], -1, 1, TRUE));

if ($wait_diff > $WAIT_FOR_IT) {
    $_SESSION["nice_try"] = array();
}

if (isset($_POST["p"])) {
    if (md5($_POST["p"]) == $PASSWORD) {
        $_SESSION["login"] = true;
        header("Location: index.php");
    } else {
        $_SESSION["nice_try"][time()] = $_POST["p"];
    }
}

if (count($_SESSION["nice_try"]) == $MAX_TRIES) {
    $wait = true;
}

?>
<html>
    <head>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/login.css">
        <link rel="icon" href="assets/images/favicon.ico">
        <title>Login - Raspberry Pi</title>
    </head>
    <body>
        <div class="vcenter container">
            <div class="circle">
                <div class="logo vcenter">
                    &pi;
                </div>
            </div>
<?php
if ($wait) {
    echo '
            Wrong password. Please wait ' . ($WAIT_FOR_IT - $wait_diff) . ' seconds';
} else {
    echo '
            <form method="post" action="" class="row">
                <input type="password" name="p" placeholder="Password" autofocus required>
            </form>';
}
?>
        </div>
    </body>
</html>