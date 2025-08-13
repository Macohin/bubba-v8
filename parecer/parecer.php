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

// --- Content Generation Logic ---
ob_start();

// Add the main title as requested
echo '<h1><strong>Parecer Previdenciário</strong></h1><hr>';

// Loop through the database result fields
foreach ($parecer as $key => $value) {
    // Process only fields that are structured as 'parte_X_...' and contain JSON data
    if (strpos($key, 'parte_') === 0 && !is_null($value)) {
        $data = json_decode($value, true);

        // Check if JSON decoding was successful and the expected keys exist
        if (json_last_error() === JSON_ERROR_NONE && isset($data['titulo']) && isset($data['conteudo'])) {
            // Append the section title and the pre-formatted HTML content
            echo '<h2>' . htmlspecialchars($data['titulo']) . '</h2>';
            echo $data['conteudo']; // Echoing raw HTML as it's pre-formatted by the AI
            echo '<hr>';
        }
    }
}
$conn->close();
$editor_content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parecer Previdenciário - Bubba A.I.</title>
    <script src="https://cdn.tiny.cloud/1/0wm27s4nqw0slo5s3z54unbiz38omlc8v450ost64vww521e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
            flex-direction: column;
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
        .hero{display:flex; flex-direction:column; align-items:center; text-align:center; margin-bottom: 2rem;}
        .ascii{
            white-space:pre; user-select:none; margin:0 auto; max-width:100%;
            line-height:1.02; letter-spacing:0;
            font-size: clamp(10px, 2vw, 18px);
            color: transparent;
            background-image: radial-gradient(120% 120% at 50% 20%, #c7e6ff, #ffffff 45%, #dff7ff 70%);
            -webkit-background-clip: text; background-clip: text;
            text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
            filter: drop-shadow(0 0 6px #a3d4ff) drop-shadow(0 0 14px #78ffe6);
        }
        .tagline{
            margin-top:10px; font-size:clamp(12px,1.2vw,14px); letter-spacing:1px;
            color: #9B84D4; /* Using a color from the theme */
            text-shadow: 0 0 4px rgba(255,255,255,0.6), 0 0 8px #00E5FF;
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
        .actions {
            padding: 10px;
            text-align: center;
            border-bottom: 1px dashed rgba(255,255,255,.12);
            flex-shrink: 0;
        }
        .btn {
            background-color: #00E5FF;
            color: #1A0033;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.2s;
            margin: 0 5px;
        }
        .btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        .btn-pdf {
            background-color: #FF00FF;
        }
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

    <section class="hero">
        <pre class="ascii" aria-hidden="true">
███╗   ███╗ █████╗  ██████╗ ██████╗ ██╗  ██╗██╗███╗   ██╗     █████╗ ██╗
████╗ ████║██╔══██╗██╔════╝██╔═══██╗██║  ██║██║████╗  ██║    ██╔══██╗██║
██╔████╔██║███████║██║     ██║   ██║███████║██║██╔██╗ ██║    ███████║██║
██║╚██╔╝██║██╔══██║██║     ██║   ██║██╔══██║██║██║╚██╗██║    ██╔══██║██║
██║ ╚═╝ ██║██║  ██║╚██████╗╚██████╔╝██║  ██║██║██║ ╚████║    ██║  ██║██║
╚═╝     ╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝ ╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝    ╚═╝  ╚═╝╚═╝
        </pre>
        <div class="tagline">// Asynchronous AI Multi-Agents — Automated Legal Analysis</div>
    </section>

    <div class="card">
        <div class="chrome">
            <span class="dot d1"></span>
            <span class="dot d2"></span>
            <span class="dot d3"></span>
            <span style="margin-left: auto; font-size: 14px;">retirement.calc • /bubba-ai</span>
        </div>
        <div class="actions">
            <button class="btn" onclick="exportDocument('docx')">📄 Exportar Word</button>
            <button class="btn btn-pdf" onclick="exportDocument('pdf')">📑 Exportar PDF</button>
        </div>
        <div class="editor-wrapper">
            <textarea id="parecer-editor"><?php echo htmlspecialchars($editor_content); ?></textarea>
        </div>
    </div>

    <script>
        function exportDocument(format) {
            const loader = document.createElement('div');
            loader.style.position = 'fixed';
            loader.style.top = '50%';
            loader.style.left = '50%';
            loader.style.transform = 'translate(-50%, -50%)';
            loader.style.padding = '20px';
            loader.style.background = 'rgba(0,0,0,0.8)';
            loader.style.color = 'white';
            loader.style.zIndex = '10000';
            loader.textContent = 'Gerando seu documento...';
            document.body.appendChild(loader);

            const content = tinymce.get('parecer-editor').getContent();
            const cpf = "<?php echo urlencode($cpf); ?>";

            const formData = new FormData();
            formData.append('content', content);
            formData.append('format', format);
            formData.append('cpf', cpf);

            fetch('../export.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok.');
                }
                const disposition = response.headers.get('Content-Disposition');
                let filename = 'parecer.docx'; // default
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }
                return response.blob().then(blob => ({ blob, filename }));
            })
            .then(({ blob, filename }) => {
                document.body.removeChild(loader);
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
            })
            .catch(error => {
                document.body.removeChild(loader);
                console.error('There has been a problem with your fetch operation:', error);
                alert('Erro ao gerar o documento. Verifique o console para mais detalhes.');
            });
        }

        tinymce.init({
            selector: '#parecer-editor',
            plugins: 'table advtable lists link image media code fullscreen preview wordcount',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | styleselect fontfamily fontsize | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | table | link image media | code fullscreen preview',
            height: '100%',
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            content_css: '../css/abnt-style.css',
        });
    </script>
</body>
</html>
