<?php
/**
 * Beispiel 1: Session Grundlagen
 *
 * Was sind Sessions?
 * - Daten die für einen Benutzer über mehrere Seiten gespeichert werden
 * - Werden auf dem Server gespeichert (nicht im Browser)
 * - Jeder Benutzer bekommt eine eindeutige Session-ID
 * - Perfekt für Login-Status, Warenkorb, temporäre Daten
 */

// WICHTIG: session_start() muss IMMER ganz am Anfang stehen
// Vor jedem HTML, echo, etc.
session_start();

echo "<h3>Session Grundlagen</h3>";

// BEISPIEL 1: Session-Variable setzen
$_SESSION['benutzername'] = 'Max';
$_SESSION['alter'] = 25;
$_SESSION['email'] = 'max@example.com';

echo "<strong>Beispiel 1: Session-Variablen gesetzt</strong><br>";
echo "Benutzername: " . $_SESSION['benutzername'] . "<br>";
echo "Alter: " . $_SESSION['alter'] . "<br>";
echo "Email: " . $_SESSION['email'] . "<br><br>";

// BEISPIEL 2: Session-Variable lesen
if (isset($_SESSION['benutzername'])) {
    echo "<strong>Beispiel 2: Session-Variable existiert</strong><br>";
    echo "Willkommen zurück, " . $_SESSION['benutzername'] . "!<br><br>";
} else {
    echo "Keine Session-Variable gesetzt.<br><br>";
}

// BEISPIEL 3: Session-Variable ändern
$_SESSION['alter'] = 26;  // Alter hochzählen
echo "<strong>Beispiel 3: Alter geändert auf " . $_SESSION['alter'] . "</strong><br><br>";

// BEISPIEL 4: Mehrere Werte in Array speichern
$_SESSION['einstellungen'] = [
    'sprache' => 'de',
    'theme' => 'dark',
    'benachrichtigungen' => true
];

echo "<strong>Beispiel 4: Array in Session</strong><br>";
echo "Sprache: " . $_SESSION['einstellungen']['sprache'] . "<br>";
echo "Theme: " . $_SESSION['einstellungen']['theme'] . "<br>";
echo "Benachrichtigungen: " . ($_SESSION['einstellungen']['benachrichtigungen'] ? 'Ja' : 'Nein') . "<br><br>";

// BEISPIEL 5: Einzelne Session-Variable löschen
unset($_SESSION['email']);
echo "<strong>Beispiel 5: Email-Variable gelöscht</strong><br>";
echo "Email existiert noch? " . (isset($_SESSION['email']) ? 'Ja' : 'Nein') . "<br><br>";

// BEISPIEL 6: Session-ID anzeigen
echo "<strong>Beispiel 6: Session-ID</strong><br>";
echo "Deine Session-ID: " . session_id() . "<br>";
echo "(Diese ID ist eindeutig für deinen Browser)<br><br>";

// BEISPIEL 7: Alle Session-Variablen anzeigen
echo "<strong>Beispiel 7: Alle aktuellen Session-Variablen</strong><br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";

// BEISPIEL 8: Session-Zähler (Besucherzähler)
if (!isset($_SESSION['besuche'])) {
    $_SESSION['besuche'] = 0;
}
$_SESSION['besuche']++;

echo "<strong>Beispiel 8: Besucherzähler</strong><br>";
echo "Du hast diese Seite " . $_SESSION['besuche'] . " mal besucht.<br>";
echo "(Aktualisiere die Seite um hochzuzählen)<br><br>";

// BEISPIEL 9: Zeitstempel speichern
if (!isset($_SESSION['erste_besuch'])) {
    $_SESSION['erste_besuch'] = time();
}

$zeitVergangen = time() - $_SESSION['erste_besuch'];
echo "<strong>Beispiel 9: Zeit seit erstem Besuch</strong><br>";
echo "Erster Besuch: " . date('H:i:s', $_SESSION['erste_besuch']) . "<br>";
echo "Vergangene Zeit: " . $zeitVergangen . " Sekunden<br><br>";

echo "<hr>";

// Navigation zu anderen Beispielen
echo "<h3>Navigation</h3>";
echo "<a href='02_session_login.php'>Weiter zu Beispiel 2: Session Login</a><br>";
echo "<a href='10_session_loeschen.php'>Session komplett löschen</a><br>";

/*
 * WICHTIGE PUNKTE:
 *
 * 1. session_start() muss IMMER am Anfang stehen
 * 2. Sessions bleiben aktiv bis Browser geschlossen wird
 * 3. Sessions werden auf dem Server gespeichert
 * 4. $_SESSION ist ein superglobales Array
 * 5. Session-Daten sind nur für den aktuellen Benutzer sichtbar
 *
 * Wann Sessions verwenden:
 * - Login-Status speichern
 * - Warenkorb
 * - Formulardaten zwischen Seiten
 * - Benutzer-Einstellungen
 * - Temporäre Daten
 *
 * Session vs. Datenbank:
 * - Session: Temporäre Daten, verschwinden wenn Browser schließt
 * - Datenbank: Permanente Daten, bleiben gespeichert
 */
?>
