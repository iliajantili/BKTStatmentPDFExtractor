<?php

include 'vendor/autoload.php';

$parser = new \Smalot\PdfParser\Parser();

$file = 'documents/Statement.pdf';

$pdf = $parser->parseFile($file);

$text = $pdf->getText();

$pdfText = nl2br($text);

echo $pdfText;