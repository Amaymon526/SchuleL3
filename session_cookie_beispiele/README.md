# Sessions & Cookies in PHP

Komplette Sammlung von praktischen Beispielen für Sessions und Cookies.

## Übersicht der Dateien

| Datei | Thema | Beschreibung |
|-------|-------|--------------|
| **01_session_grundlagen.php** | Session Basics | Session starten, Variablen setzen/lesen, Zähler |
| **02_session_login.php** | Login-System | Vollständiges Login mit Session-Verwaltung |
| **03_geschuetzte_seite.php** | Zugriffsschutz | Seiten vor nicht-eingeloggten Benutzern schützen |
| **04_session_warenkorb.php** | Warenkorb | Shopping-Cart mit Session |
| **05_cookie_grundlagen.php** | Cookie Basics | Cookies setzen, lesen, löschen |
| **06_cookie_remember_me.php** | Remember Me | Automatischer Login mit Cookies |
| **07_cookie_praktisch.php** | Praktische Anwendungen | Sprache, Theme, Zuletzt angesehen |
| **08_session_sicherheit.php** | Sicherheit | Session Hijacking, XSS, CSRF vermeiden |
| **10_session_loeschen.php** | Session löschen | Verschiedene Methoden für Logout |

---

## Was sind Sessions?

**Sessions** speichern Daten auf dem SERVER für einen bestimmten Benutzer.

### Eigenschaften:
- Daten auf dem Server gespeichert
- Jeder Benutzer bekommt eindeutige Session-ID
- Session-ID wird als Cookie im Browser gespeichert
- Verschwinden wenn Browser geschlossen wird (Standard)
- Sicherer als Cookies (Benutzer kann nicht manipulieren)

### Verwendung:
```php
session_start();  // IMMER am Anfang!

// Setzen
$_SESSION['name'] = 'Max';

// Lesen
echo $_SESSION['name'];

// Löschen
unset($_SESSION['name']);
```

---

## Was sind Cookies?

**Cookies** speichern Daten IM BROWSER des Benutzers.

### Eigenschaften:
- Daten im Browser gespeichert
- Können Ablaufzeit haben (bleiben nach Browser-Schließung)
- Benutzer kann sie sehen und löschen
- Werden bei jedem Request mitgesendet
- Maximale Größe: ca. 4 KB

### Verwendung:
```php
// Setzen (vor jedem HTML!)
setcookie('name', 'Max', time() + 3600, '/');

// Lesen
echo $_COOKIE['name'];

// Löschen
setcookie('name', '', time() - 3600, '/');
```

---

## Session vs. Cookie

| Eigenschaft | Session | Cookie |
|-------------|---------|--------|
| Speicherort | Server | Browser |
| Dauer | Bis Browser schließt* | Beliebig einstellbar |
| Größe | Unbegrenzt | ~4 KB |
| Sicherheit | Sicher | Kann manipuliert werden |
| Verwendung | Login, Warenkorb | Einstellungen, Remember Me |

*kann konfiguriert werden

---

## Sessions - Grundlagen

### 1. Session starten

```php
<?php
session_start();  // MUSS vor jedem HTML/echo stehen!
?>
<!DOCTYPE html>
...
```

**WICHTIG:**
- `session_start()` IMMER als erstes
- Vor jedem `echo`, `html`, Whitespace
- Auf JEDER Seite die Sessions nutzt

### 2. Session-Variablen

```php
// Setzen
$_SESSION['username'] = 'Max';
$_SESSION['alter'] = 25;
$_SESSION['eingeloggt'] = true;

// Arrays speichern
$_SESSION['warenkorb'] = ['Laptop', 'Maus'];
$_SESSION['einstellungen'] = [
    'sprache' => 'de',
    'theme' => 'dark'
];

// Lesen
if (isset($_SESSION['username'])) {
    echo "Hallo " . $_SESSION['username'];
}

// Löschen (einzelne Variable)
unset($_SESSION['username']);

// Alle Variablen löschen
session_unset();

// Session komplett zerstören
session_destroy();
```

### 3. Session-ID

```php
// Aktuelle Session-ID anzeigen
echo session_id();

// Neue Session-ID generieren (Sicherheit!)
session_regenerate_id(true);
```

---

## Cookies - Grundlagen

### 1. Cookie setzen

```php
// Einfach
setcookie('name', 'Max', time() + 3600, '/');

// Mit allen Optionen
setcookie('name', 'Max', [
    'expires' => time() + 3600,  // 1 Stunde
    'path' => '/',               // Ganze Domain
    'domain' => '',              // Aktuelle Domain
    'secure' => true,            // Nur HTTPS
    'httponly' => true,          // Nicht per JS lesbar
    'samesite' => 'Strict'       // CSRF-Schutz
]);
```

