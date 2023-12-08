<?php

if (empty($_POST["titel"])) {
    die("Titel wird benötigt");
}

if (empty($_POST["beschreibung"])) {
    die("Beschreibung wird benötigt.");
}

if (empty($_POST["kategorie"])) {
    die("Kategorie wird benötigt.");
}

if (empty($_POST["frequenz"])) {
    die("Frequenz wird benötigt.");
}

$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO tasks (titel, beschreibung, kategorie, frequenz, startdatum)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sssss",
                  $_POST["titel"],
                  $_POST["beschreibung"],
                  $_POST["kategorie"],
                  $_POST["frequenz"],
                  $_POST["startdatum"]);

if ($stmt->execute()) {

    header("Location: index.php");
    exit;

} else {

    if ($mysqli->errno === 1062) {
        die("Etwas ist schiefgelaufen, Opfer lol.");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}