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

// 4. Prepare base HTML and CSS
$css_path = __DIR__ . '/css/abnt-style.css';
$css_content = file_exists($css_path) ? file_get_contents($css_path) : '';

// 5. Conditionally simplify HTML for DOCX export
$content_for_export = $post_content;
if ($format === 'docx') {
    // PHPWord's HTML parser is very limited. We need to simplify the HTML.
    // 1. Remove the complex image header. It's a common point of failure.
    $content_for_export = preg_replace('/<div style="text-align: center;.*?<img.*?<\/div><hr>/si', '<h1>PARECER PREVIDENCIÁRIO ESTRUTURADO</h1><hr>', $content_for_export);

    // 2. Convert custom styled divs (callouts) into simpler paragraphs with text markers.
    $content_for_export = preg_replace('/<div class="callout nota">/si', '<p><em>[Nota]:</em> ', $content_for_export);
    $content_for_export = preg_replace('/<div class="callout alerta">/si', '<p><strong>[Alerta]:</strong> ', $content_for_export);
    $content_for_export = str_ireplace('</div>', '</p>', $content_for_export); // Case-insensitive replace for closing div
}

// 6. Construct the final, self-contained HTML
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
    {$content_for_export}
</body>
</html>
HTML;

// 7. Generate file based on format
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
