<?php
require_once 'dompdf/autoload.inc.php';

// reference the Dompdf namespace
// use Dompdf\Dompdf;


generate_pdf($html) {

    $dompdf = new Dompdf\Dompdf();

    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    // $dompdf->stream();

    $output = $dompdf->output();

    $filename = get_random_string(10);
    $structure = WP_CONTENT_DIR . '/was-pdf/';

    if (!mkdir($structure, 0777, true)) {
        die('Failed to create folders...');
    }

    file_put_contents( $structure.$filename .'.pdf', $output);
}

function get_random_string($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
  
    return $randomString;
}