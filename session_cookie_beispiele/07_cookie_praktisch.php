<?php
/**
 * Beispiel 7: Praktische Cookie-Anwendungen
 *
 * Sprachauswahl, Theme, Zuletzt angesehen, etc.
 */

// Sprache aus Cookie holen oder Standard
$sprache = $_COOKIE['sprache'] ?? 'de';

// Theme aus Cookie holen oder Standard
$theme = $_COOKIE['theme'] ?? 'hell';

// Schriftgröße
$schriftgroesse = $_COOKIE['schriftgroesse'] ?? 'mittel';

// Sprache ändern
if (isset($_GET['sprache'])) {
    $neueSprache = $_GET['sprache'];
    if (in_array($neueSprache, ['de', 'en', 'fr'])) {
        setcookie('sprache', $neueSprache, time() + (365 * 24 * 60 * 60), '/');
        $sprache = $neueSprache;
    }
}

// Theme ändern
if (isset($_GET['theme'])) {
    $neuesTheme = $_GET['theme'];
    if (in_array($neuesTheme, ['hell', 'dunkel'])) {
        setcookie('theme', $neuesTheme, time() + (365 * 24 * 60 * 60), '/');
        $theme = $neuesTheme;
    }
}

// Schriftgröße ändern
if (isset($_GET['schrift'])) {
    $neueGroesse = $_GET['schrift'];
    if (in_array($neueGroesse, ['klein', 'mittel', 'gross'])) {
        setcookie('schriftgroesse', $neueGroesse, time() + (365 * 24 * 60 * 60), '/');
        $schriftgroesse = $neueGroesse;
    }
}

// Übersetzungen
$texte = [
    'de' => [
        'titel' => 'Praktische Cookie-Anwendungen',
        'willkommen' => 'Willkommen auf unserer Seite!',
        'einstellungen' => 'Einstellungen',
        'sprache' => 'Sprache',
        'theme' => 'Design',
        'schrift' => 'Schriftgröße',
        'speichern' => 'Einstellungen werden in Cookies gespeichert'
    ],
    'en' => [
        'titel' => 'Practical Cookie Applications',
        'willkommen' => 'Welcome to our page!',
        'einstellungen' => 'Settings',
        'sprache' => 'Language',
        'theme' => 'Theme',
        'schrift' => 'Font Size',
        'speichern' => 'Settings are saved in cookies'
    ],
    'fr' => [
        'titel' => 'Applications pratiques de cookies',
        'willkommen' => 'Bienvenue sur notre page!',
        'einstellungen' => 'Paramètres',
        'sprache' => 'Langue',
        'theme' => 'Thème',
        'schrift' => 'Taille de police',
        'speichern' => 'Les paramètres sont enregistrés dans les cookies'
    ]
];

// Theme-Farben
$themes = [
    'hell' => ['bg' => '#ffffff', 'text' => '#000000', 'box' => '#f0f0f0'],
    'dunkel' => ['bg' => '#1a1a1a', 'text' => '#ffffff', 'box' => '#2a2a2a']
];

// Schriftgrößen
$schriftgroessen = [
    'klein' => '14px',
    'mittel' => '16px',
    'gross' => '20px'
];

// "Zuletzt angesehen" Funktion
$zuletzt = [];
if (isset($_COOKIE['zuletzt_angesehen'])) {
    $zuletzt = json_decode($_COOKIE['zuletzt_angesehen'], true) ?? [];
}

// Produkt als "angesehen" markieren (Demo)
if (isset($_GET['produkt'])) {
    $produkt_id = (int)$_GET['produkt'];
    // Produkt vorne hinzufügen
    array_unshift($zuletzt, $produkt_id);
    // Duplikate entfernen
    $zuletzt = array_unique($zuletzt);
    // Auf 5 Produkte limitieren
    $zuletzt = array_slice($zuletzt, 0, 5);
    // In Cookie speichern
    setcookie('zuletzt_angesehen', json_encode($zuletzt), time() + (30 * 24 * 60 * 60), '/');
}

