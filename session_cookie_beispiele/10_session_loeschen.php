<?php
/**
 * Beispiel 10: Session komplett löschen
 *
 * Verschiedene Methoden um Sessions zu beenden
 */

session_start();

$message = '';

// Methode auswählen
if (isset($_GET['methode'])) {
    $methode = $_GET['methode'];

    switch ($methode) {
        case 'unset':
            // METHODE 1: Nur Session-Variablen löschen (Session bleibt aktiv)
            session_unset();
            $message = 'Session-Variablen gelöscht (Session bleibt aktiv)';
            break;

        case 'destroy':
            // METHODE 2: Session komplett zerstören
            session_destroy();
            session_start(); // Neue Session für Meldung
            $message = 'Session komplett zerstört!';
            break;

        case 'komplett':
            // METHODE 3: Alles löschen (empfohlen für Logout)
            // 1. Session-Variablen löschen
            session_unset();

            // 2. Session-Cookie löschen
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }

            // 3. Session zerstören
            session_destroy();

            session_start(); // Neue Session für Meldung
            $message = 'Session komplett gelöscht (inkl. Cookie)!';
            break;

        case 'einzeln':
            // METHODE 4: Einzelne Variable löschen
            unset($_SESSION['test_variable']);
            $message = 'Variable "test_variable" gelöscht';
            break;
    }
}

// Testvariablen setzen wenn keine da sind
if (!isset($_SESSION['test_variable'])) {
    $_SESSION['test_variable'] = 'Testwert';
    $_SESSION['zahl'] = 42;
    $_SESSION['array'] = ['a', 'b', 'c'];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Session löschen</title>
    <style>
        .message { padding: 15px; margin: 10px 0; }
        .box { padding: 20px; margin: 15px 0; }
        button { padding: 10px 15px; margin: 5px; }
        pre { padding: 15px; overflow-x: auto; }
        code { padding: 2px 6px; }
    </style>
</head>
<body>
    <h1>Session löschen - Verschiedene Methoden</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="box">
        <h2>Aktuelle Session-Variablen:</h2>
        <?php if (empty($_SESSION)): ?>
            <p><em>Keine Session-Variablen vorhanden</em></p>
        <?php else: ?>
            <pre><?php print_r($_SESSION); ?></pre>
        <?php endif; ?>

        <p><strong>Session-ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Session-Name (Cookie):</strong> <?php echo session_name(); ?></p>
    </div>

    <div class="box">
        <h2>Lösch-Methoden:</h2>

        <h3>Methode 1: session_unset()</h3>
        <p>Löscht nur die Session-Variablen, Session bleibt aktiv</p>
        <a href="?methode=unset"><button class="warning">session_unset() ausführen</button></a>
        <pre>session_unset();</pre>

        <h3>Methode 2: session_destroy()</h3>
        <p>Zerstört die Session komplett (Session-Datei auf Server)</p>
        <a href="?methode=destroy"><button class="danger">session_destroy() ausführen</button></a>
        <pre>session_destroy();</pre>

        <h3>Methode 3: Komplett (empfohlen für Logout)</h3>
        <p>Löscht alles: Variablen + Cookie + Session</p>
        <a href="?methode=komplett"><button class="danger">Komplett löschen</button></a>
        <pre>// Session-Variablen löschen
session_unset();

// Session-Cookie löschen
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Session zerstören
session_destroy();</pre>

        <h3>Methode 4: Einzelne Variable</h3>
        <p>Löscht nur eine bestimmte Variable</p>
        <a href="?methode=einzeln"><button>Einzelne Variable löschen</button></a>
        <pre>unset($_SESSION['variable_name']);</pre>
    </div>

    <div class="box">
        <h2>📋 Verwendung:</h2>

        <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
            <tr>
                <th>Methode</th>
                <th>Wann verwenden?</th>
                <th>Code</th>
            </tr>
            <tr>
                <td>unset()</td>
                <td>Einzelne Variable löschen</td>
                <td><code>unset($_SESSION['name'])</code></td>
            </tr>
            <tr>
                <td>session_unset()</td>
                <td>Alle Variablen löschen, Session behalten</td>
                <td><code>session_unset()</code></td>
            </tr>
            <tr>
                <td>session_destroy()</td>
                <td>Session zerstören (aber Cookie bleibt)</td>
                <td><code>session_destroy()</code></td>
            </tr>
            <tr>
                <td>Komplett</td>
                <td><strong>Logout (empfohlen!)</strong></td>
                <td><code>unset + cookie löschen + destroy</code></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <h2>💡 Logout-Funktion (Best Practice)</h2>

        <pre>function logout() {
    session_start();

    // 1. Alle Session-Variablen löschen
    session_unset();

    // 2. Session-Cookie löschen
    if (isset($_COOKIE[session_name()])) {
        setcookie(
            session_name(),
            '',
            time() - 3600,
            '/'
        );
    }

    // 3. Session zerstören
    session_destroy();

    // 4. Zur Login-Seite weiterleiten
    header('Location: login.php');
    exit();
}

// Verwendung:
if (isset($_GET['logout'])) {
    logout();
}</pre>
    </div>

    <div class="box">
        <h2>⚠️ Häufige Fehler:</h2>

        <h3>❌ FALSCH:</h3>
        <pre>// Nur session_destroy() ohne session_unset()
session_destroy();  // Variablen bleiben im Speicher!

// Session-Cookie nicht gelöscht
session_destroy();  // Cookie bleibt im Browser!</pre>

        <h3>✅ RICHTIG:</h3>
        <pre>// Alles löschen
session_unset();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();</pre>
    </div>

    <hr>
    <p><a href="01_session_grundlagen.php">← Zurück zur Übersicht</a></p>

</body>
</html>

<?php
/*
 * SESSION LÖSCHEN - ZUSAMMENFASSUNG:
 *
 * 1. unset($_SESSION['var'])
 *    - Löscht eine einzelne Variable
 *    - Session bleibt aktiv
 *
 * 2. session_unset()
 *    - Löscht ALLE Session-Variablen
 *    - Session bleibt aktiv
 *    - $_SESSION ist danach leer
 *
 * 3. session_destroy()
 *    - Zerstört die Session auf dem Server
 *    - Löscht Session-Datei
 *    - Cookie bleibt im Browser!
 *    - Muss nach session_start() aufgerufen werden
 *
 * 4. KOMPLETT (für Logout):
 *    a) session_unset() - Variablen löschen
 *    b) Cookie löschen - setcookie(..., time() - 3600)
 *    c) session_destroy() - Session zerstören
 *
 * WICHTIG:
 * - Für Logout IMMER Methode 4 (Komplett) verwenden!
 * - Danach zur Login-Seite weiterleiten
 * - exit() nach header() verwenden
 */
?>
