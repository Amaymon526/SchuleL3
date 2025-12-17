<?php
/**
 * Beispiel 9: Transaktionen und Fehlerbehandlung
 *
 * Zeigt wie man mehrere Operationen zusammenfasst
 * und bei Fehlern rückgängig macht
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // TRANSAKTIONEN - Alles oder Nichts
    echo "<h3>Transaktionen - Alles oder Nichts</h3>";

    /*
     * Was sind Transaktionen?
     * - Mehrere Operationen werden zusammengefasst
     * - Entweder werden ALLE ausgeführt oder KEINE
     * - Wichtig bei zusammenhängenden Operationen (z.B. Geldüberweisung)
     */

    // BEISPIEL 1: Erfolgreiche Transaktion
    echo "<strong>Beispiel 1: Erfolgreiche Transaktion</strong><br>";

    try {
        // Transaktion starten
        $pdo->beginTransaction();

        // Mehrere Operationen durchführen
        $stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (?, ?, ?)");
        $stmt->execute(['Sarah Meyer', 29, 'Leipzig']);

        $stmt->execute(['Tim Schulz', 31, 'Düsseldorf']);

        // Alles war erfolgreich - Änderungen übernehmen
        $pdo->commit();

        echo "Transaktion erfolgreich! 2 Personen eingefügt.<br><br>";

    } catch(PDOException $e) {
        // Bei Fehler: Alle Änderungen rückgängig machen
        $pdo->rollBack();
        echo "Transaktion fehlgeschlagen: " . $e->getMessage() . "<br><br>";
    }

    // BEISPIEL 2: Fehlgeschlagene Transaktion
    echo "<strong>Beispiel 2: Transaktion mit Fehler (wird rückgängig gemacht)</strong><br>";

    try {
        $pdo->beginTransaction();

        // Erste Operation klappt
        $stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (?, ?, ?)");
        $stmt->execute(['Julia Wolf', 27, 'Bremen']);
        echo "- Julia Wolf eingefügt<br>";

        // Zweite Operation verursacht Fehler (name darf nicht NULL sein)
        $stmt->execute([null, 25, 'Hannover']);  // Fehler!

        // Diese Zeile wird nie erreicht
        $pdo->commit();

    } catch(PDOException $e) {
        // Rollback: Julia Wolf wird NICHT in Datenbank sein
        $pdo->rollBack();
        echo "- Fehler aufgetreten! Alle Änderungen rückgängig gemacht.<br>";
        echo "- Fehlermeldung: " . $e->getMessage() . "<br><br>";
    }

    echo "<hr>";

    // FEHLERBEHANDLUNG
    echo "<h3>Fehlerbehandlung mit try-catch</h3>";

    // BEISPIEL 3: Doppelten Eintrag abfangen
    echo "<strong>Beispiel 3: Duplikat-Fehler abfangen</strong><br>";

    // Zuerst erstellen wir eine Tabelle mit UNIQUE constraint
    $pdo->exec("CREATE TABLE IF NOT EXISTS emails (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) UNIQUE NOT NULL
    )");

    // Email einfügen
    try {
        $stmt = $pdo->prepare("INSERT INTO emails (email) VALUES (?)");
        $stmt->execute(['test@example.com']);
        echo "Email eingefügt.<br>";
    } catch(PDOException $e) {
        // Prüfen ob es ein Duplikat-Fehler ist
        if ($e->getCode() == 23000) {
            echo "Diese Email existiert bereits!<br>";
        } else {
            echo "Anderer Fehler: " . $e->getMessage() . "<br>";
        }
    }

    // Nochmal die gleiche Email (wird fehlschlagen)
    try {
        $stmt = $pdo->prepare("INSERT INTO emails (email) VALUES (?)");
        $stmt->execute(['test@example.com']);
        echo "Email eingefügt.<br>";
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Diese Email existiert bereits!<br>";
        }
    }

    echo "<br><hr>";

    // BEISPIEL 4: Prüfen vor dem Einfügen (besser)
    echo "<strong>Beispiel 4: Vor dem Einfügen prüfen</strong><br>";

    $neueEmail = 'neu@example.com';

    // Erst prüfen ob Email schon existiert
    $stmt = $pdo->prepare("SELECT id FROM emails WHERE email = ?");
    $stmt->execute([$neueEmail]);

    if ($stmt->rowCount() > 0) {
        echo "Email '$neueEmail' existiert bereits.<br>";
    } else {
        // Nur einfügen wenn noch nicht vorhanden
        $stmt = $pdo->prepare("INSERT INTO emails (email) VALUES (?)");
        $stmt->execute([$neueEmail]);
        echo "Email '$neueEmail' wurde eingefügt.<br>";
    }

    echo "<br><hr>";

    // BEISPIEL 5: Komplexe Transaktion (Geldüberweisung-Simulation)
    echo "<strong>Beispiel 5: Komplexe Transaktion</strong><br>";

    // Konten-Tabelle erstellen
    $pdo->exec("CREATE TABLE IF NOT EXISTS konten (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50),
        guthaben DECIMAL(10,2)
    )");

    // Testdaten (nur wenn Tabelle leer)
    $stmt = $pdo->query("SELECT COUNT(*) FROM konten");
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO konten (name, guthaben) VALUES (?, ?)");
        $stmt->execute(['Konto A', 1000.00]);
        $stmt->execute(['Konto B', 500.00]);
    }

    // Geldüberweisung: 100 Euro von Konto A zu Konto B
    $betrag = 100.00;

    try {
        $pdo->beginTransaction();

        // Von Konto A abziehen
        $stmt = $pdo->prepare("UPDATE konten SET guthaben = guthaben - ? WHERE name = ?");
        $stmt->execute([$betrag, 'Konto A']);

        // Zu Konto B hinzufügen
        $stmt = $pdo->prepare("UPDATE konten SET guthaben = guthaben + ? WHERE name = ?");
        $stmt->execute([$betrag, 'Konto B']);

        // Prüfen ob Konto A nicht ins Minus gerutscht ist
        $stmt = $pdo->prepare("SELECT guthaben FROM konten WHERE name = ?");
        $stmt->execute(['Konto A']);
        $guthaben = $stmt->fetchColumn();

        if ($guthaben < 0) {
            throw new Exception("Nicht genug Guthaben!");
        }

        // Alles OK - Überweisung durchführen
        $pdo->commit();
        echo "Überweisung erfolgreich: $betrag Euro von A nach B<br>";

    } catch(Exception $e) {
        $pdo->rollBack();
        echo "Überweisung fehlgeschlagen: " . $e->getMessage() . "<br>";
    }

    // Kontostand anzeigen
    $stmt = $pdo->query("SELECT * FROM konten");
    echo "<br>Aktuelle Kontostände:<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['name'] . ": " . $row['guthaben'] . " Euro<br>";
    }

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * TRANSAKTIONEN:
 *
 * beginTransaction() - Transaktion starten
 * commit() - Änderungen übernehmen
 * rollBack() - Änderungen rückgängig machen
 *
 * Wann verwenden:
 * - Geldtransfers (Betrag abziehen UND hinzufügen)
 * - Mehrere zusammenhängende Datensätze einfügen
 * - Komplexe Updates die zusammengehören
 *
 * FEHLERBEHANDLUNG:
 *
 * try { } - Code der Fehler verursachen könnte
 * catch(PDOException $e) { } - Was bei Fehler passiert
 *
 * Wichtige Fehler-Codes:
 * - 23000: Duplikat oder Constraint-Verletzung
 * - 42S02: Tabelle existiert nicht
 * - HY000: Allgemeiner Fehler
 *
 * Best Practices:
 * - Immer try-catch bei Datenbank-Operationen
 * - Transaktionen bei zusammenhängenden Operationen
 * - Sinnvolle Fehlermeldungen für Benutzer
 * - Technische Details nur im Entwicklungsmodus zeigen
 */
?>
