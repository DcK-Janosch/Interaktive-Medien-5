<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$mysqli = require __DIR__ . "/database.php";

$sql = sprintf("SELECT * FROM user
        WHERE email = '%s'",
        $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["password"], $user["password_hash"])) {
           
            session_start();

            session_regenerate_id();

            $_SESSION["user_id"] = $user["id"];

            header("Location: index.php");
            exit;

        }
    }

    $is_invalid = true;

}

?>

<!DOCTYPE html>
<html>

    <head>
        <title>Anmelden</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
    </head>

    <body class="input">
        <h1>Anmelden</h1>

        <?php if ($is_invalid): ?>
            <em>Inkorrekte Anmeldedaten</em>
        <?php endif; ?>

        <form method="post">

            <label for="email">Email</label>
            <br>
            <input type="email" name="email" id="email"
                   value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
            <br>
            <br>
            <label for="password">Password</label>
            <br>
            <input type="password" name="password" id="password">
            <br>
            <br>

            <button>Anmelden</button>

        </form>

    </body>

</html>