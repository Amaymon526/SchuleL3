<?php
// Pfad zur Textdatei festlegen
$userFile = __DIR__ . '/users_hash.txt';

$error = '';
$success = '';

// Formular wurde gesendet
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Daten aus Formular holen
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $passConfirm = $_POST['password_confirm'] ?? '';

    // Alle Felder müssen ausgefüllt sein
    if (empty($user) || empty($pass) || empty($passConfirm)) {
        $error = 'Bitte alle Felder ausfüllen';
    } elseif ($pass !== $passConfirm) {
        $error = 'Passwörter stimmen nicht überein';
    } elseif (strpos($user, ':') !== false) {
        // Doppelpunkt im Username ist nicht erlaubt
        $error = 'Benutzername darf keinen Doppelpunkt enthalten';
    } else {
        $userExists = false;

        // Prüfen ob Datei existiert und Benutzername schon vorhanden ist
        if (file_exists($userFile)) {
            $users = file($userFile, FILE_IGNORE_NEW_LINES);

            foreach ($users as $line) {
                $parts = explode(':', $line, 2);
                if (count($parts) == 2 && $parts[0] === $user) {
                    $userExists = true;
                    break;
                }
            }
        }

        if ($userExists) {
            $error = 'Benutzername bereits vergeben';
        } else {
            // Passwort wird gehasht für sichere Speicherung
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            // Neuen Benutzer in Datei speichern (Format: username:hashedpassword)
            $newUser = $user . ':' . $hashedPassword . PHP_EOL;

            if (file_put_contents($userFile, $newUser, FILE_APPEND)) {
                $success = 'Registrierung erfolgreich! Du kannst dich jetzt einloggen.';
            } else {
                $error = 'Fehler beim Speichern';
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
    <title>Registrierung - TXT Hash</title>
</head>
<body>
    <h2>Registrierung (TXT mit Hash)</h2>

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
