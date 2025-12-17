<?php
/**
 * Beispiel 2: Tabelle in der Datenbank erstellen
 *
 * Zeigt wie man eine neue Tabelle anlegt und auf Existenz prüft
 */

// Verbindung herstellen (wie in 01_verbindung.php)
$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prüfen ob Tabelle bereits existiert
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'personen'");

    if ($tableCheck->rowCount() > 0) {
        echo "Tabelle 'personen' existiert bereits.<br>";
    } else {
        // SQL-Befehl zum Erstellen einer Tabelle
        $sql = "CREATE TABLE personen (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            alter INT,
            stadt VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        // Tabelle erstellen
        $pdo->exec($sql);
        echo "Tabelle 'personen' erfolgreich erstellt!<br>";
    }

    // Erklärung der Spalten-Typen:
    echo "<br><strong>Spalten-Erklärung:</strong><br>";
    echo "- id: Eindeutige Nummer, automatisch hochzählend<br>";
    echo "- name: Text bis 50 Zeichen, darf nicht leer sein<br>";
    echo "- alter: Ganze Zahl<br>";
    echo "- stadt: Text bis 50 Zeichen<br>";
    echo "- created_at: Zeitstempel, wird automatisch gesetzt<br>";

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * Wichtige Datentypen:
 * - INT: Ganze Zahlen
 * - VARCHAR(n): Text mit maximaler Länge n
 * - TEXT: Längere Texte
 * - TIMESTAMP/DATETIME: Datum und Uhrzeit
 * - FLOAT/DECIMAL: Kommazahlen
 *
 * Wichtige Optionen:
 * - PRIMARY KEY: Hauptschlüssel, eindeutig
 * - AUTO_INCREMENT: Zählt automatisch hoch
 * - NOT NULL: Darf nicht leer sein
 * - UNIQUE: Wert muss eindeutig sein
 * - DEFAULT: Standardwert wenn nichts angegeben
 */
?>
