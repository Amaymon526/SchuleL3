<?php
/**
 * Beispiel 8: Session & Cookie Sicherheit
 *
 * Zeigt wichtige Sicherheitsmaßnahmen
 */

session_start();

// SICHERHEITSMASSNAHME 1: Session-ID regenerieren nach Login
if (isset($_POST['login']) && !isset($_SESSION['regeneriert'])) {
    // Neue Session-ID generieren (gegen Session Fixation)
    session_regenerate_id(true);
    $_SESSION['regeneriert'] = true;
    $_SESSION['message'] = 'Session-ID wurde nach Login neu generiert!';
}

// SICHERHEITSMASSNAHME 2: Session-Timeout
$timeout = 1800; // 30 Minuten

if (isset($_SESSION['letzte_aktivitaet'])) {
    $inaktiv = time() - $_SESSION['letzte_aktivitaet'];

    if ($inaktiv > $timeout) {
        // Session abgelaufen
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['message'] = 'Session abgelaufen! Bitte erneut einloggen.';
    }
}
$_SESSION['letzte_aktivitaet'] = time();

// SICHERHEITSMASSNAHME 3: User-Agent prüfen (gegen Session Hijacking)
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} else {
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // User-Agent hat sich geändert - verdächtig!
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['message'] = 'Sicherheitswarnung: User-Agent geändert!';
    }
}

// SICHERHEITSMASSNAHME 4: IP-Adresse prüfen (optional, kann Probleme machen)
if (!isset($_SESSION['ip_adresse'])) {
    $_SESSION['ip_adresse'] = $_SERVER['REMOTE_ADDR'];
}
// In Produktion: IP-Prüfung kann Probleme bei mobilen Geräten machen
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Session & Cookie Sicherheit</title>
    <style>
        .box { padding: 20px; margin: 15px 0; }
        .warning { padding: 15px; margin: 10px 0; }
        .danger { padding: 15px; margin: 10px 0; }
        .success { padding: 15px; margin: 10px 0; }
        code { padding: 2px 6px; }
        pre { padding: 15px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔒 Session & Cookie Sicherheit</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- SICHERHEITSRISIKEN -->
    <div class="box">
        <h2>⚠️ Sicherheitsrisiken</h2>

        <div class="danger">
            <h3>1. Session Hijacking</h3>
            <p>Angreifer stiehlt Session-ID und übernimmt die Session.</p>
            <p><strong>Schutz:</strong></p>
            <ul>
                <li>HTTPS verwenden</li>
                <li>HttpOnly-Flag bei Cookies setzen</li>
                <li>User-Agent prüfen</li>
                <li>Session-Timeout implementieren</li>
            </ul>
        </div>

        <div class="danger">
            <h3>2. Session Fixation</h3>
            <p>Angreifer setzt Session-ID vor dem Login.</p>
            <p><strong>Schutz:</strong></p>
            <ul>
                <li>Session-ID nach Login neu generieren</li>
                <li><code>session_regenerate_id(true)</code> verwenden</li>
            </ul>
        </div>

        <div class="danger">
            <h3>3. XSS (Cross-Site Scripting)</h3>
            <p>Angreifer schleust JavaScript ein um Cookies zu stehlen.</p>
            <p><strong>Schutz:</strong></p>
            <ul>
                <li>Alle Ausgaben mit <code>htmlspecialchars()</code> escapen</li>
                <li>HttpOnly-Flag bei Cookies</li>
                <li>Content Security Policy (CSP)</li>
            </ul>
        </div>

        <div class="danger">
            <h3>4. CSRF (Cross-Site Request Forgery)</h3>
            <p>Angreifer führt Aktionen im Namen des Benutzers aus.</p>
            <p><strong>Schutz:</strong></p>
            <ul>
                <li>CSRF-Token bei Formularen</li>
                <li>SameSite-Cookie-Attribut</li>
            </ul>
        </div>
    </div>

    <!-- SICHERE COOKIE-EINSTELLUNGEN -->
    <div class="box">
        <h2>✅ Sichere Cookie-Einstellungen</h2>

        <h3>Alle Sicherheits-Optionen nutzen:</h3>
        <pre>setcookie(
    'name',
    'wert',
    [
        'expires' => time() + 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,      // Nur über HTTPS
        'httponly' => true,    // Nicht per JavaScript lesbar
        'samesite' => 'Strict' // CSRF-Schutz
    ]
);</pre>

        <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
            <tr>
                <th>Option</th>
                <th>Bedeutung</th>
                <th>Empfehlung</th>
            </tr>
            <tr>
                <td><code>secure</code></td>
                <td>Cookie nur über HTTPS</td>
                <td>true (in Produktion)</td>
            </tr>
            <tr>
                <td><code>httponly</code></td>
                <td>Nicht per JavaScript lesbar</td>
                <td>true (immer!)</td>
            </tr>
            <tr>
                <td><code>samesite</code></td>
                <td>CSRF-Schutz</td>
                <td>'Strict' oder 'Lax'</td>
            </tr>
        </table>
    </div>

    <!-- AKTUELLE SESSION-INFO -->
    <div class="box">
        <h2>📊 Aktuelle Session-Informationen</h2>

        <ul>
            <li><strong>Session-ID:</strong> <?php echo session_id(); ?></li>
            <li><strong>User-Agent:</strong> <?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?></li>
            <li><strong>IP-Adresse:</strong> <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?></li>
            <li><strong>Letzte Aktivität:</strong> <?php echo isset($_SESSION['letzte_aktivitaet']) ? date('H:i:s', $_SESSION['letzte_aktivitaet']) : 'Neu'; ?></li>
            <li><strong>Timeout in:</strong> <?php echo isset($_SESSION['letzte_aktivitaet']) ? ($timeout - (time() - $_SESSION['letzte_aktivitaet'])) . ' Sekunden' : 'N/A'; ?></li>
        </ul>

        <form method="POST">
            <button type="submit" name="login">Session-ID regenerieren</button>
        </form>
    </div>

    <!-- BEST PRACTICES -->
    <div class="box">
        <h2>✨ Best Practices</h2>

        <h3>Session-Sicherheit:</h3>
        <pre>// Session-ID nach Login regenerieren
session_regenerate_id(true);

// Timeout implementieren
$_SESSION['letzte_aktivitaet'] = time();

// User-Agent speichern und prüfen
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}</pre>

        <h3>Cookie-Sicherheit:</h3>
        <pre>// RICHTIG - alle Sicherheits-Optionen
