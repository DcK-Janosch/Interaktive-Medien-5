<?php

$servername = "localhost";
$username = "875410_6_1";
$password = "k2wduqBYxnio";
$dbname = "875410_6_1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Verbindung abgekackt, sorry :(" . $conn->connect_error);
}

session_start();

if (isset($_SESSION["user_id"])) {

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM user
        WHERE id = {$_SESSION["user_id"]}";

$result = $mysqli->query($sql);

$user = $result->fetch_assoc();

}



function calculateNextReminder($startDate, $frequency) {
    $date = new DateTime($startDate);
    
    switch ($frequency) {
        case 'täglich':
            $date->modify('+1 day');
            break;
        case 'wöchentlich':
            $date->modify('+1 week');
            break;
        case 'monatlich':
            $date->modify('first day of next month');
            break;
        default:
            throw new Exception("Unbekannte Frequenz: " . $frequency);
    }
    
    return $date->format('Y-m-d H:i:s');
}

function addReminder($conn, $description, $startDate, $frequency, $title, $category) {
    $nextReminder = calculateNextReminder($startDate, $frequency);
    $stmt = $conn->prepare("INSERT INTO reminders (description, start_date, frequency, next_reminder, title, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $description, $startDate, $frequency, $nextReminder, $title, $category);
    
    $stmt->execute();
    
    if ($stmt->error) {
        echo "Fehler: " . $stmt->error;
        return false;
    }
    
    $stmt->close();
    return true;
}

function markAsDone($conn, $id, $frequency) {
    $newNextReminder = calculateNextReminder(date('Y-m-d H:i:s'), $frequency);
    $stmt = $conn->prepare("UPDATE reminders SET next_reminder = ? WHERE id = ?");
    $stmt->bind_param("si", $newNextReminder, $id);
    
    $stmt->execute();
    
    if ($stmt->error) {
        echo "Fehler beim Aktualisieren der Erinnerung: " . $stmt->error;
        return false;
    }
    
    $stmt->close();
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_done'])) {
    $id = $_POST['reminder_id'];
    $frequency = $_POST['reminder_frequency'];
    
    if (markAsDone($conn, $id, $frequency)) {
        echo "<p>Erinnerung wurde als erledigt markiert und zurückgesetzt.</p>";
    } else {
        echo "<p>Hoppla Schorsch, isch nit gange. :(.</p>";
    }
}

$sql = "SELECT id, description, start_date, frequency, next_reminder, title, category FROM reminders";
$result = $conn->query($sql);

$reminders = [];
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    array_push($reminders, $row);
  }
}

$conn->close();

?>




<!DOCTYPE html>
<html>

    <head>
        <title>TaskAnchor</title>
        <link rel="icon" type="image/x-icon" href="images/TaskAnchor_Favicon_E1.png">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
    </head>

    <header>

        <div class="topnav">
            <a class="active" href="index.php">Home</a>
            <a href="video.html">Code-Erklärung</a>
            <a href="documentation.html">Dokumentation</a>
            <a href="https://github.com/DcK-Janosch/Interaktive-Medien-5">GitHub</a>
            <a class= "right" href="logout.php">Abmelden</a>
        </div>

    </header>

    <body>

        <br>
        <br>

        <div class="Inhalt">

            <h1>TaskAnchor</h1>

        <?php if (isset($user)): ?>

            <br>

            <table>

                <thead>

                    <tr>
                        <th>Fällig am</th>
                        <th>Titel</th>
                        <th>Kategorie</th>
                        <th>Beschreibung</th>
                        <th>Aktion</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($reminders as $reminder): ?>

                        <tr>
                            <td><?= $reminder['next_reminder'] ?></td> 
                            <td><?= htmlspecialchars($reminder['title']) ?></td>
                            <td><?= htmlspecialchars($reminder['category']) ?></td>
                            <td><?= htmlspecialchars($reminder['description']) ?></td>
                            <td>
                                <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="reminder_id" value="<?= $reminder['id'] ?>">
                                <input type="hidden" name="reminder_frequency" value="<?= $reminder['frequency'] ?>">
                                <input type="submit" name="mark_done" value="Erledigt" id="button">
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                
                </tbody>

            </table>

                <p><a href="add-task.php" id="button">Aufgabe hinzufügen</a></p>

        </div>

            <br>
            <br>
            <br>

    </body>

    <footer>

        <p>Author: Andriu Manetsch</p>
        <p><a href="mailto:andriu.manetsch@gmail.com">andriu.manetsch@gmail.com</a></p>

    </footer>

    <?php else: ?>

        <p><a href="login.php">Anmelden</a> oder <a href="signup.html">registrieren</a></p>

    <?php endif; ?>

</html>
    
