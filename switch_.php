<?php
//switch ersetzt if wenn viele ifelse statments verwendet werden
// es ist mehr effizient und braucht weniger code

$grade = "F";

switch($grade){
    case "A": // was wird geprüft wie if
        echo "Sehr gut"; //Funktion die ausgeführt wird wenn wert passt
        break; // um den case zu schließen
    case "B":
        echo "gut";
        break;
    case "C":
        echo "oarsch";
        break;
    case "D":
        echo "richtig oarsch";
        break;
    case "F":
        echo "schleicht die fun da schui";
        break;
}
?>