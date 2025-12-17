<?php
/**
 * Beispiel 2: Login mit Sessions
 *
 * Zeigt wie man einen einfachen Login mit Sessions baut
 */

// Session starten
session_start();

$error = '';
$success = '';

// Fake-Benutzer für Demo (in Realität aus Datenbank)
$gueltigeBenutzer = [
    'admin' => 'admin123',
    'user' => 'password',
    'max' => '12345'
];

// Login-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prüfen ob Benutzer existiert und Passwort stimmt
    if (isset($gueltigeBenutzer[$username]) && $gueltigeBenutzer[$username] === $password) {
        // Login erfolgreich - Session-Variablen setzen
        $_SESSION['eingeloggt'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_zeit'] = time();
        $_SESSION['user_role'] = ($username === 'admin') ? 'admin' : 'user';

        $success = 'Login erfolgreich!';
    } else {
        $error = 'Falscher Benutzername oder Passwort';
    }
}

// Logout-Verarbeitung
if (isset($_GET['logout'])) {
    // Alle Session-Variablen löschen
    session_unset();
    // Session zerstören
    session_destroy();
    // Neue Session starten für Meldung
    session_start();
    $success = 'Erfolgreich ausgeloggt!';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login mit Session</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; margin: 10px 0; }
        .login-box { border: 1px solid #ccc; padding: 20px; max-width: 400px; }
        .user-info { background: #e7f3ff; padding: 15px; margin: 10px 0; }
        input { margin: 5px 0; padding: 8px; width: 100%; }
        button { padding: 10px 20px; margin: 5px; }
    </style>
</head>
<body>
    <h2>Login-System mit Sessions</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['eingeloggt']) && $_SESSION['eingeloggt']): ?>
        <!-- EINGELOGGT -->
        <div class="user-info">
            <h3>Willkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>

            <p><strong>Session-Informationen:</strong></p>
            <ul>
                <li>Benutzername: <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                <li>Role: <?php echo htmlspecialchars($_SESSION['user_role']); ?></li>
                <li>Eingeloggt seit: <?php echo date('H:i:s', $_SESSION['login_zeit']); ?></li>
                <li>Session-ID: <?php echo session_id(); ?></li>
            </ul>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <p style="color: red;"><strong>Du hast Admin-Rechte!</strong></p>
            <?php endif; ?>

            <p>Du kannst jetzt auf geschützte Bereiche zugreifen:</p>
            <ul>
                <li><a href="03_geschuetzte_seite.php">Geschützte Seite besuchen</a></li>
                <li><a href="04_session_warenkorb.php">Zum Warenkorb</a></li>
            </ul>

            <a href="?logout=1"><button>Ausloggen</button></a>
        </div>

    <?php else: ?>
        <!-- NICHT EINGELOGGT -->
        <div class="login-box">
            <h3>Bitte einloggen</h3>

            <form method="POST" action="">
                <label>Benutzername:</label>
                <input type="text" name="username" required>

                <label>Passwort:</label>
                <input type="password" name="password" required>

                <button type="submit" name="login">Einloggen</button>
            </form>

            <hr>
            <p><strong>Test-Accounts:</strong></p>
            <ul>
                <li>admin / admin123 (Admin)</li>
                <li>user / password (Normal)</li>
                <li>max / 12345 (Normal)</li>
            </ul>
        </div>
    <?php endif; ?>

    <hr>
    <p><a href="01_session_grundlagen.php">← Zurück zu Grundlagen</a></p>

</body>
</html>

<?php
/*
 * WIE FUNKTIONIERT DER LOGIN?
 *
 * 1. Benutzer gibt Username und Passwort ein
 * 2. Formular wird an gleiche Seite gesendet (POST)
 * 3. PHP prüft Zugangsdaten
 * 4. Bei Erfolg: Session-Variablen setzen
 *    - $_SESSION['eingeloggt'] = true
 *    - $_SESSION['username'] = 'max'
 * 5. Auf anderen Seiten: Prüfen ob eingeloggt
 *    - if (isset($_SESSION['eingeloggt']) && $_SESSION['eingeloggt'])
 * 6. Bei Logout: Session löschen
 *    - session_unset() + session_destroy()
 *
 * WICHTIGE SESSION-VARIABLEN für Login:
 * - eingeloggt: true/false
 * - username: Name des Benutzers
 * - user_id: ID aus Datenbank
 * - user_role: admin/user/etc.
 * - login_zeit: Wann eingeloggt
 *
 * SICHERHEIT:
 * - In Realität: Passwort-Hash aus Datenbank vergleichen
 * - password_verify($eingabe, $hash_aus_db)
 * - Nie Passwörter in Session speichern!
 * - session_regenerate_id() nach Login verwenden
 */
?>
