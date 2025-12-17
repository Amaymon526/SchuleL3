<?php
/**
 * Beispiel 5: Cookie Grundlagen
 *
 * Was sind Cookies?
 * - Kleine Datenpakete die IM BROWSER gespeichert werden
 * - Bleiben auch nach Browser-Schließung erhalten (wenn gewünscht)
 * - Werden bei jedem Request automatisch mitgesendet
 * - Perfekt für "Remember Me", Einstellungen, Tracking
 */

// WICHTIG: setcookie() muss VOR jedem HTML/echo stehen!

$message = '';

// BEISPIEL 1: Cookie setzen
if (isset($_GET['set'])) {
    // Cookie für 1 Tag (86400 Sekunden)
    setcookie('benutzername', 'Max', time() + 86400, '/');
    setcookie('lieblingsfarbe', 'blau', time() + 86400, '/');
    $message = 'Cookies wurden gesetzt! Aktualisiere die Seite.';
}

// BEISPIEL 2: Cookie mit längerer Laufzeit
if (isset($_GET['set_long'])) {
    // Cookie für 30 Tage
    setcookie('langzeit_cookie', 'Ich bleibe 30 Tage!', time() + (30 * 24 * 60 * 60), '/');
    $message = 'Langzeit-Cookie gesetzt (30 Tage)!';
}

// BEISPIEL 3: Cookie löschen
if (isset($_GET['delete'])) {
    // Cookie löschen = Ablaufzeit in der Vergangenheit setzen
    setcookie('benutzername', '', time() - 3600, '/');
    setcookie('lieblingsfarbe', '', time() - 3600, '/');
    $message = 'Cookies wurden gelöscht! Aktualisiere die Seite.';
}

// BEISPIEL 4: Alle Cookies löschen
if (isset($_GET['delete_all'])) {
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, '', time() - 3600, '/');
    }
    $message = 'Alle Cookies gelöscht!';
}

// BEISPIEL 5: Cookie-Zähler (wie oft Seite besucht)
if (isset($_COOKIE['besuche'])) {
    $besuche = (int)$_COOKIE['besuche'] + 1;
} else {
    $besuche = 1;
}
setcookie('besuche', $besuche, time() + (365 * 24 * 60 * 60), '/');

// BEISPIEL 6: Letzter Besuch speichern
$letzter_besuch = $_COOKIE['letzter_besuch'] ?? null;
setcookie('letzter_besuch', date('Y-m-d H:i:s'), time() + (365 * 24 * 60 * 60), '/');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Cookie Grundlagen</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message { background: #d4edda; padding: 10px; margin: 10px 0; }
        .cookie-box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        button { padding: 10px; margin: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>🍪 Cookie Grundlagen</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="cookie-box">
        <h3>Aktionen</h3>
        <a href="?set=1"><button>Cookies setzen</button></a>
        <a href="?set_long=1"><button>Langzeit-Cookie setzen</button></a>
        <a href="?delete=1"><button>Cookies löschen</button></a>
        <a href="?delete_all=1"><button>ALLE Cookies löschen</button></a>
    </div>

    <h3>Cookie-Werte lesen</h3>

    <?php if (isset($_COOKIE['benutzername'])): ?>
        <div class="cookie-box">
            <p><strong>Benutzername-Cookie vorhanden!</strong></p>
            <p>Wert: <?php echo htmlspecialchars($_COOKIE['benutzername']); ?></p>
            <p>Lieblingsfarbe: <?php echo htmlspecialchars($_COOKIE['lieblingsfarbe'] ?? 'Nicht gesetzt'); ?></p>
        </div>
    <?php else: ?>
        <p><em>Keine Cookies gesetzt. Klicke auf "Cookies setzen" oben.</em></p>
    <?php endif; ?>

    <hr>

    <h3>Cookie-Statistiken</h3>
    <div class="cookie-box">
        <p><strong>Besuche dieser Seite:</strong> <?php echo $besuche; ?></p>
        <?php if ($letzter_besuch): ?>
            <p><strong>Letzter Besuch:</strong> <?php echo htmlspecialchars($letzter_besuch); ?></p>
        <?php else: ?>
            <p><strong>Letzter Besuch:</strong> Erster Besuch!</p>
        <?php endif; ?>
    </div>

    <hr>

    <h3>Alle aktuellen Cookies</h3>
    <?php if (empty($_COOKIE)): ?>
        <p><em>Keine Cookies vorhanden.</em></p>
    <?php else: ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Wert</th>
            </tr>
            <?php foreach ($_COOKIE as $name => $value): ?>
                <tr>
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <td><?php echo htmlspecialchars($value); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <hr>

    <h3>Cookie-Details</h3>
    <div class="cookie-box">
        <h4>setcookie() Parameter:</h4>
        <code>setcookie(name, value, expire, path, domain, secure, httponly);</code>

        <ul>
            <li><strong>name:</strong> Name des Cookies</li>
            <li><strong>value:</strong> Wert (String)</li>
            <li><strong>expire:</strong> Ablaufzeit (Unix-Timestamp)</li>
            <li><strong>path:</strong> Gültigkeitspfad ('/' = ganze Domain)</li>
            <li><strong>domain:</strong> Domain (meist leer)</li>
            <li><strong>secure:</strong> Nur über HTTPS (true/false)</li>
            <li><strong>httponly:</strong> Nicht per JavaScript lesbar (Sicherheit)</li>
        </ul>

        <h4>Ablaufzeiten:</h4>
        <ul>
            <li>1 Stunde: <code>time() + 3600</code></li>
            <li>1 Tag: <code>time() + 86400</code></li>
            <li>1 Woche: <code>time() + (7 * 24 * 60 * 60)</code></li>
            <li>1 Monat: <code>time() + (30 * 24 * 60 * 60)</code></li>
            <li>1 Jahr: <code>time() + (365 * 24 * 60 * 60)</code></li>
            <li>Session (bis Browser schließt): <em>expire weglassen</em></li>
        </ul>
    </div>

    <hr>
    <p><a href="06_cookie_remember_me.php">Weiter → Remember Me mit Cookies</a></p>
    <p><a href="01_session_grundlagen.php">← Zurück zur Übersicht</a></p>

</body>
</html>

<?php
/*
 * WICHTIGE PUNKTE ZU COOKIES:
 *
 * 1. setcookie() muss VOR jedem HTML stehen
 * 2. Cookies werden IM BROWSER gespeichert (nicht auf Server)
 * 3. Cookies können vom Benutzer gelöscht werden
 * 4. Maximale Größe: ca. 4 KB pro Cookie
 * 5. Cookies werden bei jedem Request mitgesendet
 *
 * COOKIE SETZEN:
 * setcookie('name', 'wert', time() + 3600, '/');
 *
 * COOKIE LESEN:
 * $wert = $_COOKIE['name'] ?? 'default';
 *
 * COOKIE LÖSCHEN:
 * setcookie('name', '', time() - 3600, '/');
 *
 * SICHERHEIT:
 * - Nie sensible Daten in Cookies (Passwörter, etc.)
 * - httponly = true verwenden (gegen XSS)
 * - secure = true bei HTTPS
 * - Werte immer mit htmlspecialchars() ausgeben
 *
 * SESSION vs. COOKIE:
 * - Session: Daten auf Server, verschwinden beim Browser schließen
 * - Cookie: Daten im Browser, bleiben erhalten
 *
 * WANN COOKIES VERWENDEN:
 * - "Remember Me" Funktion
 * - Sprach-Einstellungen
 * - Theme (Hell/Dunkel)
 * - Tracking/Analytics
 * - Zuletzt angesehene Produkte
 */
?>
