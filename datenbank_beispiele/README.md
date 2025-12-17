# Datenbank Beispiele mit PHP PDO

Diese Sammlung zeigt alle wichtigen Datenbank-Operationen mit PHP und PDO.

## Übersicht der Dateien

| Datei | Thema | Was wird gezeigt |
|-------|-------|------------------|
| **01_verbindung.php** | Datenbankverbindung | Wie man eine Verbindung zur Datenbank herstellt |
| **02_tabelle_erstellen.php** | Tabelle erstellen | Wie man Tabellen anlegt und auf Existenz prüft |
| **03_daten_einfuegen.php** | INSERT | Daten sicher in Tabelle einfügen |
| **04_daten_auflisten.php** | SELECT | Alle Daten auslesen und anzeigen |
| **05_daten_suchen.php** | WHERE, LIKE | Nach bestimmten Daten suchen |
| **06_schleife_alle_treffer.php** | Schleifen | Alle Treffer durchgehen und verarbeiten |
| **07_update_delete.php** | UPDATE, DELETE | Daten ändern und löschen |
| **08_erweiterte_funktionen.php** | COUNT, MAX, MIN, etc. | Aggregat-Funktionen und mehr |
| **09_transaktionen_fehler.php** | Transaktionen | Mehrere Operationen zusammenfassen |

---

## Was ist PDO?

**PDO** (PHP Data Objects) ist die moderne Art in PHP mit Datenbanken zu arbeiten.

### Vorteile von PDO:
- Funktioniert mit verschiedenen Datenbanken (MySQL, PostgreSQL, SQLite, etc.)
- Schutz vor SQL Injection durch Prepared Statements
- Bessere Fehlerbehandlung
- Objektorientiert und modern

### Alternative: mysqli
Es gibt auch mysqli, aber PDO ist flexibler und wird empfohlen für neue Projekte.

---

## Erste Schritte

### 1. Vorbereitung

**XAMPP starten:**
1. XAMPP Control Panel öffnen
2. Apache und MySQL starten

**Datenbank erstellen:**
1. Browser öffnen: `http://localhost/phpmyadmin`
2. "Neue Datenbank" klicken
3. Name: `schule_db`
4. Erstellen

### 2. Dateien ausführen

Die Beispiele der Reihe nach durchgehen:
1. `01_verbindung.php` - Verbindung testen
2. `02_tabelle_erstellen.php` - Tabelle anlegen
3. `03_daten_einfuegen.php` - Daten einfügen
4. usw.

Im Browser aufrufen: `http://localhost/SchuleL3/datenbank_beispiele/01_verbindung.php`

---

## Grundkonzepte

### Datenbankverbindung

```php
$pdo = new PDO("mysql:host=localhost;dbname=schule_db;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

**Wichtig:**
- `host`: Server-Adresse (bei XAMPP immer localhost)
- `dbname`: Name deiner Datenbank
- `charset=utf8mb4`: Für Umlaute und Sonderzeichen
- `ERRMODE_EXCEPTION`: Fehler als Exceptions werfen

### Prepared Statements (WICHTIG!)

**FALSCH - unsicher:**
```php
$name = $_POST['name'];
$sql = "SELECT * FROM personen WHERE name = '$name'";  // SQL Injection möglich!
```

**RICHTIG - sicher:**
```php
$name = $_POST['name'];
$stmt = $pdo->prepare("SELECT * FROM personen WHERE name = ?");
$stmt->execute([$name]);  // Automatisch escaped und sicher
```

**Warum wichtig?**
- Ohne Prepared Statements kann jemand SQL-Code einschleusen
- Beispiel: Eingabe `'; DROP TABLE personen; --` würde Tabelle löschen
- Mit Prepared Statements ist das unmöglich

---

## Die 4 wichtigsten SQL-Befehle

### 1. SELECT - Daten lesen

```php
// Alle Daten
$stmt = $pdo->query("SELECT * FROM personen");

// Mit Bedingung
$stmt = $pdo->prepare("SELECT * FROM personen WHERE alter > ?");
$stmt->execute([25]);

// Ergebnisse holen
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['name'];
}
```

### 2. INSERT - Daten einfügen

```php
$stmt = $pdo->prepare("INSERT INTO personen (name, alter, stadt) VALUES (?, ?, ?)");
$stmt->execute(['Max', 25, 'Berlin']);

// ID des eingefügten Datensatzes
$id = $pdo->lastInsertId();
```

