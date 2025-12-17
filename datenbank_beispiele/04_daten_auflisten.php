<?php
/**
 * Beispiel 4: Alle Daten aus Tabelle auslesen und anzeigen
 *
 * Zeigt verschiedene Methoden um Daten abzurufen
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Alle Personen aus der Datenbank</h3>";

    // SELECT Abfrage - holt alle Daten aus der Tabelle
    $stmt = $pdo->query("SELECT * FROM personen");

    // Anzahl der gefundenen Zeilen
    echo "Anzahl gefundener Personen: " . $stmt->rowCount() . "<br><br>";

    // METHODE 1: Mit fetch() - holt eine Zeile nach der anderen
    echo "<strong>Methode 1: fetch() in Schleife</strong><br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // FETCH_ASSOC: Ergebnis als assoziatives Array (Spaltenname => Wert)
        echo "ID: " . $row['id'] . " | ";
        echo "Name: " . $row['name'] . " | ";
        echo "Alter: " . $row['alter'] . " | ";
        echo "Stadt: " . $row['stadt'] . "<br>";
    }

    echo "<br>";

    // METHODE 2: Mit fetchAll() - holt alle Zeilen auf einmal
    echo "<strong>Methode 2: fetchAll() und foreach</strong><br>";
    $stmt = $pdo->query("SELECT * FROM personen");
    $allePersonen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allePersonen as $person) {
        echo "- " . $person['name'] . " (" . $person['alter'] . " Jahre) aus " . $person['stadt'] . "<br>";
    }

    echo "<br>";

    // METHODE 3: Als HTML Tabelle ausgeben
    echo "<strong>Methode 3: Als HTML Tabelle</strong><br>";
    $stmt = $pdo->query("SELECT * FROM personen");

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Alter</th><th>Stadt</th><th>Erstellt am</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['alter'] . "</td>";
        echo "<td>" . $row['stadt'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "<br>";

    // METHODE 4: Nur bestimmte Spalten abfragen
    echo "<strong>Methode 4: Nur bestimmte Spalten</strong><br>";
    $stmt = $pdo->query("SELECT name, stadt FROM personen");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['name'] . " wohnt in " . $row['stadt'] . "<br>";
    }

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * Fetch-Modi:
 * - FETCH_ASSOC: Array mit Spaltennamen als Keys
 * - FETCH_NUM: Array mit numerischen Indexes
 * - FETCH_BOTH: Beides kombiniert (Standard)
 * - FETCH_OBJ: Als Objekt
 *
 * Beispiel FETCH_OBJ:
 * while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
 *     echo $row->name;  // Mit Pfeil-Operator statt eckigen Klammern
 * }
 */
?>
