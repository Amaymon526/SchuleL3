<?php
/**
 * Beispiel 7: Daten ändern (UPDATE) und löschen (DELETE)
 *
 * Zeigt wie man bestehende Daten aktualisiert oder entfernt
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // UPDATE - Daten ändern
    echo "<h3>UPDATE - Daten ändern</h3>";

    // BEISPIEL 1: Eine einzelne Person ändern (nach ID)
    echo "<strong>Beispiel 1: Alter von Person mit ID 1 ändern</strong><br>";

    $stmt = $pdo->prepare("UPDATE personen SET alter = ? WHERE id = ?");
    $stmt->execute([26, 1]);  // Alter auf 26, bei ID 1

    echo "Anzahl geänderter Zeilen: " . $stmt->rowCount() . "<br><br>";

    // BEISPIEL 2: Mehrere Felder gleichzeitig ändern
    echo "<strong>Beispiel 2: Name und Stadt ändern</strong><br>";

    $stmt = $pdo->prepare("UPDATE personen SET name = ?, stadt = ? WHERE id = ?");
    $stmt->execute(['Max Neumann', 'Stuttgart', 1]);

    echo "Person mit ID 1 wurde aktualisiert.<br><br>";

    // BEISPIEL 3: Alle Personen aus einer Stadt updaten
    echo "<strong>Beispiel 3: Alter aller Personen aus Berlin +1</strong><br>";

    $stmt = $pdo->prepare("UPDATE personen SET alter = alter + 1 WHERE stadt = ?");
    $stmt->execute(['Berlin']);

    echo "Anzahl geänderter Personen: " . $stmt->rowCount() . "<br><br>";

    // BEISPIEL 4: Mit benannten Platzhaltern
    echo "<strong>Beispiel 4: Person nach Namen suchen und ändern</strong><br>";

    $stmt = $pdo->prepare("UPDATE personen SET stadt = :stadt WHERE name = :name");
    $stmt->execute([
        ':stadt' => 'Dresden',
        ':name' => 'Anna Schmidt'
    ]);

    if ($stmt->rowCount() > 0) {
        echo "Anna Schmidt wohnt jetzt in Dresden.<br><br>";
    } else {
        echo "Person nicht gefunden.<br><br>";
    }

    echo "<hr>";

    // DELETE - Daten löschen
    echo "<h3>DELETE - Daten löschen</h3>";

    // BEISPIEL 5: Eine Person nach ID löschen
    echo "<strong>Beispiel 5: Person mit ID 999 löschen</strong><br>";

    $stmt = $pdo->prepare("DELETE FROM personen WHERE id = ?");
    $stmt->execute([999]);

    if ($stmt->rowCount() > 0) {
        echo "Person wurde gelöscht.<br><br>";
    } else {
        echo "Keine Person mit ID 999 gefunden.<br><br>";
    }

    // BEISPIEL 6: Alle Personen aus einer Stadt löschen
    echo "<strong>Beispiel 6: Alle Personen aus Frankfurt löschen</strong><br>";

    $stmt = $pdo->prepare("DELETE FROM personen WHERE stadt = ?");
    $stmt->execute(['Frankfurt']);

    echo "Anzahl gelöschter Personen: " . $stmt->rowCount() . "<br><br>";

    // BEISPIEL 7: Nach Namen löschen
    echo "<strong>Beispiel 7: Person nach Namen löschen</strong><br>";

    $stmt = $pdo->prepare("DELETE FROM personen WHERE name = ?");
    $stmt->execute(['Tom Weber']);

    if ($stmt->rowCount() > 0) {
        echo "Tom Weber wurde gelöscht.<br><br>";
    } else {
        echo "Tom Weber nicht gefunden.<br><br>";
    }

    // BEISPIEL 8: Mit mehreren Bedingungen löschen
    echo "<strong>Beispiel 8: Personen unter 20 Jahre aus Hamburg löschen</strong><br>";

    $stmt = $pdo->prepare("DELETE FROM personen WHERE alter < ? AND stadt = ?");
    $stmt->execute([20, 'Hamburg']);

    echo "Anzahl gelöschter Personen: " . $stmt->rowCount() . "<br><br>";

    echo "<hr>";

    // SICHERHEIT - Vor dem Löschen prüfen
    echo "<h3>SICHERHEIT - Vor dem Löschen/Ändern prüfen</h3>";

    $zuLoeschendeId = 2;

    // Erst prüfen ob Datensatz existiert
    $stmt = $pdo->prepare("SELECT * FROM personen WHERE id = ?");
    $stmt->execute([$zuLoeschendeId]);
    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($person) {
        echo "Person gefunden: " . $person['name'] . "<br>";
        echo "Möchtest du diese Person wirklich löschen? (In Echtanwendung: Bestätigung)<br><br>";

        // Nur löschen wenn bestätigt (hier als Beispiel direkt)
        // $stmt = $pdo->prepare("DELETE FROM personen WHERE id = ?");
        // $stmt->execute([$zuLoeschendeId]);
        // echo "Person wurde gelöscht.<br>";
    } else {
        echo "Person mit ID $zuLoeschendeId nicht gefunden.<br>";
    }

    echo "<hr>";

    // Aktuelle Daten anzeigen
    echo "<h3>Aktuelle Personen in der Datenbank</h3>";
    $stmt = $pdo->query("SELECT * FROM personen");

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Alter</th><th>Stadt</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['alter'] . "</td>";
        echo "<td>" . $row['stadt'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * WICHTIGE HINWEISE:
 *
 * UPDATE:
 * - Immer WHERE verwenden, sonst werden ALLE Zeilen geändert!
 * - rowCount() gibt Anzahl der geänderten Zeilen zurück
 * - Kann mehrere Felder gleichzeitig ändern
 *
 * DELETE:
 * - VORSICHT: Ohne WHERE werden ALLE Zeilen gelöscht!
 * - Gelöschte Daten können nicht wiederhergestellt werden
 * - Immer vor dem Löschen prüfen ob Datensatz existiert
 * - Bei wichtigen Daten: Soft-Delete verwenden (Feld "deleted" auf 1 setzen)
 *
 * Best Practices:
 * - Immer Prepared Statements verwenden
 * - Vor DELETE/UPDATE prüfen ob Datensatz existiert
 * - Bei kritischen Operationen: Bestätigung vom Benutzer einholen
 * - Transaktionen verwenden bei mehreren zusammenhängenden Änderungen
 */
?>
