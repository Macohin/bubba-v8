<?php
// Require Composer's autoloader using a robust, absolute path.
// This is essential for PHPWord and Dompdf to work.
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use Dompdf\Dompdf;
use Dompdf\Options;

// --- Main Export Logic ---

// 1. Get data from POST request
$post_content = $_POST['content'] ?? '';
$format = $_POST['format'] ?? '';
$cpf = $_POST['cpf'] ?? 'documento';
$date = date('Y-m-d');

// 2. Sanitize CPF for filename
$cpf_sanitized = preg_replace('/[^0-9]/', '', $cpf);
$filename_base = "parecer_{$cpf_sanitized}_{$date}";

// 3. Validate input
if (empty($post_content) || empty($format)) {
    http_response_code(400);
    die('Error: Missing content or format.');
}

// 4. Prepare self-contained HTML for conversion
$css_path = __DIR__ . '/css/abnt-style.css';
$css_content = '';
if (file_exists($css_path)) {
    $css_content = file_get_contents($css_path);
} else {
    // Optional: handle missing CSS file, maybe log an error
    // For now, it will proceed without the styles if the file is missing
}

$full_html = <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Parecer Previdenciário</title>
    <style>
        {$css_content}
    </style>
</head>
<body>
    {$post_content}
</body>
</html>
HTML;


// 5. Generate file based on format
switch ($format) {
    case 'docx':
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header("Content-Disposition: attachment; filename=\"{$filename_base}.docx\"");

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $full_html, false, false);

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');
        } catch (Exception $e) {
            http_response_code(500);
            error_log('Error creating DOCX file: ' . $e->getMessage());
            die('Error creating DOCX file. Please check server logs.');
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
            $dompdf->loadHtml($full_html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            echo $dompdf->output();
        } catch (Exception $e) {
            http_response_code(500);
            error_log('Error creating PDF file: ' . $e->getMessage());
            die('Error creating PDF file. Please check server logs.');
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
