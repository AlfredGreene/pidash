<!doctype html>
<html>
    <head>
        <title>md5 - Raspberry Pi</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="icon" href="assets/images/favicon.ico">
        <meta name="viewport" content="width=device-width, user-scalable=no">
    </head>
    <body>
        <div class="container">
<?php
if (isset($_POST["p"])) {
    echo '
            <div class="row">
                <div class="col-50">
                    <div class="block">
                        <h2>Result</h2>
                        ' . md5($_POST["p"]) . '
                    </div>
                </div>
                <div class="col-50">
                    <div class="block">
                        <h2>Create Hash</h2>
                        <form method="post" action="">
                            <input type="password" class="fullwidth" name="p" placeholder="Text to hash">
                        </form>
                    </div>
                </div>
            </div>';
} else {
    echo '
            <div class="block">
                <h2>Create Hash</h2>
                <form method="post" action="">
                    <input type="password" class="fullwidth" name="p" placeholder="Text to hash">
                </form>
            </div>';
}
?>

        </div>
    </body>
</html>