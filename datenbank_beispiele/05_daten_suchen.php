<?php
/**
 * Beispiel 5: Daten suchen und vergleichen
 *
 * Zeigt wie man nach bestimmten Werten sucht
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // BEISPIEL 1: Nach einem bestimmten Namen suchen
    echo "<h3>Beispiel 1: Nach Namen suchen</h3>";
    $suchName = 'Max Mustermann';

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE name = ?");
    $stmt->execute([$suchName]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($person) {
        echo "Person gefunden!<br>";
        echo "Name: " . $person['name'] . "<br>";
        echo "Alter: " . $person['alter'] . "<br>";
        echo "Stadt: " . $person['stadt'] . "<br>";
    } else {
        echo "Keine Person mit dem Namen '$suchName' gefunden.<br>";
    }

    echo "<br>";

    // BEISPIEL 2: Alle Personen aus einer bestimmten Stadt
    echo "<h3>Beispiel 2: Alle Personen aus Berlin</h3>";
    $stadt = 'Berlin';

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE stadt = ?");
    $stmt->execute([$stadt]);

    $anzahl = $stmt->rowCount();
    echo "Gefundene Personen in $stadt: $anzahl<br><br>";

    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $person['name'] . " (" . $person['alter'] . " Jahre)<br>";
    }

    echo "<br>";

    // BEISPIEL 3: Personen älter als ein bestimmtes Alter
    echo "<h3>Beispiel 3: Personen älter als 25</h3>";
    $minAlter = 25;

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE alter > ?");
    $stmt->execute([$minAlter]);

    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $person['name'] . " ist " . $person['alter'] . " Jahre alt<br>";
    }

    echo "<br>";

    // BEISPIEL 4: Mehrere Bedingungen kombinieren (UND)
    echo "<h3>Beispiel 4: Personen aus München UND älter als 25</h3>";
    $stmt = $pdo->prepare("SELECT * FROM personen WHERE stadt = ? AND alter > ?");
    $stmt->execute(['München', 25]);

    if ($stmt->rowCount() > 0) {
        while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $person['name'] . "<br>";
        }
    } else {
        echo "Keine Personen gefunden.<br>";
    }

    echo "<br>";

    // BEISPIEL 5: Mehrere Bedingungen (ODER)
    echo "<h3>Beispiel 5: Personen aus Berlin ODER Hamburg</h3>";
    $stmt = $pdo->prepare("SELECT * FROM personen WHERE stadt = ? OR stadt = ?");
    $stmt->execute(['Berlin', 'Hamburg']);

    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $person['name'] . " aus " . $person['stadt'] . "<br>";
    }

    echo "<br>";

    // BEISPIEL 6: Teil eines Namens suchen (LIKE)
    echo "<h3>Beispiel 6: Namen die 'Max' enthalten</h3>";
    $suchbegriff = '%Max%';  // % = Platzhalter für beliebige Zeichen

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE name LIKE ?");
    $stmt->execute([$suchbegriff]);

    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $person['name'] . "<br>";
    }

    echo "<br>";

    // BEISPIEL 7: Sortieren
    echo "<h3>Beispiel 7: Nach Alter sortiert (aufsteigend)</h3>";
    $stmt = $pdo->query("SELECT * FROM personen ORDER BY alter ASC");

    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $person['name'] . " (" . $person['alter'] . " Jahre)<br>";
    }

    echo "<br>";

    // BEISPIEL 8: Limit - nur erste 3 Ergebnisse
    echo "<h3>Beispiel 8: Nur die ersten 3 Personen</h3>";
    $stmt = $pdo->query("SELECT * FROM personen LIMIT 3");

    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $person['name'] . "<br>";
    }

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * Vergleichs-Operatoren:
 * - = : Gleich
 * - != oder <> : Ungleich
 * - > : Größer als
 * - < : Kleiner als
 * - >= : Größer oder gleich
 * - <= : Kleiner oder gleich
 * - LIKE : Muster-Suche mit % als Platzhalter
 * - IN : Ist in Liste enthalten
 *
 * Logische Operatoren:
 * - AND : Beide Bedingungen müssen erfüllt sein
 * - OR : Mindestens eine Bedingung muss erfüllt sein
 * - NOT : Negiert die Bedingung
 *
 * LIKE Muster:
 * - 'Max%' : Beginnt mit Max
 * - '%mann' : Endet mit mann
 * - '%Max%' : Enthält Max irgendwo
 */
?>
