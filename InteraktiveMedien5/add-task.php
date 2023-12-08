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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $startDate = $_POST['start_date'];
    $frequency = $_POST['frequency'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    
    $success = addReminder($conn, $description, $startDate, $frequency, $title, $category);
    
    if ($success) {
        echo "<p>Erinnerung erfolgreich hinzugefügt.</p>";
    } else {
        echo "<p>Fehler beim Hinzufügen der Erinnerung.</p>";
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
        <title>Home</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
    </head>

    <body>

        <h1 class="input">Erinnerung hinzufügen</h1>

        <?php if (isset($user)): ?>
                
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="input">
                        <label for="title">Titel:</label>
                        <br>
                            <input type="text" id="title" name="title" required>
                        <br>
                        <br>
                    
                        <label for="description">Beschreibung:</label>
                        <br>
                            <input type="text" id="description" name="description" required>
                        <br>
                        <br>

                        <label for="category">Kategorie:</label>
                        <br>
                            <input type="text" id="category" name="category" required>
                        <br>
                        <br>
    
                         <label for="start-date">Startdatum:</label>
                         <br>
                            <input type="datetime-local" id="start-date" name="start_date" required>
                        <br>
                        <br>
    
                        <label for="frequency">Frequenz:</label>
                        <br>
                            <select id="frequency" name="frequency">
                                <option value="täglich">Täglich</option>
                                <option value="wöchentlich">Wöchentlich</option>
                                <option value="monatlich">Monatlich</option>
                            </select>
                        <br>
                        <br>
    
                            <input type="submit" value="Erinnerung hinzufügen">
                    </form>
        
                <p class="input"><a href="index.php">Zurück</a></p>

        <?php else: ?>

            <p><a href="login.php">Anmelden</a> oder <a href="signup.html">registrieren</a></p>

        <?php endif; ?>

     </body>

</html>
    
