<?php
// Pfad zur Textdatei festlegen
$userFile = __DIR__ . '/users_hash.txt';

$error = '';
$success = '';

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Eingaben vom Formular holen
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Beide Felder müssen ausgefüllt sein
    if (empty($user) || empty($pass)) {
        $error = 'Bitte alle Felder ausfüllen';
    } else {
        // Prüfen ob Datei existiert
        if (!file_exists($userFile)) {
            $error = 'Keine Benutzer registriert';
        } else {
            // Datei Zeile für Zeile durchlesen
            $users = file($userFile, FILE_IGNORE_NEW_LINES);
            $found = false;

            // Jede Zeile durchgehen
            foreach ($users as $line) {
                // Zeile an Doppelpunkt trennen (Format: username:hashedpassword)
                $parts = explode(':', $line, 2);

                if (count($parts) == 2) {
                    $savedUser = $parts[0];
                    $savedHash = $parts[1];

                    // Benutzername vergleichen und Hash überprüfen
                    if ($savedUser === $user && password_verify($pass, $savedHash)) {
                        // Login erfolgreich
                        session_start();
                        $_SESSION['username'] = $user;
                        $success = 'Login erfolgreich!';
                        $found = true;
                        break;
                    }
                }
            }

            if (!$found && empty($success)) {
                $error = 'Falscher Benutzername oder Passwort';
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
    <title>Login - TXT Hash</title>
</head>
<body>
    <h2>Login (TXT mit Hash)</h2>

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
