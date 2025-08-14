<?php
// --- Database Connection & Data Fetching ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials - replace with your actual credentials
$servername = "localhost";
$username = "bubba_parecer";
$password = "#aD{WhbJ8y]b*!6?";
$dbname = "bubba_analises";

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Get CPF from URL parameter and sanitize it
if (!isset($_GET['cpf'])) {
    die('CPF not provided.');
}
$cpf = $conn->real_escape_string($_GET['cpf']);

// Fetch the structured report from the database
$sql = "SELECT * FROM parecer_estruturado WHERE cpf = '$cpf'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die('No report found for the provided CPF.');
}
$parecer = $result->fetch_assoc();

// --- Content Generation Logic ---
// Use an output buffer to build the HTML string for the editor
ob_start();

// Add the image header, which will be part of the exported document content.
echo '<div style="text-align: center; margin-bottom: 20px;"><img src="https://bubba.macohin.ai/bg/cabecalho.png" alt="Cabeçalho do Parecer" style="width: 100%; max-width: 750px; border-radius: 10px;"></div><hr>';

// Loop through all columns of the fetched row
foreach ($parecer as $key => $value) {
    // We are only interested in columns named 'parte_X_...' that contain data
    if (strpos($key, 'parte_') === 0 && !is_null($value)) {
        // Decode the JSON content of the column
        $data = json_decode($value, true);

        // Check if JSON is valid and contains the expected keys
        if (json_last_error() === JSON_ERROR_NONE && isset($data['titulo']) && isset($data['conteudo'])) {
            // Append the pre-formatted HTML content from the 'conteudo' key.
            // No need to add the title separately as it's already inside the content.
            echo $data['conteudo'];
            echo '<hr>'; // Add a separator between parts
        }
    }
}
$conn->close();
// Get the complete HTML string from the buffer
$editor_content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parecer Previdenciário - MACOHIN AI</title>

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:title" content="Parecer Previdenciário - Bubba A.I.">
    <meta property="og:description" content="Clique para visualizar o parecer completo do caso.">
    <meta property="og:image" content="https://bubba.macohin.ai/bg/parecer-pronto.jpg">
    <meta property="og:url" content="https://bubba.macohin.ai/parecer/parecer.php?cpf=<?php echo urlencode($_GET['cpf']); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Parecer Previdenciário - Bubba A.I.">
    <meta name="twitter:description" content="Clique para visualizar o parecer completo do caso.">
    <meta name="twitter:image" content="https://bubba.macohin.ai/bg/parecer-pronto.jpg">

    <!-- TinyMCE a a-service script -->
    <script src="https://cdn.tiny.cloud/1/0wm27s4nqw0slo5s3z54unbiz38omlc8v450ost64vww521e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;700&family=VT323&display=swap" rel="stylesheet">
    <style>
        /* This styling ensures the page itself has the hacker theme, matching index.php */
        :root {
            --bg-main: #1A0033;
            --text-primary: #FFFFFF;
            --text-secondary: #9B84D4;
            --accent-func: #00E5FF;
            --card-bg: rgba(26, 0, 51, 0.75);
            --card-border: rgba(255, 255, 255, .15);
            --shadow: 0 0 18px rgba(88,166,255,.28), 0 0 36px rgba(55,227,195,.18);
        }
        body {
            font-family: 'Fira Code', monospace;
            margin: 0;
            background-color: var(--bg-main);
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 2em;
            box-sizing: border-box;
        }
        #background-video {
            position: fixed; top: 50%; left: 50%;
            min-width: 100%; min-height: 100%;
            width: auto; height: auto;
            transform: translateX(-50%) translateY(-50%);
            z-index: -1;
            opacity: 0.5;
            object-fit: cover;
        }
        .hero { display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 2rem; }
        .ascii {
            white-space: pre; user-select: none; margin: 0 auto; max-width: 100%;
            line-height: 1.02; letter-spacing: 0;
            font-size: clamp(10px, 2vw, 18px);
            color: transparent;
            background-image: radial-gradient(120% 120% at 50% 20%, #c7e6ff, #ffffff 45%, #dff7ff 70%);
            -webkit-background-clip: text; background-clip: text;
            filter: drop-shadow(0 0 6px #a3d4ff) drop-shadow(0 0 14px #78ffe6);
        }
        .tagline {
            margin-top: 10px; font-size: clamp(12px, 1.2vw, 14px); letter-spacing: 1px;
            color: var(--text-secondary);
            text-shadow: 0 0 4px rgba(255, 255, 255, 0.6), 0 0 8px var(--accent-func);
        }
        .card {
            width: 100%;
            max-width: 1200px; /* Wider card for the editor */
            height: 90vh; /* Take up most of the viewport height */
            background: var(--card-bg);
            border: 2px dotted var(--card-border);
            border-radius: 18px;
            box-shadow: 0 12px 60px rgba(0, 0, 0, .35), var(--shadow);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .chrome {
            display: flex; align-items: center; gap: 8px; padding: 10px 12px;
            border-bottom: 1px dashed rgba(255, 255, 255, .12);
            background: linear-gradient(0deg, #ffffff10, #0000);
            flex-shrink: 0; color: var(--text-secondary);
        }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .d1 { background: #ff5f56; }
        .d2 { background: #ffbd2e; }
        .d3 { background: #27c93f; }
        .actions {
            padding: 10px; text-align: center;
            border-bottom: 1px dashed rgba(255, 255, 255, .12);
            flex-shrink: 0; background: rgba(0,0,0,0.2);
        }
        .btn {
            background-color: transparent;
            border: 1px solid var(--accent-func);
            color: var(--accent-func);
            padding: 8px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.2s ease;
            margin: 0 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn:hover {
            background-color: var(--accent-func);
            color: var(--bg-main);
            box-shadow: 0 0 15px var(--accent-func);
        }
        .btn-pdf {
            border-color: #FF00FF;
            color: #FF00FF;
        }
        .btn-pdf:hover {
            background-color: #FF00FF;
            color: var(--bg-main);
            box-shadow: 0 0 15px #FF00FF;
        }
        .editor-wrapper {
            flex-grow: 1; /* Make the editor wrapper fill the available space */
            position: relative;
        }
        /* Ensure TinyMCE fills the wrapper */
        .tox-tinymce {
            border: none !important;
            height: 100% !important;
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
            <span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span>
            <span style="margin-left: auto; font-size: 14px;">parecer.final • /bubba-ai/review</span>
        </div>
        <div class="editor-wrapper">
            <textarea id="parecer-editor"><?php echo htmlspecialchars($editor_content); ?></textarea>
        </div>
    </div>

    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#parecer-editor',
            // Essential Plan plugins
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',

            // Configuration options
            height: '100%', // Take full height of the wrapper
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            image_advtab: true,
            importcss_append: true,

            // Use the print stylesheet for an accurate preview of the PDF.
            content_css: '../css/print.css',

            // Make the UI of the editor itself dark to match the theme
            skin: 'oxide-dark',
            // Apply the transparent background directly to the body inside the iframe.
            content_style: "body { background-color: rgba(255, 255, 255, 0.85) !important; }"
        });
    </script>
</body>
</html>
