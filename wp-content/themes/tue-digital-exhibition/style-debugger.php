<?php
// Imposta l'header per dire al browser che questo è un file CSS
header('Content-type: text/css');

// Percorso del file CSS originale
$css_file_path = __DIR__ . '/style.css';

if (!file_exists($css_file_path)) {
    // Se il file non esiste, non fare nulla
    exit;
}

// Legge tutte le righe del file in un array
$css_lines = file($css_file_path, FILE_IGNORE_NEW_LINES);

// Prende i parametri 'start' e 'end' dall'URL. Se non ci sono, li imposta a 0.
$start_line_to_comment = isset($_GET['start']) ? intval($_GET['start']) - 1 : 0;
$end_line_to_comment = isset($_GET['end']) ? intval($_GET['end']) - 1 : 0;

// Se i parametri sono validi, commenta il blocco di righe specificato
if ($start_line_to_comment > 0 || $end_line_to_comment > 0) {
    echo "/* DEBUG: Commenting out lines from " . ($start_line_to_comment + 1) . " to " . ($end_line_to_comment + 1) . " */\n";

    // Stampa le righe prima del blocco
    for ($i = 0; $i < $start_line_to_comment; $i++) {
        if (isset($css_lines[$i])) {
            echo $css_lines[$i] . "\n";
        }
    }

    // Aggiungi l'apertura del commento
    echo "/* START DEBUG COMMENT\n";

    // Stampa le righe commentate (per riferimento)
    for ($i = $start_line_to_comment; $i <= $end_line_to_comment; $i++) {
        if (isset($css_lines[$i])) {
            echo $css_lines[$i] . "\n";
        }
    }

    // Aggiungi la chiusura del commento
    echo "END DEBUG COMMENT */\n";

    // Stampa le righe dopo il blocco
    for ($i = $end_line_to_comment + 1; $i < count($css_lines); $i++) {
        if (isset($css_lines[$i])) {
            echo $css_lines[$i] . "\n";
        }
    }

} else {
    // Se non ci sono parametri, stampa l'intero file CSS
    readfile($css_file_path);
}
