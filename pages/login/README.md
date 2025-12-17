# Login Systeme - Übersicht

Diese Dateien zeigen verschiedene Methoden für Login- und Registrierungssysteme in PHP.

## Ordnerstruktur

### pdo/
Login mit Datenbank ohne Passwort-Hashing
- login.php
- register.php

### pdo_hash/
Login mit Datenbank mit Passwort-Hashing
- login.php
- register.php

### txt/
Login mit TXT-Datei ohne Passwort-Hashing
- login.php
- register.php
- users.txt (wird automatisch erstellt)

### txt_hash/
Login mit TXT-Datei mit Passwort-Hashing
- login.php
- register.php
- users_hash.txt (wird automatisch erstellt)

---

## Was ist ein Login-System?

Ein Login-System prüft ob ein Benutzer die richtigen Zugangsdaten eingibt. Es besteht aus zwei Teilen:

1. **Registrierung** - Neuer Benutzer erstellt Account mit Benutzername und Passwort
2. **Login** - Benutzer gibt Zugangsdaten ein und wird eingeloggt wenn sie stimmen

---

## Datenbank vs. Textdatei

### Datenbank (PDO)
- Benutzerdaten werden in MySQL Datenbank gespeichert
- PDO (PHP Data Objects) stellt Verbindung zur Datenbank her
- Vorteil: Schneller bei vielen Benutzern, strukturierter
- Braucht: MySQL Server und Datenbanktabelle

**Benötigte Tabelle:**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
```

### Textdatei
- Benutzerdaten werden in einer .txt Datei gespeichert
- Jede Zeile = ein Benutzer (Format: username:password)
- Vorteil: Keine Datenbank nötig, einfacher für Tests
- Nachteil: Langsamer bei vielen Benutzern

---

## Was ist Passwort-Hashing?

### Ohne Hash (UNSICHER!)
Das Passwort wird im Klartext gespeichert.
```
Benutzer gibt ein: "meinPasswort123"
Gespeichert wird: "meinPasswort123"
```
**Problem:** Wenn jemand die Datenbank/Datei liest, sieht er alle Passwörter direkt.

### Mit Hash (SICHER)
Das Passwort wird vor dem Speichern verschlüsselt.
```
Benutzer gibt ein: "meinPasswort123"
Gespeichert wird: "$2y$10$abcd1234..."
```
**Vorteil:** Selbst wenn jemand die Daten stiehlt, kann er das Passwort nicht zurückrechnen.

### PHP Funktionen für Hashing
```php
// Passwort hashen bei Registrierung
$hash = password_hash($passwort, PASSWORD_DEFAULT);

// Passwort prüfen beim Login
if (password_verify($eingegebenesPasswort, $gespeicherterHash)) {
    // Login erfolgreich
}
```

---

## Welche Ordner soll ich nutzen?

**Für echte Projekte:**
- Nutze **pdo_hash/** (Ordner mit login.php und register.php)
- Datenbank + Hashing = am sichersten

**Zum Lernen/Testen:**
- Nutze **txt_hash/** (Ordner mit login.php und register.php)
- Keine Datenbank nötig, trotzdem mit Hashing

**Niemals für echte Projekte:**
- Die Ordner **pdo/** und **txt/** ohne Hash sind nur zur Demonstration
- Passwörter im Klartext zu speichern ist extrem unsicher

---

## Setup

### Für PDO Versionen:
1. MySQL Server starten (XAMPP Control Panel)
2. Datenbank `schule_db` erstellen
3. Tabelle `users` mit SQL-Befehl oben erstellen
4. Datei im Browser öffnen

### Für TXT Versionen:
1. Einfach Datei im Browser öffnen
2. Die .txt Datei wird automatisch erstellt
