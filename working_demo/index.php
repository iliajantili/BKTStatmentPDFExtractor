
<?php

if (!empty($_FILES["pdf_file"]["name"])) {
    $fileName = basename($_FILES["pdf_file"]["name"]); 
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
 
    $allowTypes = array('pdf'); 
    if (in_array($fileType, $allowTypes)) { 

        include 'vendor/autoload.php'; 
            
        $parser = new \Smalot\PdfParser\Parser();
             
        $file = $_FILES["pdf_file"]["tmp_name"]; 
            
        $pdf = $parser->parseFile($file);

        $text = $pdf->getText();
        
        if(array_key_exists('extractPDF', $_POST)) {
            pdf(); 
        }

        if(array_key_exists('values', $_POST)) {
            nxirVlerat(); 
        }
    }
}

function nxirVlerat() {
    $linesArray = preg_split('/\n+/', $GLOBALS['text']);

    $date = '';
    $foundDate = false;
    for ($x = 0; $x < count($linesArray); $x++) {
        $line = $linesArray[$x];

        $partsArray = [];
        
        if (strpos($line, 'BOOKING DATE') !== false) {
            $foundDate = true;
            $partsArray = preg_split('/\s+/', trim($line));
            $date = end($partsArray);
        }

        if ($foundDate == true && strpos(trim($line), $date) === 0) {
            $originalArray = preg_split('/\s+/', trim($line));
            $firstLineArray = [];

            array_push($firstLineArray, $originalArray[0]);
            array_push($firstLineArray, $originalArray[1]);
            if (count($originalArray) > 6) {
                array_push($firstLineArray, $originalArray[2]);
                array_push($firstLineArray, $originalArray[3]);
            }
            $sumArray = array_slice($originalArray, -2, 1);
            $sum = $sumArray[0];
            array_push($firstLineArray, $sum);
            
            $nextLine = $linesArray[$x+1];
            $nextLineArray = preg_split('/\s+/', trim($nextLine));

            $lastSeven = array_slice($nextLineArray, -7);
            
            $firstParts = array_diff($nextLineArray, $lastSeven);
            $firma = array_slice($firstParts, 1);
            if (count($firma) == 0) {
                $firma = array_slice($nextLineArray, 2);
            }

            $bashkimiIReshtave = array_merge($firstLineArray,$firma);
            foreach($bashkimiIReshtave as $element) {
                if(strtotime($element)){
                    echo '<span style="margin-right: 10px"><strong>';
                    print_r($element);
                    echo "</strong></span>";
                } else {
                    echo '<span style="margin-right: 10px">';
                    print_r($element);
                    echo "</span>";
                }
            }
            echo "<br>";
        }
    }
}

function pdf() {

    $pdfText = nl2br($GLOBALS['text']);
    echo $pdfText;
}
?>

<html>
    <head>
        <title>Read pdf</title>
    </head> 

    <style>
        h1 {text-align: center;}
        h2 {text-align: center;}
        div {text-align: center;}

        table, th, td {
        border:2px solid black;
        }
    </style>

    <body>
        <form method="post" enctype="multipart/form-data">
            <div class="form-input">

            <h1>Extract PDF  to text</h1>

            <table style="width: 100%">
            
        <tr>
            <th><label for="pdf_file">Select PDF File</label>

            <div>
            <input type="file" name="pdf_file" placeholder="Select a PDF file" required="">
            </div>

            <h2>
     
            </h2>

            <div>
            <input type="submit" name="extractPDF" class="btn" value="Extract PDF">
            <input type="submit" name="values" value="Values">
            </div>

            </th>
        </tr>
        </table>
                 
            </div>

        </form>
    </body>
</html>