<?php
/**
 * Öffnungszeiten-Verwaltung mit TXT-Datei
 */

$datei = __DIR__ . '/oeffnungszeiten.txt';

// Standard-Öffnungszeiten wenn Datei nicht existiert
$standardZeiten = [
    'Montag' => ['ist_offen' => true, 'oeffnet' => '09:00', 'schliesst' => '18:00'],
    'Dienstag' => ['ist_offen' => true, 'oeffnet' => '09:00', 'schliesst' => '18:00'],
    'Mittwoch' => ['ist_offen' => true, 'oeffnet' => '09:00', 'schliesst' => '18:00'],
    'Donnerstag' => ['ist_offen' => true, 'oeffnet' => '09:00', 'schliesst' => '18:00'],
    'Freitag' => ['ist_offen' => true, 'oeffnet' => '09:00', 'schliesst' => '18:00'],
    'Samstag' => ['ist_offen' => true, 'oeffnet' => '10:00', 'schliesst' => '14:00'],
    'Sonntag' => ['ist_offen' => false, 'oeffnet' => '', 'schliesst' => '']
];

$message = '';

// Öffnungszeiten aus Datei laden
function lade_zeiten($datei, $standard) {
    if (!file_exists($datei)) {
        speichere_zeiten($datei, $standard);
        return $standard;
    }

    $zeiten = [];
    $lines = file($datei, FILE_IGNORE_NEW_LINES);

    foreach ($lines as $line) {
        // Format: Wochentag|ist_offen|oeffnet|schliesst
        $parts = explode('|', $line);
        if (count($parts) == 4) {
            $zeiten[$parts[0]] = [
                'ist_offen' => $parts[1] === '1',
                'oeffnet' => $parts[2],
                'schliesst' => $parts[3]
            ];
        }
    }

    return $zeiten;
}

// Öffnungszeiten in Datei speichern
function speichere_zeiten($datei, $zeiten) {
    $content = '';
    foreach ($zeiten as $tag => $daten) {
        $ist_offen = $daten['ist_offen'] ? '1' : '0';
        $oeffnet = $daten['oeffnet'] ?? '';
        $schliesst = $daten['schliesst'] ?? '';
        $content .= "$tag|$ist_offen|$oeffnet|$schliesst\n";
    }
    file_put_contents($datei, $content);
}

// Öffnungszeiten laden
$zeiten = lade_zeiten($datei, $standardZeiten);

// Öffnungszeiten speichern
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $neueZeiten = [];

    foreach (['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'] as $tag) {
        $ist_offen = isset($_POST['tag'][$tag]['ist_offen']);
        $neueZeiten[$tag] = [
            'ist_offen' => $ist_offen,
            'oeffnet' => $ist_offen ? ($_POST['tag'][$tag]['oeffnet'] ?? '') : '',
            'schliesst' => $ist_offen ? ($_POST['tag'][$tag]['schliesst'] ?? '') : ''
        ];
    }

    speichere_zeiten($datei, $neueZeiten);
    $zeiten = $neueZeiten;
    $message = 'Öffnungszeiten gespeichert!';
}

// Status-Funktion: Ist aktuell geöffnet?
function ist_jetzt_offen($zeiten) {
    $wochentage = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
    $heute = $wochentage[date('w')];
    $jetzt = date('H:i');

    if (!isset($zeiten[$heute])) {
        return ['status' => false, 'text' => 'Status unbekannt'];
    }

    $zeit = $zeiten[$heute];

    if (!$zeit['ist_offen']) {
        return ['status' => false, 'text' => 'Heute geschlossen'];
    }

    if ($jetzt >= $zeit['oeffnet'] && $jetzt <= $zeit['schliesst']) {
        return ['status' => true, 'text' => 'Geöffnet bis ' . $zeit['schliesst'] . ' Uhr'];
    } else {
        return ['status' => false, 'text' => 'Geschlossen (öffnet um ' . $zeit['oeffnet'] . ' Uhr)'];
    }
}