### 2. Ablaufzeiten

```php
// Session-Cookie (bis Browser schließt)
setcookie('name', 'wert');  // ohne expires

// 1 Stunde
time() + 3600

// 1 Tag
time() + 86400
time() + (24 * 60 * 60)

// 1 Woche
time() + (7 * 24 * 60 * 60)

// 1 Monat
time() + (30 * 24 * 60 * 60)

// 1 Jahr
time() + (365 * 24 * 60 * 60)
```

### 3. Cookie lesen

```php
// Prüfen und lesen
if (isset($_COOKIE['name'])) {
    echo $_COOKIE['name'];
}

// Mit Fallback
$name = $_COOKIE['name'] ?? 'Gast';
```

### 4. Cookie löschen

```php
// Ablaufzeit in Vergangenheit setzen
setcookie('name', '', time() - 3600, '/');

// Alle Cookies löschen
foreach ($_COOKIE as $name => $value) {
    setcookie($name, '', time() - 3600, '/');
}
```

---

## Praktische Anwendungen

### 1. Login-System

```php
session_start();

// Login
if ($_POST['username'] == 'admin' && $_POST['password'] == 'geheim') {
    $_SESSION['eingeloggt'] = true;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_id'] = 1;
}

// Prüfen ob eingeloggt
if (!isset($_SESSION['eingeloggt']) || !$_SESSION['eingeloggt']) {
    header('Location: login.php');
    exit();
}

// Logout
session_unset();
session_destroy();
```

### 2. Warenkorb

```php
session_start();

// Warenkorb initialisieren
if (!isset($_SESSION['warenkorb'])) {
    $_SESSION['warenkorb'] = [];
}

// Produkt hinzufügen
$_SESSION['warenkorb'][$produkt_id] = [
    'name' => 'Laptop',
    'preis' => 799.99,
    'menge' => 1
];

// Produkt entfernen
unset($_SESSION['warenkorb'][$produkt_id]);

// Warenkorb leeren
$_SESSION['warenkorb'] = [];
```

### 3. Remember Me

```php
// Login mit "Angemeldet bleiben"
if (isset($_POST['remember'])) {
    $token = bin2hex(random_bytes(32));
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
    // Token in Datenbank speichern
}

// Auto-Login prüfen
if (!isset($_SESSION['eingeloggt']) && isset($_COOKIE['remember_token'])) {
    // Token aus Datenbank holen und prüfen
    // Wenn gültig: Session setzen
}
```

### 4. Sprachauswahl

```php
// Sprache aus Cookie oder Standard
$sprache = $_COOKIE['sprache'] ?? 'de';

// Sprache ändern
if (isset($_GET['lang'])) {
    setcookie('sprache', $_GET['lang'], time() + (365 * 24 * 60 * 60), '/');
}
```

### 5. Theme (Hell/Dunkel)

```php
$theme = $_COOKIE['theme'] ?? 'hell';

if (isset($_GET['theme'])) {
    setcookie('theme', $_GET['theme'], time() + (365 * 24 * 60 * 60), '/');
}
```

### 6. Zuletzt angesehen

```php
// Aus Cookie holen
$zuletzt = json_decode($_COOKIE['zuletzt'] ?? '[]', true);

// Produkt hinzufügen
array_unshift($zuletzt, $produkt_id);
$zuletzt = array_unique($zuletzt);
$zuletzt = array_slice($zuletzt, 0, 5);

// In Cookie speichern
setcookie('zuletzt', json_encode($zuletzt), time() + (30 * 24 * 60 * 60), '/');
```

---

## Sicherheit

### Session-Sicherheit

#### 1. Session Hijacking vermeiden

```php
// Session-ID nach Login neu generieren
session_regenerate_id(true);

// User-Agent prüfen
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} else {
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        exit('Sicherheitswarnung!');
    }
}
```

#### 2. Session-Timeout

```php
$timeout = 1800; // 30 Minuten

if (isset($_SESSION['letzte_aktivitaet'])) {
    if (time() - $_SESSION['letzte_aktivitaet'] > $timeout) {
        session_unset();
        session_destroy();
    }
}
$_SESSION['letzte_aktivitaet'] = time();
```

#### 3. HTTPS verwenden

```php
// Session-Cookie nur über HTTPS
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
```

### Cookie-Sicherheit

#### Alle Sicherheitsoptionen nutzen

```php
setcookie('name', 'wert', [
    'expires' => time() + 3600,
    'path' => '/',
    'secure' => true,      // Nur HTTPS
    'httponly' => true,    // Nicht per JavaScript lesbar
    'samesite' => 'Strict' // CSRF-Schutz
]);
```

