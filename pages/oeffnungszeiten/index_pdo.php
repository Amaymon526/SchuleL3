<?php
/**
 * Öffnungszeiten-Verwaltung mit PDO
 */

// Datenbankverbindung
$host = 'localhost';
$dbname = 'schule_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prüfen ob Tabelle existiert, sonst erstellen
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'oeffnungszeiten'");

    if ($tableCheck->rowCount() == 0) {
        // Tabelle erstellen
        $createTable = "CREATE TABLE oeffnungszeiten (
            id INT AUTO_INCREMENT PRIMARY KEY,
            wochentag VARCHAR(20) NOT NULL UNIQUE,
            ist_offen BOOLEAN DEFAULT 1,
            oeffnet_um TIME,
            schliesst_um TIME
        )";
        $pdo->exec($createTable);

        // Standard-Öffnungszeiten einfügen
        $stmt = $pdo->prepare("INSERT INTO oeffnungszeiten (wochentag, ist_offen, oeffnet_um, schliesst_um) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Montag', 1, '09:00', '18:00']);
        $stmt->execute(['Dienstag', 1, '09:00', '18:00']);
        $stmt->execute(['Mittwoch', 1, '09:00', '18:00']);
        $stmt->execute(['Donnerstag', 1, '09:00', '18:00']);
        $stmt->execute(['Freitag', 1, '09:00', '18:00']);
        $stmt->execute(['Samstag', 1, '10:00', '14:00']);
        $stmt->execute(['Sonntag', 0, null, null]);
    }
} catch(PDOException $e) {
    die("Verbindung fehlgeschlagen: " . $e->getMessage());
}

$message = '';

// Öffnungszeiten speichern
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    foreach ($_POST['tag'] as $wochentag => $daten) {
        $ist_offen = isset($daten['ist_offen']) ? 1 : 0;
        $oeffnet = $ist_offen ? $daten['oeffnet'] : null;
        $schliesst = $ist_offen ? $daten['schliesst'] : null;

        $stmt = $pdo->prepare("UPDATE oeffnungszeiten SET ist_offen = ?, oeffnet_um = ?, schliesst_um = ? WHERE wochentag = ?");
        $stmt->execute([$ist_offen, $oeffnet, $schliesst, $wochentag]);
    }
    $message = 'Öffnungszeiten gespeichert!';
}

// Alle Öffnungszeiten holen
$stmt = $pdo->query("SELECT * FROM oeffnungszeiten ORDER BY FIELD(wochentag, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag')");
$zeiten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status-Funktion: Ist aktuell geöffnet?
function ist_jetzt_offen($zeiten) {
    $wochentage = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
    $heute = $wochentage[date('w')];
    $jetzt = date('H:i');

    foreach ($zeiten as $zeit) {
        if ($zeit['wochentag'] == $heute) {
            if (!$zeit['ist_offen']) {
                return ['status' => false, 'text' => 'Heute geschlossen'];
            }

            if ($jetzt >= $zeit['oeffnet_um'] && $jetzt <= $zeit['schliesst_um']) {
                return ['status' => true, 'text' => 'Geöffnet bis ' . substr($zeit['schliesst_um'], 0, 5) . ' Uhr'];
            } else {
                return ['status' => false, 'text' => 'Geschlossen (öffnet um ' . substr($zeit['oeffnet_um'], 0, 5) . ' Uhr)'];
            }
        }
    }
    return ['status' => false, 'text' => 'Status unbekannt'];
}

$status = ist_jetzt_offen($zeiten);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öffnungszeiten - PDO</title>
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
            <p>Verwalten Sie Ihre Öffnungszeiten (mit Datenbank)</p>
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
                <?php foreach ($zeiten as $zeit): ?>
                    <div class="tag-row <?php echo !$zeit['ist_offen'] ? 'geschlossen' : ''; ?>">
                        <div class="tag-header">
                            <span class="tag-name"><?php echo $zeit['wochentag']; ?></span>
                            <div class="checkbox-wrapper">
                                <input type="checkbox"
                                       name="tag[<?php echo $zeit['wochentag']; ?>][ist_offen]"
                                       id="offen_<?php echo $zeit['wochentag']; ?>"
                                       <?php echo $zeit['ist_offen'] ? 'checked' : ''; ?>
                                       onchange="toggleZeiten('<?php echo $zeit['wochentag']; ?>')">
                                <label for="offen_<?php echo $zeit['wochentag']; ?>">Geöffnet</label>
                            </div>
                        </div>
                        <div class="zeit-inputs">
                            <div class="zeit-gruppe">
                                <label>Von:</label>
                                <input type="time"
                                       name="tag[<?php echo $zeit['wochentag']; ?>][oeffnet]"
                                       id="von_<?php echo $zeit['wochentag']; ?>"
                                       value="<?php echo $zeit['oeffnet_um']; ?>"
                                       <?php echo !$zeit['ist_offen'] ? 'disabled' : ''; ?>>
                            </div>
                            <span>—</span>
                            <div class="zeit-gruppe">
                                <label>Bis:</label>
                                <input type="time"
                                       name="tag[<?php echo $zeit['wochentag']; ?>][schliesst]"
                                       id="bis_<?php echo $zeit['wochentag']; ?>"
                                       value="<?php echo $zeit['schliesst_um']; ?>"
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
                Die Zeiten werden automatisch in der Datenbank gespeichert.
            </div>
        </div>

        <div class="footer">
            <a href="index_txt.php" style="color: #667eea; text-decoration: none;">
                → Zur TXT-Version wechseln
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
