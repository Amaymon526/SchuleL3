<?php
// Datenbankverbindung herstellen
$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    // PDO Objekt anlegen
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Fehler aktivieren
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prüfen ob Tabelle existiert, sonst erstellen
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'users'");

    if ($tableCheck->rowCount() == 0) {
        // Tabelle existiert nicht, wird erstellt
        $createTable = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL
        )";
        $pdo->exec($createTable);
    }
} catch(PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}

$error = '';
$success = '';

// Wenn Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Daten aus Formular holen
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $passConfirm = $_POST['password_confirm'] ?? '';

    // Felder müssen alle ausgefüllt sein
    if (empty($user) || empty($pass) || empty($passConfirm)) {
        $error = 'Bitte alle Felder ausfüllen';
    } elseif ($pass !== $passConfirm) {
        $error = 'Passwörter stimmen nicht überein';
    } else {
        // Prüfen ob Username schon existiert
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$user]);

        if ($stmt->fetch()) {
            $error = 'Benutzername bereits vergeben';
        } else {
            // Passwort wird gehasht für sichere Speicherung
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            // Benutzer mit gehashtem Passwort speichern
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

            if ($stmt->execute([$user, $hashedPassword])) {
                $success = 'Registrierung erfolgreich! Du kannst dich jetzt einloggen.';
            } else {
                $error = 'Fehler bei der Registrierung';
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
    <title>Registrierung - PDO Hash</title>
</head>
<body>
    <h2>Registrierung (PDO mit Hash)</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label>Benutzername:</label><br>
            <input type="text" name="username" required>
        </div>
        <br>
        <div>
            <label>Passwort:</label><br>
            <input type="password" name="password" required>
        </div>
        <br>
        <div>
            <label>Passwort bestätigen:</label><br>
            <input type="password" name="password_confirm" required>
        </div>
        <br>
        <button type="submit">Registrieren</button>
    </form>

    <p>Schon ein Account? <a href="login.php">Hier einloggen</a></p>
</body>
</html>