### 3. UPDATE - Daten ändern

```php
$stmt = $pdo->prepare("UPDATE personen SET alter = ? WHERE id = ?");
$stmt->execute([26, 1]);

// Anzahl geänderter Zeilen
$anzahl = $stmt->rowCount();
```

### 4. DELETE - Daten löschen

```php
$stmt = $pdo->prepare("DELETE FROM personen WHERE id = ?");
$stmt->execute([1]);

// Anzahl gelöschter Zeilen
$anzahl = $stmt->rowCount();
```

**ACHTUNG:** Ohne WHERE werden ALLE Zeilen gelöscht!

---

## Wichtige WHERE-Bedingungen

```php
// Gleich
WHERE name = 'Max'

// Ungleich
WHERE alter != 25

// Größer/Kleiner
WHERE alter > 25
WHERE alter >= 25

// Mehrere Bedingungen (UND)
WHERE stadt = 'Berlin' AND alter > 25

// Mehrere Bedingungen (ODER)
WHERE stadt = 'Berlin' OR stadt = 'München'

// Teil eines Strings (LIKE)
WHERE name LIKE '%Max%'  // Enthält "Max"
WHERE name LIKE 'Max%'   // Beginnt mit "Max"
WHERE name LIKE '%mann'  // Endet mit "mann"

// In Liste
WHERE stadt IN ('Berlin', 'München', 'Hamburg')

// Bereich
WHERE alter BETWEEN 20 AND 30

// Leer/Nicht leer
WHERE alter IS NULL
WHERE alter IS NOT NULL
```

---

## Schleifen für Ergebnisse

### Methode 1: while mit fetch()

```php
$stmt = $pdo->query("SELECT * FROM personen");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['name'] . "<br>";
}
```

**Vorteil:** Speichersparend bei großen Datenmengen

### Methode 2: fetchAll() und foreach

```php
$stmt = $pdo->query("SELECT * FROM personen");
$personen = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($personen as $person) {
    echo $person['name'] . "<br>";
}
```

**Vorteil:** Kann mehrfach durchlaufen werden

---

## Fetch-Modi

```php
// FETCH_ASSOC - Assoziatives Array (empfohlen)
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row['name'];

// FETCH_NUM - Numerisches Array
$row = $stmt->fetch(PDO::FETCH_NUM);
echo $row[0];

// FETCH_OBJ - Als Objekt
$row = $stmt->fetch(PDO::FETCH_OBJ);
echo $row->name;
```

---

## Aggregat-Funktionen

```php
// Anzahl zählen
SELECT COUNT(*) as anzahl FROM personen

// Höchster Wert
SELECT MAX(alter) as max_alter FROM personen

// Niedrigster Wert
SELECT MIN(alter) as min_alter FROM personen

// Durchschnitt
SELECT AVG(alter) as durchschnitt FROM personen

// Summe
SELECT SUM(alter) as summe FROM personen

// Gruppieren
SELECT stadt, COUNT(*) as anzahl FROM personen GROUP BY stadt
```

---

## Transaktionen

Für zusammenhängende Operationen (z.B. Geldüberweisung):

```php
try {
    $pdo->beginTransaction();

    // Operation 1
    $stmt = $pdo->prepare("UPDATE konten SET guthaben = guthaben - ? WHERE id = ?");
    $stmt->execute([100, 1]);

    // Operation 2
    $stmt = $pdo->prepare("UPDATE konten SET guthaben = guthaben + ? WHERE id = ?");
    $stmt->execute([100, 2]);

    // Alles OK - übernehmen
    $pdo->commit();

} catch(PDOException $e) {
    // Fehler - rückgängig machen
    $pdo->rollBack();
}
```

**Wichtig:** Entweder werden ALLE Operationen durchgeführt oder KEINE

---

## Fehlerbehandlung

```php
try {
    $stmt = $pdo->prepare("INSERT INTO personen (name) VALUES (?)");
    $stmt->execute([$name]);

} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();

    // Fehler-Code prüfen
    if ($e->getCode() == 23000) {
        echo "Eintrag existiert bereits!";
    }
}
```

**Häufige Fehler-Codes:**
- `23000`: Duplikat oder Constraint-Verletzung
- `42S02`: Tabelle existiert nicht
- `HY000`: Allgemeiner Fehler

