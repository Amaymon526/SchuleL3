<?php
// Datenbankverbindung herstellen
$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    // PDO Verbindung aufbauen
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Fehlerbehandlung einschalten
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prüfen ob Tabelle existiert, sonst erstellen
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'newsletter'");

    if ($tableCheck->rowCount() == 0) {
        // Tabelle existiert nicht, wird erstellt
        $createTable = "CREATE TABLE newsletter (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) UNIQUE NOT NULL,
            vorname VARCHAR(50) NOT NULL,
            nachname VARCHAR(50) NOT NULL,
            telefon VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($createTable);
    }
} catch(PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}

$error = '';
$success = '';

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Eingaben vom Formular holen
    $email = $_POST['email'] ?? '';
    $vorname = $_POST['vorname'] ?? '';
    $nachname = $_POST['nachname'] ?? '';
    $telefon = $_POST['telefon'] ?? '';

    // Pflichtfelder prüfen
    if (empty($email) || empty($vorname) || empty($nachname)) {
        $error = 'Bitte Email, Vorname und Nachname ausfüllen';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte gültige Email-Adresse eingeben';
    } else {
        // Prüfen ob Email bereits registriert ist
        $stmt = $pdo->prepare("SELECT id FROM newsletter WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Diese Email-Adresse ist bereits registriert';
        } else {
            // Neuen Newsletter-Abonnenten eintragen
            $stmt = $pdo->prepare("INSERT INTO newsletter (email, vorname, nachname, telefon) VALUES (?, ?, ?, ?)");

            if ($stmt->execute([$email, $vorname, $nachname, $telefon])) {
                $success = 'Erfolgreich für Newsletter angemeldet!';
            } else {
                $error = 'Fehler bei der Anmeldung';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Anmeldung - Datenbank</title>
</head>
<body>
    <h2>Newsletter Anmeldung (Datenbank)</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Email-Adresse:</label><br>
            <input type="email" name="email" required>
        </div>
        <br>
        <div>
            <label>Vorname:</label><br>
            <input type="text" name="vorname" required>
        </div>
        <br>
        <div>
            <label>Nachname:</label><br>
            <input type="text" name="nachname" required>
        </div>
        <br>
        <div>
            <label>Telefonnummer (optional):</label><br>
            <input type="tel" name="telefon">
        </div>
        <br>
        <button type="submit">Anmelden</button>
    </form>
</body>
</html>
