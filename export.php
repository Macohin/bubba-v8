<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Composer Autoloader ---
// Required for PHPWord
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Font;

// --- Main Controller Logic ---

// 1. Get and Validate Input
$post_content = $_POST['content'] ?? '';
$format = $_POST['format'] ?? '';
$cpf = $_POST['cpf'] ?? 'documento';
$date = date('Y-m-d');

if (empty($post_content) || empty($format)) {
    http_response_code(400);
    die('Error: Missing content or format.');
}

// 2. Sanitize CPF for filename
$cpf_sanitized = preg_replace('/[^0-9]/', '', $cpf);
$filename_base = "parecer_{$cpf_sanitized}_{$date}";

// 3. Route to the correct handler based on format
switch ($format) {
    case 'pdf':
        generate_pdf($post_content, $filename_base);
        break;
    case 'docx':
        generate_docx($post_content, $filename_base);
        break;
    default:
        http_response_code(400);
        die('Error: Invalid format specified.');
}

// --- PDF Generation Function (using Puppeteer) ---
function generate_pdf($html_content, $filename_base) {
    // Create a temporary HTML file to pass to Puppeteer
    $tmp_dir = sys_get_temp_dir();
    $tmp_html_file = tempnam($tmp_dir, 'puppeteer_html_');
    if ($tmp_html_file === false) {
        http_response_code(500);
        die('Error: Could not create temporary HTML file.');
    }

    // Rename to have a .html extension, which Puppeteer prefers
    $html_file_with_ext = $tmp_html_file . '.html';
    rename($tmp_html_file, $html_file_with_ext);

    // Construct the full HTML with a link to the print CSS
    // Using an absolute file path for the CSS link is most reliable
    $css_path = __DIR__ . '/css/print.css';
    $full_html = <<<HTML
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Parecer Previdenciário</title>
    <link rel="stylesheet" href="{$css_path}">
</head>
<body>
    {$html_content}
</body>
</html>
HTML;

    file_put_contents($html_file_with_ext, $full_html);

    // Execute the Node.js Puppeteer script
    // Pass the path to the temporary HTML file as an argument
    // Redirect stderr to stdout to capture any errors from the Node script
    $node_script_path = __DIR__ . '/generate-pdf.js';
    $command = "node " . escapeshellarg($node_script_path) . " " . escapeshellarg($html_file_with_ext) . " 2>&1";

    // Execute the command and capture the output
    $pdf_output = shell_exec($command);

    // Clean up the temporary HTML file
    unlink($html_file_with_ext);

    // Check if the output contains "Error:", which indicates a failure in the Node script
    if (strpos($pdf_output, 'Error:') === 0 || strpos($pdf_output, 'Puppeteer PDF Generation Error:') !== false) {
        http_response_code(500);
        // Return the actual error from Puppeteer for easier debugging
        die("Error generating PDF with Puppeteer: " . htmlspecialchars($pdf_output));
    } else {
        // If successful, stream the PDF back to the client
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"{$filename_base}.pdf\"");
        header('Content-Length: ' . strlen($pdf_output));
        echo $pdf_output;
    }
}

// --- DOCX Generation Function (using programmatic creation) ---
function generate_docx($html_content, $filename_base) {
    try {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Load the HTML into a DOMDocument to parse it
        $dom = new DOMDocument();
        // Suppress errors from invalid HTML, but log them
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html_content);
        libxml_clear_errors();

        // Recursively traverse the DOM and add elements to PHPWord
        traverse_dom_nodes($dom->getElementsByTagName('body')->item(0), $section);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment; filename=\"{$filename_base}.docx\"");
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');

    } catch (Exception $e) {
        http_response_code(500);
        error_log('Error creating DOCX file: ' . $e->getMessage());
        die('Error creating DOCX file. Please check server logs.');
    }
}

function traverse_dom_nodes($node, &$parent_element) {
    if ($node === null) return;

    foreach ($node->childNodes as $child) {
        switch ($child->nodeName) {
            case 'h1':
                $parent_element->addTitle($child->textContent, 1);
                break;
            case 'h2':
                $parent_element->addTitle($child->textContent, 2);
                break;
            case 'h3':
                $parent_element->addTitle($child->textContent, 3);
                break;
            case 'p':
                // Handle paragraphs with potentially mixed content (bold, italic)
                $textrun = $parent_element->addTextRun();
                foreach ($child->childNodes as $p_child) {
                    if ($p_child->nodeName === 'strong' || $p_child->nodeName === 'b') {
                        $textrun->addText($p_child->textContent, ['bold' => true]);
                    } elseif ($p_child->nodeName === 'em' || $p_child->nodeName === 'i') {
                        $textrun->addText($p_child->textContent, ['italic' => true]);
                    } elseif ($p_child->nodeName === '#text') {
                        $textrun->addText(htmlspecialchars($p_child->textContent));
                    }
                }
                break;
            case 'ul':
            case 'ol':
                foreach ($child->getElementsByTagName('li') as $li) {
                    $parent_element->addListItem($li->textContent, 0);
                }
                break;
            case 'table':
                $table = $parent_element->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
                foreach ($child->getElementsByTagName('tr') as $tr) {
                    $table->addRow();
                    foreach ($tr->childNodes as $td_or_th) {
                         if($td_or_th->nodeName === 'td' || $td_or_th->nodeName === 'th') {
                            $cell = $table->addCell(4500); // Approx width
                            $is_header = $td_or_th->nodeName === 'th';
                            $cell->addText($td_or_th->textContent, ['bold' => $is_header]);
                         }
                    }
                }
                 $parent_element->addTextBreak(1); // Add space after table
                break;
            case 'hr':
                $parent_element->addPageBreak();
                break;
            case 'div': // Handle simplified callouts
                 if ($child->hasAttribute('class') && strpos($child->getAttribute('class'), 'callout') !== false) {
                     $parent_element->addText("---", ['italic' => true]);
                     traverse_dom_nodes($child, $parent_element);
                     $parent_element->addText("---", ['italic' => true]);
                 } else {
                    // For other divs, just process their children
                    traverse_dom_nodes($child, $parent_element);
                 }
                break;
             case 'img':
                // PHPWord has trouble with remote images in HTML. A more robust solution
                // would be to download the image first, but for now we will add a placeholder.
                $src = $child->getAttribute('src');
                $alt = $child->getAttribute('alt');
                $parent_element->addText("[Image: " . htmlspecialchars($alt) . " - " . htmlspecialchars($src) . "]", ['italic' => true, 'color' => '888888']);
                break;
        }
    }
}

exit;
?>
