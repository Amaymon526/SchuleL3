<?php
/**
 * Beispiel 4: Warenkorb mit Sessions
 *
 * Zeigt wie man einen Warenkorb mit Sessions implementiert
 */

session_start();

// Produkt-Datenbank (in Realität aus MySQL)
$produkte = [
    1 => ['name' => 'Laptop', 'preis' => 799.99],
    2 => ['name' => 'Maus', 'preis' => 19.99],
    3 => ['name' => 'Tastatur', 'preis' => 49.99],
    4 => ['name' => 'Monitor', 'preis' => 299.99],
    5 => ['name' => 'Headset', 'preis' => 79.99]
];

// Warenkorb initialisieren falls nicht vorhanden
if (!isset($_SESSION['warenkorb'])) {
    $_SESSION['warenkorb'] = [];
}

$message = '';

// Produkt hinzufügen
if (isset($_GET['add'])) {
    $produkt_id = (int)$_GET['add'];

    if (isset($produkte[$produkt_id])) {
        // Prüfen ob Produkt schon im Warenkorb
        if (isset($_SESSION['warenkorb'][$produkt_id])) {
            // Menge erhöhen
            $_SESSION['warenkorb'][$produkt_id]['menge']++;
        } else {
            // Neu hinzufügen
            $_SESSION['warenkorb'][$produkt_id] = [
                'name' => $produkte[$produkt_id]['name'],
                'preis' => $produkte[$produkt_id]['preis'],
                'menge' => 1
            ];
        }
        $message = $produkte[$produkt_id]['name'] . ' wurde hinzugefügt!';
    }
}

// Produkt entfernen
if (isset($_GET['remove'])) {
    $produkt_id = (int)$_GET['remove'];
    if (isset($_SESSION['warenkorb'][$produkt_id])) {
        $name = $_SESSION['warenkorb'][$produkt_id]['name'];
        unset($_SESSION['warenkorb'][$produkt_id]);
        $message = $name . ' wurde entfernt!';
    }
}

// Menge ändern
if (isset($_POST['update'])) {
    foreach ($_POST['menge'] as $id => $menge) {
        $id = (int)$id;
        $menge = (int)$menge;

        if ($menge > 0 && isset($_SESSION['warenkorb'][$id])) {
            $_SESSION['warenkorb'][$id]['menge'] = $menge;
        } elseif ($menge <= 0) {
            unset($_SESSION['warenkorb'][$id]);
        }
    }
    $message = 'Warenkorb aktualisiert!';
}

// Warenkorb leeren
if (isset($_GET['clear'])) {
    $_SESSION['warenkorb'] = [];
    $message = 'Warenkorb geleert!';
}

// Gesamtsumme berechnen
function berechne_gesamtsumme() {
    $summe = 0;
    foreach ($_SESSION['warenkorb'] as $item) {
        $summe += $item['preis'] * $item['menge'];
    }
    return $summe;
}

// Anzahl Artikel im Warenkorb
function anzahl_artikel() {
    $anzahl = 0;
    foreach ($_SESSION['warenkorb'] as $item) {
        $anzahl += $item['menge'];
    }
    return $anzahl;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Warenkorb mit Sessions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message { background: #d4edda; padding: 10px; margin: 10px 0; }
        .produkte { display: flex; flex-wrap: wrap; gap: 10px; }
        .produkt { border: 1px solid #ccc; padding: 15px; width: 200px; }
        .produkt button { background: #28a745; color: white; border: none; padding: 10px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f2f2f2; }
        .gesamt { font-size: 20px; font-weight: bold; color: green; }
        input[type="number"] { width: 60px; }
    </style>
</head>
<body>
    <h2>🛒 Warenkorb-System mit Sessions</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <p>Artikel im Warenkorb: <strong><?php echo anzahl_artikel(); ?></strong></p>

    <h3>Verfügbare Produkte</h3>
    <div class="produkte">
        <?php foreach ($produkte as $id => $produkt): ?>
            <div class="produkt">
                <h4><?php echo $produkt['name']; ?></h4>
                <p>Preis: <?php echo number_format($produkt['preis'], 2); ?> €</p>
                <a href="?add=<?php echo $id; ?>">
                    <button>In den Warenkorb</button>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>

    <h3>Dein Warenkorb</h3>

    <?php if (empty($_SESSION['warenkorb'])): ?>
        <p><em>Dein Warenkorb ist leer.</em></p>
    <?php else: ?>
        <form method="POST" action="">
            <table>
                <tr>
                    <th>Produkt</th>
                    <th>Preis</th>
                    <th>Menge</th>
                    <th>Summe</th>
                    <th>Aktion</th>
                </tr>
                <?php foreach ($_SESSION['warenkorb'] as $id => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['preis'], 2); ?> €</td>
                        <td>
                            <input type="number" name="menge[<?php echo $id; ?>]"
                                   value="<?php echo $item['menge']; ?>" min="0">
                        </td>
                        <td><?php echo number_format($item['preis'] * $item['menge'], 2); ?> €</td>
                        <td>
                            <a href="?remove=<?php echo $id; ?>">Entfernen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Gesamtsumme:</strong></td>
                    <td colspan="2" class="gesamt">
                        <?php echo number_format(berechne_gesamtsumme(), 2); ?> €
                    </td>
                </tr>
            </table>

            <button type="submit" name="update">Warenkorb aktualisieren</button>
            <a href="?clear=1"><button type="button">Warenkorb leeren</button></a>
        </form>

        <hr>
        <h3>Nächster Schritt</h3>
        <p><a href="05_session_checkout.php"><button style="padding: 15px; font-size: 16px;">Zur Kasse gehen</button></a></p>
    <?php endif; ?>

    <hr>
    <p><a href="01_session_grundlagen.php">← Zurück zur Übersicht</a></p>

</body>
</html>

<?php
/*
 * WARENKORB-STRUKTUR:
 *
 * $_SESSION['warenkorb'] = [
 *     1 => [
 *         'name' => 'Laptop',
 *         'preis' => 799.99,
 *         'menge' => 2
 *     ],
 *     3 => [
 *         'name' => 'Tastatur',
 *         'preis' => 49.99,
 *         'menge' => 1
 *     ]
 * ];
 *
 * WICHTIGE FUNKTIONEN:
 *
 * - Produkt hinzufügen: $_SESSION['warenkorb'][$id] = [...]
 * - Menge erhöhen: $_SESSION['warenkorb'][$id]['menge']++
 * - Produkt entfernen: unset($_SESSION['warenkorb'][$id])
 * - Warenkorb leeren: $_SESSION['warenkorb'] = []
 *
 * VERBESSERUNGEN für echten Shop:
 * - Lagerbestand prüfen
 * - Preise aus Datenbank holen (nicht aus Session!)
 * - Warenkorb auch in DB speichern für eingeloggte Benutzer
 * - Verfallszeit für Warenkorb-Artikel
 * - Session-ID regenerieren für Sicherheit
 */
?>
