<?php
// Require Composer's autoloader.
// The user will ensure this is available on the server.
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use Dompdf\Dompdf;
use Dompdf\Options;

// --- Main Export Logic ---

// 1. Get data from POST request
$content = $_POST['content'] ?? '';
$format = $_POST['format'] ?? '';
$cpf = $_POST['cpf'] ?? 'document';
$date = date('Y-m-d');

// Sanitize CPF for filename
$cpf_sanitized = preg_replace('/\D/', '', $cpf);
$filename_base = "parecer_{$cpf_sanitized}_{$date}";

// 2. Check for required data
if (empty($content) || empty($format)) {
    http_response_code(400);
    die('Error: Missing content or format.');
}

// 3. Generate file based on format
switch ($format) {
    case 'docx':
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header("Content-Disposition: attachment; filename=\"{$filename_base}.docx\"");

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $content, false, false);

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');

        } catch (Exception $e) {
            http_response_code(500);
            die('Error creating DOCX file: ' . $e->getMessage());
        }
        break;

    case 'pdf':
        try {
            header('Content-Type: application/pdf');
            header("Content-Disposition: attachment; filename=\"{$filename_base}.pdf\"");

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($content);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Stream the file to the browser
            echo $dompdf->output();

        } catch (Exception $e) {
            http_response_code(500);
            die('Error creating PDF file: ' . $e->getMessage());
        }
        break;

    default:
        http_response_code(400);
        die('Error: Invalid format specified.');
        break;
}

exit;
?>
