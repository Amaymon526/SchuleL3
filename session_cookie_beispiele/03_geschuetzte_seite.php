<?php
/**
 * Beispiel 3: Geschützte Seite
 *
 * Zeigt wie man Seiten vor nicht-eingeloggten Benutzern schützt
 */

// Session starten
session_start();

// Prüfen ob Benutzer eingeloggt ist
if (!isset($_SESSION['eingeloggt']) || $_SESSION['eingeloggt'] !== true) {
    // Nicht eingeloggt - zurück zum Login
    header('Location: 02_session_login.php');
    exit();
}

// Ab hier ist Zugriff nur für eingeloggte Benutzer möglich
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Geschützter Bereich</title>
    <style>
        .protected { padding: 20px; margin: 20px 0; }
        .admin-only { padding: 15px; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Geschützter Bereich</h2>

    <div class="protected">
        <h3>✅ Zugriff erlaubt</h3>
        <p>Willkommen, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
        <p>Du siehst diese Seite weil du eingeloggt bist.</p>
    </div>

    <h3>Deine Session-Informationen:</h3>
    <ul>
        <li>Benutzername: <?php echo htmlspecialchars($_SESSION['username']); ?></li>
        <li>Rolle: <?php echo htmlspecialchars($_SESSION['user_role']); ?></li>
        <li>Eingeloggt seit: <?php echo date('H:i:s', $_SESSION['login_zeit']); ?></li>
        <li>Dauer: <?php echo (time() - $_SESSION['login_zeit']); ?> Sekunden</li>
    </ul>

    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <!-- Nur für Admins sichtbar -->
        <div class="admin-only">
            <h3>👑 Admin-Bereich</h3>
            <p>Dieser Bereich ist nur für Administratoren sichtbar!</p>
            <ul>
                <li>Benutzer verwalten</li>
                <li>System-Einstellungen</li>
                <li>Statistiken</li>
            </ul>
        </div>
    <?php else: ?>
        <p><em>Du hast keine Admin-Rechte. Logge dich als "admin" ein um mehr zu sehen.</em></p>
    <?php endif; ?>

    <hr>

    <h3>Weitere geschützte Bereiche:</h3>
    <ul>
        <li><a href="04_session_warenkorb.php">Mein Warenkorb</a></li>
        <li><a href="05_session_profil.php">Mein Profil</a></li>
    </ul>

    <p><a href="02_session_login.php?logout=1">Ausloggen</a></p>

</body>
</html>

<?php
/*
 * SEITE SCHÜTZEN - MUSTER:
 *
 * session_start();
 *
 * if (!isset($_SESSION['eingeloggt']) || $_SESSION['eingeloggt'] !== true) {
 *     header('Location: login.php');
 *     exit();
 * }
 *
 * // Ab hier: Nur für eingeloggte Benutzer
 *
 * WICHTIG:
 * - session_start() IMMER zuerst
 * - header() vor jedem HTML
 * - exit() nach header() um Script zu beenden
 * - !== true für strikte Prüfung
 *
 * ROLLEN-BASIERTER ZUGRIFF:
 *
 * if ($_SESSION['user_role'] !== 'admin') {
 *     die('Nur für Admins!');
 * }
 *
 * ALTERNATIVE: Function für Zugriffsprüfung
 *
 * function pruefe_login() {
 *     session_start();
 *     if (!isset($_SESSION['eingeloggt']) || !$_SESSION['eingeloggt']) {
 *         header('Location: login.php');
 *         exit();
 *     }
 * }
 *
 * pruefe_login();  // Am Anfang jeder geschützten Seite
 */
?>
