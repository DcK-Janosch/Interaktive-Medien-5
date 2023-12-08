<?php

    $host = "localhost";
    $dbname = "875410_6_1";
    $username = "875410_6_1";
    $password = "k2wduqBYxnio";

$mysqli = new mysqli($host, $username, $password, $dbname);

    if ($mysqli->connect_errno) {
        die("Connection error: " . $mysqli->connect_error);
    }

return $mysqli;