---

## Wichtige Sicherheits-Tipps

### 1. IMMER Prepared Statements verwenden

```php
// FALSCH - SQL Injection möglich
$sql = "SELECT * FROM users WHERE name = '$name'";

// RICHTIG - Sicher
$stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
$stmt->execute([$name]);
```

### 2. Niemals Passwörter im Klartext speichern

```php
// Beim Registrieren
$hash = password_hash($passwort, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (password) VALUES (?)");
$stmt->execute([$hash]);

// Beim Login
$stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (password_verify($passwort, $user['password'])) {
    // Login erfolgreich
}
```

### 3. Eingaben validieren

```php
// Email validieren
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Ungültige Email");
}

// Alter als Zahl prüfen
if (!is_numeric($alter)) {
    die("Alter muss eine Zahl sein");
}
```

### 4. Bei DELETE und UPDATE immer WHERE verwenden

```php
// GEFÄHRLICH - löscht ALLES
DELETE FROM personen

// RICHTIG - löscht nur bestimmte Zeile
DELETE FROM personen WHERE id = ?
```

---

## Häufige Fehler und Lösungen

### Fehler: "Access denied for user"
**Problem:** Falscher Benutzername oder Passwort
**Lösung:** Bei XAMPP ist Standard: User=`root`, Password=leer

### Fehler: "Unknown database"
**Problem:** Datenbank existiert nicht
**Lösung:** Datenbank in phpMyAdmin erstellen

### Fehler: "Table doesn't exist"
**Problem:** Tabelle wurde nicht erstellt
**Lösung:** Erst `02_tabelle_erstellen.php` ausführen

### Fehler: "Call to a member function fetch() on bool"
**Problem:** Query ist fehlgeschlagen
**Lösung:** Fehler-Modus aktivieren und Fehlermeldung prüfen

### Leere Ausgabe bei fetch()
**Problem:** Keine Daten gefunden oder Cursor am Ende
**Lösung:** `rowCount()` prüfen oder Query überprüfen

---

## Best Practices

1. **Immer try-catch verwenden** bei Datenbank-Operationen
2. **Prepared Statements** für alle Queries mit Variablen
3. **Fehler-Modus aktivieren** (`ERRMODE_EXCEPTION`)
4. **Passwörter hashen** mit `password_hash()`
5. **Eingaben validieren** bevor sie in DB kommen
6. **Transaktionen** bei zusammenhängenden Operationen
7. **LIMIT** verwenden bei großen Datenmengen
8. **Indexes** auf häufig gesuchte Spalten setzen

---

## Nützliche Funktionen

```php
// Anzahl Ergebnisse
$anzahl = $stmt->rowCount();

// ID des letzten INSERT
$id = $pdo->lastInsertId();

// Nur einen Wert holen
$anzahl = $stmt->fetchColumn();

// Prüfen ob Ergebnisse da sind
if ($stmt->rowCount() > 0) {
    // Daten vorhanden
}
```

---

## Weiterführende Themen

Nach diesen Grundlagen kannst du dich beschäftigen mit:

- **JOIN**: Daten aus mehreren Tabellen verbinden
- **Indizes**: Queries schneller machen
- **Foreign Keys**: Beziehungen zwischen Tabellen
- **Views**: Virtuelle Tabellen für komplexe Queries
- **Stored Procedures**: SQL-Code in der Datenbank speichern
- **Backup/Restore**: Datenbank sichern

---

## Hilfe und Dokumentation

- [PHP PDO Dokumentation](https://www.php.net/manual/de/book.pdo.php)
- [MySQL Dokumentation](https://dev.mysql.com/doc/)
- [W3Schools SQL Tutorial](https://www.w3schools.com/sql/)

---

## Zusammenfassung

1. **Verbindung** herstellen mit PDO
2. **Prepared Statements** für Sicherheit verwenden
3. **SELECT** zum Lesen, **INSERT** zum Einfügen
4. **UPDATE** zum Ändern, **DELETE** zum Löschen
5. **WHERE** für Bedingungen
6. **Schleifen** für mehrere Ergebnisse
7. **Transaktionen** für zusammenhängende Operationen
8. **try-catch** für Fehlerbehandlung

Die Beispiel-Dateien zeigen alles Schritt für Schritt mit vielen Kommentaren!
