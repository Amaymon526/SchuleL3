<?php
/**
 * Beispiel 3: Daten in Tabelle einfügen (INSERT)
 *
 * Zeigt wie man neue Datensätze sicher einfügt
 */

$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Daten einfügen</h3>";

    // METHODE 1: Prepared Statement (EMPFOHLEN - sicher gegen SQL Injection)
    // Fragezeichen (?) sind Platzhalter für Werte
    $stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (?, ?, ?)");

    // Werte in Array übergeben
    $stmt->execute(['Max Mustermann', 25, 'Berlin']);
    echo "Person 1 eingefügt (ID: " . $pdo->lastInsertId() . ")<br>";

    $stmt->execute(['Anna Schmidt', 30, 'München']);
    echo "Person 2 eingefügt (ID: " . $pdo->lastInsertId() . ")<br>";

    $stmt->execute(['Peter Klein', 22, 'Hamburg']);
    echo "Person 3 eingefügt (ID: " . $pdo->lastInsertId() . ")<br>";

    // METHODE 2: Benannte Platzhalter (übersichtlicher bei vielen Feldern)
    $stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (:name, :alter, :stadt)");

    // Werte als assoziatives Array
    $stmt->execute([
        ':name' => 'Lisa Müller',
        ':alter' => 28,
        ':stadt' => 'Köln'
    ]);
    echo "Person 4 eingefügt (ID: " . $pdo->lastInsertId() . ")<br>";

    // METHODE 3: Mit Variablen
    $name = 'Tom Weber';
    $alter = 35;
    $stadt = 'Frankfurt';

    $stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (?, ?, ?)");
    $stmt->execute([$name, $alter, $stadt]);
    echo "Person 5 eingefügt (ID: " . $pdo->lastInsertId() . ")<br>";

    echo "<br><strong>Alle Daten erfolgreich eingefügt!</strong>";

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

/*
 * WICHTIG - Prepared Statements verwenden!
 *
 * FALSCH (unsicher):
 * $sql = "INSERT INTO personen (name) VALUES ('$name')";
 * Problem: SQL Injection möglich!
 *
 * RICHTIG (sicher):
 * $stmt = $pdo->prepare("INSERT INTO personen (name) VALUES (?)");
 * $stmt->execute([$name]);
 *
 * Vorteile Prepared Statements:
 * - Schutz vor SQL Injection
 * - Automatisches Escaping von Sonderzeichen
 * - Bessere Performance bei mehrfacher Ausführung
 */
?>
