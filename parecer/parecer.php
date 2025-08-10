<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Conexão com o Banco de Dados ---
$servername = "localhost";
$username = "bubba_parecer";
$password = "#aD{WhbJ8y]b*!6?";
$dbname = "bubba_analises";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// --- Obter e Validar CPF ---
if (!isset($_GET['cpf'])) {
    die('CPF não informado.');
}
$cpf = $conn->real_escape_string($_GET['cpf']);

// --- Consultar o Parecer ---
$sql = "SELECT * FROM parecer_estruturado WHERE cpf = '$cpf'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die('Nenhum parecer encontrado para o CPF informado.');
}
$parecer = $result->fetch_assoc();

// --- Processamento dos Dados ---
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
    // Limpeza para evitar <ul> aninhado incorretamente
    $text = str_replace('</ul><ul>', '', $text);
    $text = str_replace('</ol><ol>', '', $text);
    return $text;
}

function parse_markdown_table($markdown) {
    $lines = explode("\n", trim($markdown));
    if (count($lines) < 2) return $markdown; // Não é uma tabela válida

    $html = '<table style="width:100%; border-collapse: collapse;" border="1">';

    // Cabeçalho
    $header = array_map('trim', explode('|', trim($lines[0], '|')));
    $html .= '<thead><tr>';
    foreach ($header as $col) {
        $html .= '<th style="padding: 8px; background-color: #f2f2f2;">' . htmlspecialchars($col) . '</th>';
    }
    $html .= '</tr></thead>';

    // Corpo
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

ob_start();
foreach ($partes as $nome_parte => $dados_parte) {
    if (isset($dados_parte['conteudo_bruto'])) {
        echo '<h2>' . htmlspecialchars($dados_parte['titulo']) . '</h2>';
        // Verifica se é a parte 8 para tratar como tabela markdown
        if ($nome_parte === 'parte_8_simulacoes') {
             echo parse_markdown_table($dados_parte['conteudo_bruto']);
        } else {
            echo '<pre>' . htmlspecialchars($dados_parte['conteudo_bruto']) . '</pre>';
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
        foreach ($conteudo['blocos'] as $bloco) {
            echo '<p>' . markdown_to_html($bloco) . '</p>';
        }
    }
    if (isset($conteudo['paragrafos'])) {
        foreach ($conteudo['paragrafos'] as $paragrafo) {
            echo '<p>' . markdown_to_html($paragrafo) . '</p>';
        }
    }
    if (isset($conteudo['tabela'])) {
        echo '<table style="width:100%; border-collapse: collapse;" border="1">';
        echo '<thead><tr>';
        foreach ($conteudo['tabela']['cabecalho'] as $th) {
            echo '<th style="padding: 8px; background-color: #f2f2f2;">' . htmlspecialchars($th) . '</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($conteudo['tabela']['linhas'] as $tr) {
            echo '<tr>';
            foreach ($tr as $td) {
                echo '<td style="padding: 8px;">' . markdown_to_html($td) . '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table><br>';
    }
    if (isset($conteudo['citações'])) {
        echo '<h4>Citações e Jurisprudências:</h4><ul>';
        foreach ($conteudo['citações'] as $citacao) {
            echo '<li><strong>' . htmlspecialchars($citacao['jurisprudencia']) . ':</strong> ' . markdown_to_html($citacao['texto']) . '</li>';
        }
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
    <script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
    <style>
        :root { --primary-color: #2c3e50; --secondary-color: #3498db; --bg-color: #ecf0f1; --font-color: #34495e; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; background-color: var(--bg-color); color: var(--font-color); }
        .header { background-color: var(--primary-color); color: white; padding: 20px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { margin: 0; font-size: 2em; }
        .container { max-width: 1100px; margin: 20px auto; padding: 20px; background-color: white; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 8px; }
        .actions { text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd; }
        .btn { background-color: var(--secondary-color); color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.3s, transform 0.2s; margin: 5px; }
        .btn:hover { background-color: #2980b9; transform: translateY(-2px); }
        .btn-pdf { background-color: #e74c3c; }
        .btn-pdf:hover { background-color: #c0392b; }
        .loader { border: 5px solid #f3f3f3; border-radius: 50%; border-top: 5px solid var(--secondary-color); width: 40px; height: 40px; animation: spin 1s linear infinite; display: none; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        /* Estilos para o conteúdo do editor */
        h1, h2, h3 { color: var(--primary-color); }
        table { border-collapse: collapse; width: 100%; margin-bottom: 1em; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        mark { background-color: #f1c40f; padding: 2px 4px; border-radius: 3px; }
        hr { border: 0; height: 1px; background: #ccc; margin: 2em 0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Parecer Previdenciário Estruturado</h1>
    </div>

    <div class="container">
        <div class="actions">
            <button class="btn btn-word" onclick="exportWord()">Exportar para Word (.docx)</button>
            <button class="btn btn-pdf" onclick="exportPDF()">Exportar para PDF</button>
        </div>
        <div id="loader" class="loader"></div>

        <div id="editor-container"></div>
    </div>

    <script>
        let editor; // Variável global para o editor

        ClassicEditor
            .create(document.querySelector('#editor-container'), {
                language: 'pt-br'
            })
            .then(newEditor => {
                editor = newEditor;
                editor.setData(<?php echo json_encode($editor_content); ?>);
            })
            .catch(error => {
                console.error(error);
            });

        function showLoader(show) {
            document.getElementById('loader').style.display = show ? 'block' : 'none';
        }

        function exportWord() {
            showLoader(true);
            const header = "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body>";
            const footer = "</body></html>";
            const content = editor.getData();
            const source = header + content + footer;

            const fileBlob = htmlDocx.asBlob(source);

            saveAs(fileBlob, 'parecer-previdenciario.docx');
            showLoader(false);
        }

        function exportPDF() {
            showLoader(true);
            const editorContent = document.querySelector('.ck-editor__editable');

            html2canvas(editorContent, {
                scale: 2, // Aumenta a resolução da captura
                useCORS: true
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;

                const pdf = new jsPDF({
                    orientation: 'p',
                    unit: 'px',
                    format: [canvas.width, canvas.height]
                });

                pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
                pdf.save('parecer-previdenciario.pdf');
                showLoader(false);
            }).catch(err => {
                console.error("Erro ao gerar PDF:", err);
                showLoader(false);
                alert("Ocorreu um erro ao tentar gerar o PDF. Verifique o console para mais detalhes.");
            });
        }
    </script>
</body>
</html>