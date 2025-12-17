<?php
// Pfad zur Textdatei festlegen
$newsletterFile = __DIR__ . '/newsletter.txt';

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
    } elseif (strpos($email, '|') !== false || strpos($vorname, '|') !== false ||
              strpos($nachname, '|') !== false || strpos($telefon, '|') !== false) {
        // Pipe-Zeichen ist nicht erlaubt da es als Trenner verwendet wird
        $error = 'Pipe-Zeichen (|) ist nicht erlaubt';
    } else {
        $emailExists = false;

        // Prüfen ob Email bereits registriert ist
        if (file_exists($newsletterFile)) {
            $subscribers = file($newsletterFile, FILE_IGNORE_NEW_LINES);

            foreach ($subscribers as $line) {
                // Format: email|vorname|nachname|telefon
                $parts = explode('|', $line, 4);
                if (count($parts) >= 1 && $parts[0] === $email) {
                    $emailExists = true;
                    break;
                }
            }
        }

        if ($emailExists) {
            $error = 'Diese Email-Adresse ist bereits registriert';
        } else {
            // Neuen Abonnenten speichern (Format: email|vorname|nachname|telefon)
            $newSubscriber = $email . '|' . $vorname . '|' . $nachname . '|' . $telefon . PHP_EOL;

            if (file_put_contents($newsletterFile, $newSubscriber, FILE_APPEND)) {
                $success = 'Erfolgreich für Newsletter angemeldet!';
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
    <title>Newsletter Anmeldung - TXT</title>
</head>
<body>
    <h2>Newsletter Anmeldung (Textdatei)</h2>

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
