<?php
$age = 17;

// Niemals ein = verwenden den das bedeutet du weist einen wert zu immer == wird verwendet um zu vergleichen

if ($age >= 18) {
    echo "Du kannst die seite betreten";
} elseif ($age >= 16) {
    echo "Du darfst nur mit eltern hier rein";
} else {
    echo "Du bist nicht alt genug";
}