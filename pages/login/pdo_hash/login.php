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
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'users'");

    if ($tableCheck->rowCount() == 0) {
        // Tabelle existiert nicht, wird erstellt
        $createTable = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULLta
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
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (empty($user) || empty($pass)) {
        $error = 'Bitte alle Felder ausfüllen';
    } else {
        // Benutzer aus Datenbank holen
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Benutzer existiert und Passwort wird mit Hash verglichen
        if ($userData && password_verify($pass, $userData['password'])) {
            // Login klappt
            session_start();
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $success = 'Login erfolgreich!';
        } else {
            $error = 'Falscher Benutzername oder Passwort';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PDO Hash</title>
</head>
<body>
    <h2>Login (PDO mit Hash)</h2>

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
        <button type="submit">Einloggen</button>
    </form>

    <p>Noch kein Account? <a href="register.php">Hier registrieren</a></p>
</body>
</html>