setcookie('name', 'wert', [
    'expires' => time() + 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// FALSCH - unsicher
setcookie('name', 'wert');</pre>

        <h3>Eingaben escapen:</h3>
        <pre>// Immer bei Ausgabe escapen
echo htmlspecialchars($_SESSION['username']);
echo htmlspecialchars($_COOKIE['name']);</pre>
    </div>

    <!-- CHECKLISTE -->
    <div class="box">
        <h2>📋 Sicherheits-Checkliste</h2>

        <h3>Sessions:</h3>
        <ul>
            <li>✅ <code>session_regenerate_id()</code> nach Login</li>
            <li>✅ Session-Timeout implementieren</li>
            <li>✅ User-Agent/IP prüfen (optional)</li>
            <li>✅ HTTPS verwenden</li>
            <li>✅ Keine sensiblen Daten in Session</li>
        </ul>

        <h3>Cookies:</h3>
        <ul>
            <li>✅ <code>httponly => true</code> verwenden</li>
            <li>✅ <code>secure => true</code> bei HTTPS</li>
            <li>✅ <code>samesite => 'Strict'</code> setzen</li>
            <li>✅ Ablaufzeit definieren</li>
            <li>✅ Keine Passwörter in Cookies!</li>
        </ul>

        <h3>Allgemein:</h3>
        <ul>
            <li>✅ Alle Ausgaben escapen (<code>htmlspecialchars()</code>)</li>
            <li>✅ CSRF-Token bei Formularen</li>
            <li>✅ Prepared Statements bei DB-Queries</li>
            <li>✅ Passwörter hashen (<code>password_hash()</code>)</li>
        </ul>
    </div>

    <hr>
    <p><a href="01_session_grundlagen.php">← Zurück zur Übersicht</a></p>

</body>
</html>

<?php
/*
 * ZUSAMMENFASSUNG SICHERHEIT:
 *
 * 1. SESSIONS:
 *    - session_regenerate_id() nach Login
 *    - Timeout implementieren
 *    - User-Agent prüfen
 *    - Über HTTPS
 *
 * 2. COOKIES:
 *    - httponly = true (gegen XSS)
 *    - secure = true (nur HTTPS)
 *    - samesite = 'Strict' (gegen CSRF)
 *    - Nie Passwörter speichern
 *
 * 3. AUSGABEN:
 *    - htmlspecialchars() IMMER verwenden
 *    - Nie ungeprüfte Eingaben ausgeben
 *
 * 4. DATENBANK:
 *    - Prepared Statements verwenden
 *    - Passwörter hashen
 *
 * 5. FORMULARE:
 *    - CSRF-Token generieren und prüfen
 *    - Eingaben validieren
 */
?>