$produkte = [
    1 => 'Laptop',
    2 => 'Maus',
    3 => 'Tastatur',
    4 => 'Monitor',
    5 => 'Headset'
];
?>
<!DOCTYPE html>
<html lang="<?php echo $sprache; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $texte[$sprache]['titel']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: <?php echo $themes[$theme]['bg']; ?>;
            color: <?php echo $themes[$theme]['text']; ?>;
            font-size: <?php echo $schriftgroessen[$schriftgroesse]; ?>;
            transition: all 0.3s;
        }
        .box {
            background: <?php echo $themes[$theme]['box']; ?>;
            padding: 20px;
            margin: 15px 0;
            border-radius: 5px;
        }
        button, a.button {
            padding: 10px 15px;
            margin: 5px;
            cursor: pointer;
            border: none;
            background: #007bff;
            color: white;
            text-decoration: none;
            display: inline-block;
            border-radius: 3px;
        }
        button:hover, a.button:hover {
            background: #0056b3;
        }
        .aktiv {
            background: #28a745 !important;
        }
        .produkt-link {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1><?php echo $texte[$sprache]['willkommen']; ?></h1>

    <div class="box">
        <h2><?php echo $texte[$sprache]['einstellungen']; ?></h2>

        <h3><?php echo $texte[$sprache]['sprache']; ?>:</h3>
        <a href="?sprache=de" class="button <?php echo $sprache === 'de' ? 'aktiv' : ''; ?>">Deutsch</a>
        <a href="?sprache=en" class="button <?php echo $sprache === 'en' ? 'aktiv' : ''; ?>">English</a>
        <a href="?sprache=fr" class="button <?php echo $sprache === 'fr' ? 'aktiv' : ''; ?>">Français</a>

        <h3><?php echo $texte[$sprache]['theme']; ?>:</h3>
        <a href="?theme=hell" class="button <?php echo $theme === 'hell' ? 'aktiv' : ''; ?>">☀️ Hell</a>
        <a href="?theme=dunkel" class="button <?php echo $theme === 'dunkel' ? 'aktiv' : ''; ?>">🌙 Dunkel</a>

        <h3><?php echo $texte[$sprache]['schrift']; ?>:</h3>
        <a href="?schrift=klein" class="button <?php echo $schriftgroesse === 'klein' ? 'aktiv' : ''; ?>">Klein</a>
        <a href="?schrift=mittel" class="button <?php echo $schriftgroesse === 'mittel' ? 'aktiv' : ''; ?>">Mittel</a>
        <a href="?schrift=gross" class="button <?php echo $schriftgroesse === 'gross' ? 'aktiv' : ''; ?>">Groß</a>

        <p style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
            ℹ️ <?php echo $texte[$sprache]['speichern']; ?>
        </p>
    </div>

    <div class="box">
        <h2>Demo-Produkte (für "Zuletzt angesehen")</h2>
        <p>Klicke auf Produkte um sie als angesehen zu markieren:</p>
        <?php foreach ($produkte as $id => $name): ?>
            <a href="?produkt=<?php echo $id; ?>" class="produkt-link"><?php echo $name; ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($zuletzt)): ?>
        <div class="box">
            <h2>📋 Zuletzt angesehen:</h2>
            <ul>
                <?php foreach ($zuletzt as $id): ?>
                    <?php if (isset($produkte[$id])): ?>
                        <li><?php echo $produkte[$id]; ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <p style="font-size: 12px; opacity: 0.8;">
                (Gespeichert als JSON in Cookie)
            </p>
        </div>
    <?php endif; ?>

    <div class="box">
        <h2>Aktuelle Cookie-Werte:</h2>
        <ul>
            <li><strong>Sprache:</strong> <?php echo $sprache; ?></li>
            <li><strong>Theme:</strong> <?php echo $theme; ?></li>
            <li><strong>Schriftgröße:</strong> <?php echo $schriftgroesse; ?></li>
            <li><strong>Zuletzt angesehen:</strong> <?php echo !empty($zuletzt) ? implode(', ', $zuletzt) : 'keine'; ?></li>
        </ul>
    </div>

    <hr style="border-color: <?php echo $themes[$theme]['text']; ?>; opacity: 0.3;">
    <p><a href="05_cookie_grundlagen.php" style="color: <?php echo $themes[$theme]['text']; ?>;">← Zurück zur Übersicht</a></p>

</body>
</html>

<?php
/*
 * PRAKTISCHE COOKIE-ANWENDUNGEN:
 *
 * 1. SPRACHAUSWAHL:
 *    - Sprache in Cookie speichern
 *    - Bei jedem Aufruf Cookie lesen
 *    - Texte entsprechend anzeigen
 *
 * 2. THEME (Hell/Dunkel):
 *    - Theme-Wahl in Cookie
 *    - CSS dynamisch anpassen
 *    - Bleibt über Seiten hinweg erhalten
 *
 * 3. SCHRIFTGRÖSSE:
 *    - Barrierefreiheit
 *    - Benutzer kann Größe wählen
 *    - In Cookie speichern
 *
 * 4. "ZULETZT ANGESEHEN":
 *    - Produkt-IDs in Array
 *    - Als JSON in Cookie
 *    - Bei Besuch hinzufügen
 *    - json_encode() / json_decode()
 *
 * 5. ANDERE VERWENDUNGEN:
 *    - Layout-Präferenzen (Grid/Liste)
 *    - Sortierung (Preis/Name/Datum)
 *    - Seitenanzahl (Pagination)
 *    - Cookie-Banner-Akzeptierung
 *    - Regionale Einstellungen (Währung)
 *
 * JSON IN COOKIES:
 *
 * // Speichern
 * $daten = ['a' => 1, 'b' => 2];
 * setcookie('name', json_encode($daten), time() + 3600, '/');
 *
 * // Lesen
 * $daten = json_decode($_COOKIE['name'], true);
 *
 * WICHTIG:
 * - Cookie-Größe beachten (max ~4KB)
 * - Keine sensiblen Daten
 * - Immer Fallback-Werte haben
 */
?>
