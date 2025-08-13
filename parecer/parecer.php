<?php
// --- Database Connection & Data Fetching (Copied from original) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "bubba_parecer";
$password = "#aD{WhbJ8y]b*!6?";
$dbname = "bubba_analises";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (!isset($_GET['cpf'])) {
    die('CPF não informado.');
}
$cpf = $conn->real_escape_string($_GET['cpf']);

$sql = "SELECT * FROM parecer_estruturado WHERE cpf = '$cpf'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die('Nenhum parecer encontrado para o CPF informado.');
}
$parecer = $result->fetch_assoc();

// --- Helper Functions for Content Parsing (Copied from original) ---
function markdown_to_html($text) {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.*?)__/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/==(.*?)==/s', '<mark>$1</mark>', $text);
    $text = preg_replace('/~~(.*?)~~/s', '<del>$1</del>', $text);
    $text = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $text);
    $text = preg_replace('/_(.*?)_/s', '<em>$1</em>', $text);
    $text = preg_replace('/^# (.*)/m', '<h1>$1</h1>', $text);
    $text = preg_replace('/^## (.*)/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^### (.*)/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^\s*-\s(.*)/m', '<ul><li>$1</li></ul>', $text);
    $text = preg_replace('/^\s*\d\.\s(.*)/m', '<ol><li>$1</li></ol>', $text);
    $text = nl2br($text);
    $text = str_replace('</ul><ul>', '', $text);
    $text = str_replace('</ol><ol>', '', $text);
    return $text;
}

function parse_markdown_table($markdown) {
    $lines = explode("\n", trim($markdown));
    if (count($lines) < 2) return $markdown;
    $html = '<table style="width:100%; border-collapse: collapse;" border="1">';
    $header = array_map('trim', explode('|', trim($lines[0], '|')));
    $html .= '<thead><tr>';
    foreach ($header as $col) {
        $html .= '<th style="padding: 8px; background-color: #f2f2f2;">' . htmlspecialchars($col) . '</th>';
    }
    $html .= '</tr></thead>';
    $html .= '<tbody>';
    for ($i = 2; $i < count($lines); $i++) {
        $row = array_map('trim', explode('|', trim($lines[$i], '|')));
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td style="padding: 8px;">' . markdown_to_html($cell) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table><br>';
    return $html;
}

// --- Content Generation (Copied and adapted from original) ---
ob_start();
// Add the main title as requested
echo '<h1><strong>Parecer Previdenciário</strong></h1><hr>';

$partes = [];
foreach ($parecer as $key => $value) {
    if (strpos($key, 'parte_') === 0 && !is_null($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $partes[$key] = $decoded;
        } else {
            $titulo = ucfirst(str_replace(['parte_', '_'], ['', ' '], $key));
            $partes[$key] = ['titulo' => $titulo, 'conteudo_bruto' => $value];
        }
    }
}
$conn->close();

foreach ($partes as $nome_parte => $dados_parte) {
    if (isset($dados_parte['conteudo_bruto'])) {
        echo '<h2>' . htmlspecialchars($dados_parte['titulo']) . '</h2>';
        if ($nome_parte === 'parte_8_simulacoes') {
             echo parse_markdown_table($dados_parte['conteudo_bruto']);
        } else {
            echo '<p>' . nl2br(htmlspecialchars($dados_parte['conteudo_bruto'])) . '</p>';
        }
        echo '<hr>';
        continue;
    }
    $secao_principal = key($dados_parte);
    $conteudo = $dados_parte[$secao_principal];
    if (isset($conteudo['titulo'])) {
        echo '<h2>' . htmlspecialchars($conteudo['titulo']) . '</h2>';
    }
    if (isset($conteudo['blocos'])) {
        foreach ($conteudo['blocos'] as $bloco) { echo '<p>' . markdown_to_html($bloco) . '</p>'; }
    }
    if (isset($conteudo['paragrafos'])) {
        foreach ($conteudo['paragrafos'] as $paragrafo) { echo '<p>' . markdown_to_html($paragrafo) . '</p>'; }
    }
    if (isset($conteudo['tabela'])) {
        echo '<table style="width:100%; border-collapse: collapse;" border="1"><thead><tr>';
        foreach ($conteudo['tabela']['cabecalho'] as $th) { echo '<th style="padding: 8px; background-color: #f2f2f2;">' . htmlspecialchars($th) . '</th>'; }
        echo '</tr></thead><tbody>';
        foreach ($conteudo['tabela']['linhas'] as $tr) {
            echo '<tr>';
            foreach ($tr as $td) { echo '<td style="padding: 8px;">' . markdown_to_html($td) . '</td>'; }
            echo '</tr>';
        }
        echo '</tbody></table><br>';
    }
    if (isset($conteudo['citações'])) {
        echo '<h4>Citações e Jurisprudências:</h4><ul>';
        foreach ($conteudo['citações'] as $citacao) { echo '<li><strong>' . htmlspecialchars($citacao['jurisprudencia']) . ':</strong> ' . markdown_to_html($citacao['texto']) . '</li>'; }
        echo '</ul>';
    }
    echo '<hr>';
}
$editor_content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parecer Previdenciário - Bubba A.I.</title>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        :root {
            --bg: #1A0033;
            --card-bg: linear-gradient(180deg, rgba(32, 16, 65, 0.85), rgba(37, 17, 77, 0.85));
            --card-border: rgba(255,255,255,.15);
            --shadow: 0 0 18px rgba(88,166,255,.28), 0 0 36px rgba(55,227,195,.18);
        }
        body {
            font-family: 'Fira Code', monospace;
            margin: 0;
            background-color: var(--bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2em;
            box-sizing: border-box;
        }
        #background-video {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            z-index: -1;
            opacity: 0.5;
            object-fit: cover;
        }
        .card {
            width: 100%;
            max-width: 1200px;
            height: 90vh;
            background: var(--card-bg);
            border: 2px dotted var(--card-border);
            border-radius: 18px;
            box-shadow: 0 12px 60px rgba(0,0,0,.35), var(--shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .chrome {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-bottom: 1px dashed rgba(255,255,255,.12);
            background: linear-gradient(0deg,#ffffff10,#0000);
            flex-shrink: 0;
            color: #9B84D4;
        }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .d1 { background: #ff5f56; }
        .d2 { background: #ffbd2e; }
        .d3 { background: #27c93f; }
        .editor-wrapper {
            flex-grow: 1;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <video id="background-video" autoplay loop muted playsinline src="https://bubba.macohin.ai/bg/bg.mp4"></video>
    <div class="card">
        <div class="chrome">
            <span class="dot d1"></span>
            <span class="dot d2"></span>
            <span class="dot d3"></span>
        </div>
        <div class="editor-wrapper">
            <textarea id="parecer-editor"><?php echo htmlspecialchars($editor_content); ?></textarea>
        </div>
    </div>

    <script>
        tinymce.init({
            selector: '#parecer-editor',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            height: '100%',
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            content_css: 'default',
            content_style: "html, body { font-family:Helvetica,Arial,sans-serif; font-size:14px; background-color: rgba(255,255,255,0.85); }",
        });
    </script>
</body>
</html>
