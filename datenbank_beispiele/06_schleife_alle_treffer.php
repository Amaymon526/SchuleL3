<?php
/**
 * Beispiel 6: Alle Treffer mit Schleife durchgehen
 *
 * Zeigt wie man ALLE Personen mit einem bestimmten Namen findet
 * und mit einer Schleife durchgeht
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Zuerst fügen wir mehrere Personen mit gleichem Vornamen ein
    echo "<h3>Testdaten vorbereiten</h3>";

    // Prüfen ob schon Jeffs existieren, sonst einfügen
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM personen WHERE name LIKE ?");
    $stmt->execute(['Jeff%']);
    $anzahlJeffs = $stmt->fetchColumn();

    if ($anzahlJeffs == 0) {
        $stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (?, ?, ?)");
        $stmt->execute(['Jeff Miller', 28, 'New York']);
        $stmt->execute(['Jeff Brown', 32, 'Los Angeles']);
        $stmt->execute(['Jeff Wilson', 25, 'Chicago']);
        $stmt->execute(['Jeff Davis', 30, 'Houston']);
        echo "4 Personen namens Jeff eingefügt.<br><br>";
    }

    // BEISPIEL 1: Alle Jeffs finden und durchgehen
    echo "<h3>Alle Personen die 'Jeff' im Namen haben</h3>";

    $suchName = 'Jeff%';  // % = alles was nach Jeff kommt
    $stmt = $pdo->prepare("SELECT * FROM personen WHERE name LIKE ?");
    $stmt->execute([$suchName]);

    // Anzahl der Treffer prüfen
    $anzahl = $stmt->rowCount();
    echo "Anzahl gefundener Personen: $anzahl<br><br>";

    // Durch alle Treffer loopen
    $counter = 1;
    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Treffer $counter:<br>";
        echo "- Name: " . $person['name'] . "<br>";
        echo "- Alter: " . $person['alter'] . "<br>";
        echo "- Stadt: " . $person['stadt'] . "<br>";
        echo "- ID: " . $person['id'] . "<br><br>";
        $counter++;
    }

    echo "<hr>";

    // BEISPIEL 2: Alle Treffer sammeln und dann verarbeiten
    echo "<h3>Beispiel 2: Alle Jeffs sammeln und verarbeiten</h3>";

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE name LIKE ?");
    $stmt->execute(['Jeff%']);

    // Alle Ergebnisse in Array speichern
    $alleJeffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($alleJeffs) > 0) {
        echo "Gefunden: " . count($alleJeffs) . " Personen<br><br>";

        // Jetzt können wir mehrfach durch die Daten gehen
        echo "<strong>Namen-Liste:</strong><br>";
        foreach ($alleJeffs as $jeff) {
            echo "- " . $jeff['name'] . "<br>";
        }

        echo "<br><strong>Durchschnittsalter berechnen:</strong><br>";
        $summeAlter = 0;
        foreach ($alleJeffs as $jeff) {
            $summeAlter += $jeff['alter'];
        }
        $durchschnitt = $summeAlter / count($alleJeffs);
        echo "Durchschnittsalter aller Jeffs: " . round($durchschnitt, 1) . " Jahre<br>";

        echo "<br><strong>Nach Alter sortiert:</strong><br>";
        // Array nach Alter sortieren
        usort($alleJeffs, function($a, $b) {
            return $a['alter'] - $b['alter'];
        });

        foreach ($alleJeffs as $jeff) {
            echo $jeff['name'] . " - " . $jeff['alter'] . " Jahre<br>";
        }

    } else {
        echo "Keine Personen mit dem Namen Jeff gefunden.<br>";
    }

    echo "<hr>";

    // BEISPIEL 3: Durch Ergebnisse loopen und nur bestimmte anzeigen
    echo "<h3>Beispiel 3: Nur Jeffs über 27 Jahre</h3>";

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE name LIKE ?");
    $stmt->execute(['Jeff%']);

    $gefunden = 0;
    while ($person = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Bedingung innerhalb der Schleife prüfen
        if ($person['alter'] > 27) {
            echo "- " . $person['name'] . " (" . $person['alter'] . " Jahre)<br>";
            $gefunden++;
        }
    }

    if ($gefunden == 0) {
        echo "Keine Jeffs über 27 Jahre gefunden.<br>";
    }

    echo "<hr>";

    // BEISPIEL 4: Do-While Schleife (selten verwendet, aber möglich)
    echo "<h3>Beispiel 4: Mit Do-While Schleife</h3>";

    $stmt = $pdo->prepare("SELECT * FROM personen WHERE name LIKE ?");
    $stmt->execute(['Jeff%']);

    if ($stmt->rowCount() > 0) {
        // Erste Zeile holen
        $person = $stmt->fetch(PDO::FETCH_ASSOC);

        do {
            echo "- " . $person['name'] . " aus " . $person['stadt'] . "<br>";
        } while ($person = $stmt->fetch(PDO::FETCH_ASSOC));
        // Schleife läuft bis keine Zeilen mehr da sind
    }

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * Wichtige Punkte:
 *
 * 1. fetch() holt EINE Zeile, muss in Schleife verwendet werden
 * 2. fetchAll() holt ALLE Zeilen auf einmal in ein Array
 * 3. while-Schleife läuft bis fetch() false zurückgibt (keine Daten mehr)
 * 4. rowCount() gibt Anzahl der Ergebnisse zurück
 * 5. fetchColumn() holt nur den Wert einer Spalte (nützlich für COUNT)
 *
 * Wann welche Methode:
 * - fetch() in while: Wenn große Datenmengen, spart Speicher
 * - fetchAll(): Wenn Daten mehrfach durchgegangen werden müssen
 * - foreach: Wenn fetchAll() verwendet wurde
 */
?>