#### Nie sensible Daten in Cookies

```php
// ❌ NIEMALS:
setcookie('password', $password);  // FALSCH!
setcookie('kreditkarte', $nummer); // FALSCH!

// ✅ OKAY:
setcookie('theme', 'dark');
setcookie('sprache', 'de');
```

### XSS vermeiden

```php
// IMMER escapen bei Ausgabe
echo htmlspecialchars($_SESSION['username']);
echo htmlspecialchars($_COOKIE['name']);

// Niemals:
echo $_SESSION['text'];  // GEFÄHRLICH!
```

---

## Häufige Fehler und Lösungen

### 1. "Headers already sent"

**Problem:**
```php
<?php
echo "Hallo";
setcookie('name', 'Max');  // FEHLER!
```

**Lösung:**
```php
<?php
setcookie('name', 'Max');  // Zuerst!
echo "Hallo";
```

### 2. Session nicht verfügbar

**Problem:**
```php
$_SESSION['name'] = 'Max';  // session_start() fehlt!
```

**Lösung:**
```php
session_start();
$_SESSION['name'] = 'Max';
```

### 3. Cookie wird nicht gesetzt

**Gründe:**
- `setcookie()` nach HTML
- Falscher `path` Parameter
- Browser akzeptiert keine Cookies
- Cookie wurde gelöscht

### 4. Session bleibt nach Logout

**Problem:**
```php
session_destroy();  // Cookie bleibt!
```

**Lösung:**
```php
session_unset();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();
```

---

## Beste Vorgehensweise (Best Practices)

### Sessions

1. ✅ `session_start()` IMMER zuerst
2. ✅ `session_regenerate_id()` nach Login
3. ✅ Session-Timeout implementieren
4. ✅ Über HTTPS verwenden
5. ✅ User-Agent speichern und prüfen
6. ✅ Bei Logout komplett löschen

### Cookies

1. ✅ `httponly => true` verwenden
2. ✅ `secure => true` bei HTTPS
3. ✅ `samesite => 'Strict'` setzen
4. ✅ Ablaufzeit immer definieren
5. ✅ Nie Passwörter speichern
6. ✅ Ausgaben escapen

### Allgemein

1. ✅ HTTPS in Produktion
2. ✅ Eingaben validieren
3. ✅ Ausgaben escapen
4. ✅ CSRF-Token bei Formularen
5. ✅ Passwörter hashen
6. ✅ Prepared Statements bei DB

---

## Checkliste für Login-System

- [ ] `session_start()` auf allen Seiten
- [ ] `session_regenerate_id()` nach Login
- [ ] Session-Timeout implementiert
- [ ] Logout löscht alles (Session + Cookie)
- [ ] Passwörter werden gehasht
- [ ] Geschützte Seiten prüfen Login-Status
- [ ] `header()` mit `exit()` verwenden
- [ ] Alle Ausgaben escapen
- [ ] HTTPS verwenden
- [ ] Cookie-Flags gesetzt (httponly, secure, samesite)

---

## Debugging

### Session-Informationen anzeigen

```php
session_start();

echo "Session-ID: " . session_id() . "<br>";
echo "Session-Name: " . session_name() . "<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
```

### Cookie-Informationen anzeigen

```php
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

// In Browser Dev-Tools:
// Application → Cookies
```

### Session-Dateien prüfen

Windows XAMPP: `C:\xampp\tmp`

---

## Weiterführende Themen

- **Session-Handler** - Eigenen Session-Speicher (z.B. Datenbank)
- **JWT** - JSON Web Tokens als Alternative
- **OAuth** - Login mit Google/Facebook
- **2FA** - Zwei-Faktor-Authentifizierung
- **Rate Limiting** - Bruteforce-Schutz
- **CSRF-Token** - Cross-Site Request Forgery Schutz

---

## Zusammenfassung

**Sessions** für:
- Login-Status
- Warenkorb
- Temporäre Daten
- Zwischen Seiten teilen

**Cookies** für:
- "Remember Me"
- Einstellungen (Sprache, Theme)
- Tracking
- Daten die bleiben sollen

**Sicherheit:**
- Sessions: `session_regenerate_id()`, Timeout, HTTPS
- Cookies: `httponly`, `secure`, `samesite`
- Immer: Ausgaben escapen, Eingaben validieren

**Bei Logout:**
```php
session_unset();
setcookie(session_name(), '', time() - 3600, '/');
session_destroy();
```

Die Beispieldateien zeigen alles Schritt für Schritt!
