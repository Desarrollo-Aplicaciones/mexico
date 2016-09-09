<?php
            // Including all required classes
            // include(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/../classes/barcode/class/BCGFontFile.php');
require_once(dirname(__FILE__).'/../classes/barcode/class/BCGColor.php');
require_once(dirname(__FILE__).'/../classes/barcode/class/BCGDrawing.php');

// Including the barcode technology
require_once(dirname(__FILE__).'/../classes/barcode/class/BCGcode128.barcode.php');

// Loading Font
$font = new BCGFontFile(dirname(__FILE__).'/../classes/barcode/font/Arial.ttf', 9);

// Don't forget to sanitize user inputs
$text = isset($_GET['barcode']) ? $_GET['barcode'] : '0000000';

// The arguments are R, G, B for color.
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
    $code = new BCGcode128();
    $code->setScale(1); // Resolution
    $code->setThickness(35); // Thickness
    $code->setForegroundColor($color_black); // Color of bars
    $code->setBackgroundColor($color_white); // Color of spaces
    $code->setFont($font); // Font (or 0)
    $code->parse($text); // Text
} catch(Exception $exception) {
    $drawException = $exception;
}

/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
    $drawing->drawException($drawException);
} else {
    $drawing->setBarcode($code);
    $drawing->draw();
}

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');
header('Content-Disposition: inline; filename="barcode.png"');

// Draw (or save) the image into PNG format.
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>