$status = ist_jetzt_offen($zeiten);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öffnungszeiten - TXT</title>
    <style>
        .container { max-width: 800px; margin: 0 auto; }
        .header { padding: 20px; }
        .status { padding: 15px; }
        .content { padding: 20px; }
        .message { padding: 10px; margin-bottom: 15px; }
        .tag-row { padding: 15px; margin-bottom: 10px; }
        .tag-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .checkbox-wrapper { display: flex; gap: 5px; }
        .zeit-inputs { display: flex; gap: 10px; }
        .zeit-gruppe { display: flex; gap: 5px; }
        .button-group { margin-top: 20px; }
        .footer { padding: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🕐 Öffnungszeiten</h1>
            <p>Verwalten Sie Ihre Öffnungszeiten (mit Textdatei)</p>
        </div>

        <div class="status <?php echo $status['status'] ? 'offen' : 'geschlossen'; ?>">
            <?php echo $status['status'] ? '✅ ' : '🔒 '; ?>
            <?php echo $status['text']; ?>
            <br>
            <small style="font-size: 0.7em; opacity: 0.8;">
                Heute ist <?php echo date('l, d.m.Y H:i'); ?> Uhr
            </small>
        </div>

        <div class="content">
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <?php foreach ($zeiten as $tag => $zeit): ?>
                    <div class="tag-row <?php echo !$zeit['ist_offen'] ? 'geschlossen' : ''; ?>">
                        <div class="tag-header">
                            <span class="tag-name"><?php echo $tag; ?></span>
                            <div class="checkbox-wrapper">
                                <input type="checkbox"
                                       name="tag[<?php echo $tag; ?>][ist_offen]"
                                       id="offen_<?php echo $tag; ?>"
                                       <?php echo $zeit['ist_offen'] ? 'checked' : ''; ?>
                                       onchange="toggleZeiten('<?php echo $tag; ?>')">
                                <label for="offen_<?php echo $tag; ?>">Geöffnet</label>
                            </div>
                        </div>
                        <div class="zeit-inputs">
                            <div class="zeit-gruppe">
                                <label>Von:</label>
                                <input type="time"
                                       name="tag[<?php echo $tag; ?>][oeffnet]"
                                       id="von_<?php echo $tag; ?>"
                                       value="<?php echo $zeit['oeffnet']; ?>"
                                       <?php echo !$zeit['ist_offen'] ? 'disabled' : ''; ?>>
                            </div>
                            <span>—</span>
                            <div class="zeit-gruppe">
                                <label>Bis:</label>
                                <input type="time"
                                       name="tag[<?php echo $tag; ?>][schliesst]"
                                       id="bis_<?php echo $tag; ?>"
                                       value="<?php echo $zeit['schliesst']; ?>"
                                       <?php echo !$zeit['ist_offen'] ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="button-group">
                    <button type="submit" name="save">💾 Öffnungszeiten speichern</button>
                </div>
            </form>

            <div class="info">
                <strong>ℹ️ Hinweis:</strong>
                Deaktiviere die Checkbox für Tage an denen geschlossen ist.
                Die Zeiten werden in einer Textdatei (oeffnungszeiten.txt) gespeichert.
            </div>
        </div>

        <div class="footer">
            <a href="index_pdo.php" style="color: #f093fb; text-decoration: none;">
                → Zur Datenbank-Version wechseln
            </a>
        </div>
    </div>

    <script>
        function toggleZeiten(tag) {
            const checkbox = document.getElementById('offen_' + tag);
            const von = document.getElementById('von_' + tag);
            const bis = document.getElementById('bis_' + tag);
            const row = checkbox.closest('.tag-row');

            if (checkbox.checked) {
                von.disabled = false;
                bis.disabled = false;
                row.classList.remove('geschlossen');
            } else {
                von.disabled = true;
                bis.disabled = true;
                row.classList.add('geschlossen');
            }
        }
    </script>
</body>
</html>
