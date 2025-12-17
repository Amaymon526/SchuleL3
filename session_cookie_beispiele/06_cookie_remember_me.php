<?php
/**
 * Beispiel 6: "Remember Me" mit Cookies
 *
 * Zeigt wie man einen automatischen Login mit Cookies implementiert
 */

session_start();

$error = '';
$success = '';

// Fake-Benutzer (in Realität aus Datenbank)
$gueltigeBenutzer = [
    'admin' => 'admin123',
    'user' => 'password',
    'max' => '12345'
];

// Auto-Login prüfen (bei Seitenaufruf)
if (!isset($_SESSION['eingeloggt']) && isset($_COOKIE['remember_token'])) {
    // Cookie vorhanden - versuche Auto-Login
    $token = $_COOKIE['remember_token'];
    $username = $_COOKIE['remember_user'] ?? '';

    // In Realität: Token aus Datenbank prüfen
    // Hier: Einfache Prüfung ob Benutzer existiert
    if (isset($gueltigeBenutzer[$username])) {
        // Auto-Login erfolgreich
        $_SESSION['eingeloggt'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_zeit'] = time();
        $success = 'Automatisch eingeloggt via Cookie!';
    }
}

// Login-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (isset($gueltigeBenutzer[$username]) && $gueltigeBenutzer[$username] === $password) {
        // Login erfolgreich
        $_SESSION['eingeloggt'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_zeit'] = time();

        // "Remember Me" aktiviert?
        if ($remember) {
            // Token generieren (in Realität: kryptografisch sicher)
            $token = bin2hex(random_bytes(32));

            // Cookies setzen (30 Tage gültig)
            $ablauf = time() + (30 * 24 * 60 * 60);
            setcookie('remember_token', $token, $ablauf, '/', '', false, true);
            setcookie('remember_user', $username, $ablauf, '/', '', false, true);

            // In Realität: Token in Datenbank speichern
            $success = 'Login erfolgreich! Du bleibst 30 Tage eingeloggt.';
        } else {
            $success = 'Login erfolgreich!';
        }
    } else {
        $error = 'Falscher Benutzername oder Passwort';
    }
}

// Logout
if (isset($_GET['logout'])) {
    // Session löschen
    session_unset();
    session_destroy();

    // Remember-Cookies löschen
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_user', '', time() - 3600, '/');

    session_start();
    $success = 'Erfolgreich ausgeloggt!';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Remember Me - Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; margin: 10px 0; }
        .login-box { border: 1px solid #ccc; padding: 20px; max-width: 400px; }
        .user-info { background: #e7f3ff; padding: 15px; margin: 10px 0; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; margin: 5px; }
        .remember { margin: 10px 0; }
    </style>
</head>
<body>
    <h2>🔐 Login mit "Remember Me"</h2>

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

            <p><strong>Login-Informationen:</strong></p>
            <ul>
                <li>Benutzername: <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                <li>Eingeloggt seit: <?php echo date('H:i:s', $_SESSION['login_zeit']); ?></li>
            </ul>

            <?php if (isset($_COOKIE['remember_token'])): ?>
                <p style="color: green;">
                    ✅ <strong>"Remember Me" ist aktiv!</strong><br>
                    Du bleibst auch nach Browser-Schließung eingeloggt.
                </p>
                <p><em>Cookie-Info:</em></p>
                <ul>
                    <li>Token: <?php echo substr($_COOKIE['remember_token'], 0, 20); ?>...</li>
                    <li>Benutzer: <?php echo htmlspecialchars($_COOKIE['remember_user']); ?></li>
                </ul>
            <?php else: ?>
                <p><em>"Remember Me" wurde nicht aktiviert. Nach Browser-Schließung musst du dich neu einloggen.</em></p>
            <?php endif; ?>

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

                <div class="remember">
                    <label>
                        <input type="checkbox" name="remember" value="1">
                        <strong>Angemeldet bleiben (30 Tage)</strong>
                    </label>
                    <p style="font-size: 12px; color: #666;">
                        Ein Cookie wird gesetzt um dich automatisch einzuloggen.
                    </p>
                </div>

                <button type="submit" name="login">Einloggen</button>
            </form>

            <hr>
            <p><strong>Test-Accounts:</strong></p>
            <ul>
                <li>admin / admin123</li>
                <li>user / password</li>
                <li>max / 12345</li>
            </ul>

            <p style="font-size: 12px; color: #666;">
                <strong>Tipp:</strong> Logge dich mit "Angemeldet bleiben" ein,
                schließe den Browser und öffne die Seite erneut -
                du bist automatisch eingeloggt!
            </p>
        </div>
    <?php endif; ?>

    <hr>
    <p><a href="05_cookie_grundlagen.php">← Zurück zu Cookie Grundlagen</a></p>

</body>
</html>

<?php
/*
 * "REMEMBER ME" FUNKTIONSWEISE:
 *
 * 1. BEIM LOGIN (mit Checkbox aktiviert):
 *    - Eindeutigen Token generieren
 *    - Token in Cookie speichern (30 Tage)
 *    - Username in Cookie speichern
 *    - Token in Datenbank mit User-ID speichern
 *
 * 2. BEI JEDEM SEITENAUFRUF:
 *    - Prüfen ob Session existiert
 *    - Wenn nicht: Prüfen ob Remember-Cookie existiert
 *    - Token aus Cookie mit Datenbank vergleichen
 *    - Bei Match: Automatisch einloggen (Session setzen)
 *
 * 3. BEIM LOGOUT:
 *    - Session löschen
 *    - Cookies löschen
 *    - Token aus Datenbank entfernen
 *
 * SICHERHEIT - WICHTIG!
 *
 * ❌ UNSICHER (nicht machen):
 * - Passwort in Cookie speichern
 * - Username alleine als Token verwenden
 * - Keine Ablaufzeit setzen
 *
 * ✅ SICHER:
 * - Kryptografisch sicheren Token generieren
 * - Token in Datenbank hashen
 * - httponly = true für Cookies
 * - Token nach einmaliger Verwendung erneuern
 * - IP-Adresse/User-Agent prüfen (optional)
 *
 * BEISPIEL DATENBANK-TABELLE:
 *
 * CREATE TABLE remember_tokens (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     user_id INT NOT NULL,
 *     token_hash VARCHAR(255) NOT NULL,
 *     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *     expires_at TIMESTAMP
 * );
 *
 * Token speichern:
 * $hash = password_hash($token, PASSWORD_DEFAULT);
 * INSERT INTO remember_tokens (user_id, token_hash, expires_at)
 * VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
 *
 * Token prüfen:
 * SELECT * FROM remember_tokens WHERE user_id = ? AND expires_at > NOW()
 * if (password_verify($cookie_token, $db_hash)) { ... }
 */
?>
