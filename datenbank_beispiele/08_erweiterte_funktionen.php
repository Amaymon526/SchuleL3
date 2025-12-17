<?php
/**
 * Beispiel 8: Erweiterte Funktionen
 *
 * COUNT, MAX, MIN, AVG, GROUP BY und mehr
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // COUNT - Anzahl zählen
    echo "<h3>COUNT - Anzahl zählen</h3>";

    // Alle Personen zählen
    $stmt = $pdo->query("SELECT COUNT(*) as anzahl FROM personen");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Gesamtanzahl Personen: " . $result['anzahl'] . "<br>";

    // Personen in einer Stadt zählen
    $stmt = $pdo->prepare("SELECT COUNT(*) as anzahl FROM personen WHERE stadt = ?");
    $stmt->execute(['Berlin']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Personen in Berlin: " . $result['anzahl'] . "<br><br>";

    // MAX - Höchster Wert
    echo "<h3>MAX - Höchster Wert</h3>";

    $stmt = $pdo->query("SELECT MAX(alter) as max_alter FROM personen");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Älteste Person ist: " . $result['max_alter'] . " Jahre<br><br>";

    // MIN - Kleinster Wert
    echo "<h3>MIN - Kleinster Wert</h3>";

    $stmt = $pdo->query("SELECT MIN(alter) as min_alter FROM personen");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Jüngste Person ist: " . $result['min_alter'] . " Jahre<br><br>";

    // AVG - Durchschnitt
    echo "<h3>AVG - Durchschnitt</h3>";

    $stmt = $pdo->query("SELECT AVG(alter) as durchschnitt FROM personen");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Durchschnittsalter: " . round($result['durchschnitt'], 1) . " Jahre<br><br>";

    // SUM - Summe
    echo "<h3>SUM - Summe</h3>";

    $stmt = $pdo->query("SELECT SUM(alter) as gesamt FROM personen");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Summe aller Alter: " . $result['gesamt'] . "<br><br>";

    echo "<hr>";

    // GROUP BY - Gruppieren
    echo "<h3>GROUP BY - Nach Stadt gruppieren</h3>";

    $stmt = $pdo->query("SELECT stadt, COUNT(*) as anzahl FROM personen GROUP BY stadt");

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Stadt</th><th>Anzahl Personen</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['stadt'] . "</td>";
        echo "<td>" . $row['anzahl'] . "</td>";
        echo "</tr>";
    }

    echo "</table><br>";

    // GROUP BY mit AVG
    echo "<h3>Durchschnittsalter pro Stadt</h3>";

    $stmt = $pdo->query("SELECT stadt, AVG(alter) as durchschnitt FROM personen GROUP BY stadt");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['stadt'] . ": " . round($row['durchschnitt'], 1) . " Jahre<br>";
    }

    echo "<br><hr>";

    // DISTINCT - Nur einzigartige Werte
    echo "<h3>DISTINCT - Alle verschiedenen Städte</h3>";

    $stmt = $pdo->query("SELECT DISTINCT stadt FROM personen ORDER BY stadt");

    echo "Städte in der Datenbank: ";
    $staedte = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $staedte[] = $row['stadt'];
    }
    echo implode(', ', $staedte) . "<br><br>";

    echo "<hr>";

    // BETWEEN - Wertebereich
    echo "<h3>BETWEEN - Personen zwischen 25 und 30 Jahre</h3>";

    $stmt = $pdo->query("SELECT * FROM personen WHERE alter BETWEEN 25 AND 30");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['name'] . " (" . $row['alter'] . " Jahre)<br>";
    }

    echo "<br><hr>";

    // IN - Ist in Liste
    echo "<h3>IN - Personen aus Berlin, München oder Hamburg</h3>";

    $stmt = $pdo->query("SELECT * FROM personen WHERE stadt IN ('Berlin', 'München', 'Hamburg')");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['name'] . " aus " . $row['stadt'] . "<br>";
    }

    echo "<br><hr>";

    // IS NULL / IS NOT NULL
    echo "<h3>IS NULL - Personen ohne Altersangabe</h3>";

    $stmt = $pdo->query("SELECT * FROM personen WHERE alter IS NULL");

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['name'] . "<br>";
        }
    } else {
        echo "Alle Personen haben eine Altersangabe.<br>";
    }

    echo "<br><hr>";

    // LIMIT und OFFSET - Pagination
    echo "<h3>LIMIT und OFFSET - Pagination</h3>";

    $seite = 1;  // Aktuelle Seite
    $proSeite = 2;  // Einträge pro Seite
    $offset = ($seite - 1) * $proSeite;

    $stmt = $pdo->prepare("SELECT * FROM personen LIMIT ? OFFSET ?");
    $stmt->execute([$proSeite, $offset]);

    echo "Seite $seite (Einträge " . ($offset + 1) . " bis " . ($offset + $proSeite) . "):<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['name'] . "<br>";
    }

    echo "<br><hr>";

    // CONCAT - Strings verbinden
    echo "<h3>CONCAT - Strings verbinden</h3>";

    $stmt = $pdo->query("SELECT CONCAT(name, ' aus ', stadt) as info FROM personen");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['info'] . "<br>";
    }

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * Aggregat-Funktionen:
 * - COUNT(*): Anzahl Zeilen
 * - MAX(spalte): Höchster Wert
 * - MIN(spalte): Niedrigster Wert
 * - AVG(spalte): Durchschnitt
 * - SUM(spalte): Summe
 *
 * Weitere wichtige SQL-Befehle:
 * - GROUP BY: Gruppiert Ergebnisse
 * - HAVING: Filtert gruppierte Ergebnisse (wie WHERE für GROUP BY)
 * - DISTINCT: Nur einzigartige Werte
 * - BETWEEN: Wertebereich
 * - IN: Ist in Liste enthalten
 * - IS NULL / IS NOT NULL: Auf leere Werte prüfen
 * - LIMIT: Anzahl Ergebnisse begrenzen
 * - OFFSET: Ergebnisse überspringen (für Pagination)
 *
 * String-Funktionen:
 * - CONCAT(): Strings verbinden
 * - UPPER(): In Großbuchstaben
 * - LOWER(): In Kleinbuchstaben
 * - LENGTH(): Länge des Strings
 */
?>
