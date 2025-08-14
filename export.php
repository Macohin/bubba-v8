<?php
// Require Composer's autoloader.
// This is essential for PHPWord and Dompdf to work.
// The user will ensure this is available on the server environment.
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use Dompdf\Dompdf;
use Dompdf\Options;

// --- Main Export Logic ---

// 1. Get data from the POST request
$content = $_POST['content'] ?? '';
$format = $_POST['format'] ?? '';
$cpf = $_POST['cpf'] ?? 'documento'; // Use a default name if CPF is not provided
$date = date('Y-m-d');

// Sanitize CPF for use in the filename to prevent any path traversal or other attacks
$cpf_sanitized = preg_replace('/[^0-9]/', '', $cpf);
$filename_base = "parecer_{$cpf_sanitized}_{$date}";

// 2. Validate that we have the required data
if (empty($content) || empty($format)) {
    http_response_code(400);
    // Provide a clear error message for debugging
    die('Error: Missing required "content" or "format" in POST request.');
}

// 3. Generate the file based on the requested format
switch ($format) {
    case 'docx':
        try {
            // Set headers for DOCX file download
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header("Content-Disposition: attachment; filename=\"{$filename_base}.docx\"");

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Use PHPWord's HTML helper to add the HTML content to the document
            // The `false, false` arguments disable adding a new page and using tables as rows
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $content, false, false);

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            // Save the document directly to the PHP output stream
            $objWriter->save('php://output');

        } catch (Exception $e) {
            // If something goes wrong, send a server error and a descriptive message
            http_response_code(500);
            die('Error creating DOCX file: ' . $e->getMessage());
        }
        break;

    case 'pdf':
        try {
            // Set headers for PDF file download
            header('Content-Type: application/pdf');
            header("Content-Disposition: attachment; filename=\"{$filename_base}.pdf\"");

            $options = new Options();
            // Enable remote content (like images) and the HTML5 parser for better compatibility
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($content);

            // Set paper size to A4, standard for documents
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Stream the generated PDF to the browser
            echo $dompdf->output();

        } catch (Exception $e) {
            // If something goes wrong, send a server error and a descriptive message
            http_response_code(500);
            die('Error creating PDF file: ' . $e->getMessage());
        }
        break;

    default:
        // If the format is not 'docx' or 'pdf', it's an invalid request
        http_response_code(400);
        die('Error: Invalid format specified. Must be "docx" or "pdf".');
        break;
}

// Ensure no other output is sent
exit;
?>
