<?php
/**
 * Beispiel 1: Datenbankverbindung mit PDO herstellen
 *
 * PDO (PHP Data Objects) ist eine Schnittstelle für Datenbank-Zugriff in PHP.
 * Vorteil: Funktioniert mit verschiedenen Datenbanken (MySQL, PostgreSQL, etc.)
 */

// Verbindungsdaten festlegen
$host = 'localhost';        // Server-Adresse (bei XAMPP immer localhost)
$dbname = 'schule_db';      // Name der Datenbank
$username = 'root';         // Benutzername (bei XAMPP Standard: root)
$password = '';             // Passwort (bei XAMPP Standard: leer)

try {
    // PDO-Objekt erstellen - stellt Verbindung her
    // Format: "mysql:host=SERVER;dbname=DATENBANKNAME;charset=utf8mb4"
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Fehler-Modus einstellen
    // ERRMODE_EXCEPTION wirft bei Fehlern eine Exception (Fehler wird abgefangen)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Verbindung erfolgreich hergestellt!<br>";
    echo "Datenbankname: " . $dbname;

} catch(PDOException $e) {
    // Wird ausgeführt wenn Verbindung fehlschlägt
    echo "Verbindung fehlgeschlagen: " . $e->getMessage();
}

// WICHTIG: PDO-Verbindung wird automatisch geschlossen wenn Script endet
// Manuelle Schließung (optional): $pdo = null;
?>
