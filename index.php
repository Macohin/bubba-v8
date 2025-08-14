<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Bubba A.I. - Análise Previdenciária</title>
<meta name="theme-color" content="#18092d" />
<meta name="description" content="Bubba A.I. - Análise Previdenciária" />
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js" integrity="sha512-Qlv6VSKh1gDKGoJbnyA5RMXYcvnpIqhO++MhIM2fStMcGT9i2T//tSwYFlcyoRRDcDZ+TYHpH8azBBCyhpSeqw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;700&family=VT323&display=swap" rel="stylesheet">
<style>
  /* Jules Theme - Inspired by User Request */
  :root{
    --bg-main: #1A0033;
    --text-primary: #FFFFFF;
    --text-secondary: #9B84D4;
    --accent-string: #FFD166;
    --accent-func: #00E5FF;
    --accent-keyword: #FF00FF;
    --accent-green: #39FF14;
    --accent-pink: #FF3366;

    /* Legacy mapping for compatibility */
    --bg: var(--bg-main);
    --txt: var(--text-primary);
    --muted: var(--text-secondary);
    --c1: var(--accent-func);
    --c2: var(--accent-keyword);
    --c3: var(--accent-string);

    --grid: rgba(155, 132, 212, .1);
    --card: rgba(26, 0, 51, 0.6);
    --card2: rgba(30, 0, 60, 0.7);
    --mono: 'Fira Code', monospace;
    --logo-font: 'VT323', monospace;
    --shadow: 0 0 18px rgba(88,166,255,.28), 0 0 36px rgba(55,227,195,.18);
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    color:var(--txt);
    font-family:var(--mono);
    background-color: var(--bg);
    overflow-x:hidden;
    display:flex;
    flex-direction:column;
    min-height:100svh;
  }

  .text-glow {
    text-shadow: 0 0 4px rgba(255,255,255,0.6), 0 0 8px var(--accent-func);
  }

  #background-video {
    position: fixed; top: 50%; left: 50%;
    min-width: 100%; min-height: 100%;
    width: auto; height: auto;
    transform: translateX(-50%) translateY(-50%);
    z-index: -2; opacity: 0.5; object-fit: cover;
  }

  #rain{position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.1}
  .grid{position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.3;
    background:
      linear-gradient(transparent 31px, var(--grid) 32px),
      linear-gradient(90deg, transparent 31px, var(--grid) 32px);
    background-size:32px 32px;
    mask-image: radial-gradient(1100px 520px at 50% -10%, #000 35%, transparent 80%);
  }

  header{
    position:relative; z-index:2; display:flex; flex-direction:column;
    align-items:center; gap:10px; padding:16px;
  }

  .container{position:relative; z-index:2; width:100%; max-width:1120px; margin:0 auto;
    padding: clamp(16px, 3vw, 32px); display:flex; flex-direction:column; align-items:center;
    flex-grow: 1;}

  .hero{display:flex; flex-direction:column; align-items:center; text-align:center;
    margin-top: clamp(6px, 3vh, 16px);}

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
    color: var(--text-secondary);
    text-shadow: 0 0 4px rgba(255,255,255,0.6), 0 0 8px var(--accent-func);
  }

  .section{width:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; flex-grow:1; margin-top: clamp(18px, 4vh, 32px);}
  .card{
    width:min(900px, 95%); background:linear-gradient(180deg, rgba(32, 16, 65, 0.85), rgba(37, 17, 77, 0.85));
    border:2px dotted rgba(255,255,255,.15); border-radius:18px;
    box-shadow:0 12px 60px rgba(0,0,0,.35), var(--shadow); overflow:hidden;
    display:flex; flex-direction:column; flex-grow:1; max-height: 90%; /* prevent card from being too tall */
  }
  .chrome{display:flex; align-items:center; gap:8px; padding:10px 12px;
    border-bottom:1px dashed rgba(255,255,255,.12);
    background:linear-gradient(0deg,#ffffff10,#0000); font-size:12px; color:var(--muted)}
  .dot{width:10px;height:10px;border-radius:50%}
  .d1{background:#ff5f56}.d2{background:#ffbd2e}.d3{background:#27c93f}

  .card-body{padding:18px; font-size:clamp(13px,1.6vw,15px); line-height:1.55; color:var(--txt);
    display:flex; flex-direction:column; flex-grow:1; min-height:0;}

  .comment{color:var(--muted); opacity:.9}

  /* Use Cases Section */
  .examples{width:100%; max-width:1120px; margin-top: clamp(16px, 5vh, 40px);
    display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:18px}
  .ex{
    background: var(--card2);
    border:1px solid var(--accent-func);
    border-radius:16px; padding:16px;
    box-shadow: 0 0 8px var(--accent-func);
  }
  .ex h4{margin:0 0 8px 0; font-size:clamp(13px,1.4vw,14px); letter-spacing:.4px; color:var(--c1)}
  .ex p{margin:0; font-size:clamp(12px,1.2vw,13px); color:var(--txt); line-height:1.55}
  .ex .meta{margin-top:10px; font-size:12px; color:var(--muted); opacity:.85}
  @media (max-width:900px){ .examples{grid-template-columns:1fr} }

  footer{margin-top:auto; position:relative; z-index:2; text-align:center; color:var(--muted);
    font-size:12px; opacity:.8; padding:22px 12px 28px}

  @media (prefers-reduced-motion: reduce){ #rain, .text-glow, .ascii { text-shadow:none!important; filter:none!important; } }

  /* App-specific styles */
  .card-body .space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 1.5rem; }
  .card-body label {
    color: var(--muted); font-size: 13px; letter-spacing: .5px;
    margin-bottom: .5rem; display: block;
  }
  .card-body input[type="text"] {
    background: var(--bg-main);
    border: 1px solid var(--muted);
    border-radius: 8px; padding: 10px 12px;
    color: var(--txt); width: 100%; font-family: var(--mono);
  }
  .card-body input[type="text"]:focus {
    outline: none; border-color: var(--c1); box-shadow: 0 0 5px var(--c1);
  }
  .card-body input[type="text"]::placeholder { color: var(--muted); opacity: 0.5; }
  .card-body #cpfError { color: var(--accent-pink); font-size: 12px; margin-top: .5rem; }

  .card-body #uploadPromptText { text-align: center; font-size: 1.125rem; margin-bottom: 1rem; }
  .card-body #dropzoneContainer label {
    border: 2px dashed rgba(255,255,255,.15);
    background: rgba(255,255,255,.05);
    border-radius: 12px;
    transition: background .2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 15rem;
    cursor: pointer;
  }
  .card-body #dropzoneContainer label:hover {
    background: rgba(255,255,255,.08);
    border-color: rgba(255,255,255,.3);
  }
  .card-body #dropzoneContainer svg { color: var(--muted); width: 2rem; height: 2rem; margin-bottom: 1rem; }
  .card-body #dropzoneContainer p { color: var(--muted); font-size: .875rem; }
  .card-body #fileListPreviewContainer {
    margin-top: 1rem;
    max-height: 110px; /* Set a max height */
    overflow-y: auto; /* Allow vertical scrolling */
    padding-right: 10px; /* Add some padding so scrollbar doesn't overlap content */
  }
  /* Custom Scrollbar for the file list */
  #fileListPreviewContainer::-webkit-scrollbar { width: 8px; }
  #fileListPreviewContainer::-webkit-scrollbar-track { background: transparent; }
  #fileListPreviewContainer::-webkit-scrollbar-thumb { background-color: var(--accent-func); border-radius: 20px; border: 2px solid var(--bg-main); }
  #fileListPreviewContainer::-webkit-scrollbar-thumb:hover { background-color: var(--accent-green); }

  .card-body #selectedFilesList { list-style-type: none; padding-left: 0; }
  .card-body #selectedFilesList li {
    color: var(--muted); font-size: 13px;
    padding-left: 1.5em; position: relative;
  }
  .card-body #selectedFilesList li::before {
    content: '✓'; color: var(--accent-green); position: absolute; left: 0;
  }

  .btn{ text-decoration:none; font-weight:800; letter-spacing:.3px; padding:10px 14px;
    border-radius:10px; border:1px solid rgba(255,255,255,.15); display:inline-block}
  .btn.primary{color:#091017; background:linear-gradient(180deg,#9dffce,#37e3c3)}

  #resultsArea {
    border-top: 1px dashed rgba(255,255,255,.16);
    padding-top: 18px; margin-top: 18px;
    overflow-y: auto;
    flex-grow: 1;
    min-height: 0;
    /* Hide scrollbar for IE, Edge and Firefox */
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
  }
  /* Hide scrollbar for Chrome, Safari and Opera */
  #resultsArea::-webkit-scrollbar {
    display: none;
  }
  #htmlResultContent {
    font-size:clamp(13px,1.6vw,15px); line-height:1.55; color:var(--txt);
  }
  #htmlResultContent h2 {
      font-size: 1.5em; font-weight: 800; color: var(--c1); margin-bottom: 1em;
  }
  #htmlResultContent p { margin-bottom: 1em; }
  #htmlResultContent strong { color: var(--c1); font-weight: 800; }
  #htmlResultContent ul { list-style-type: '» '; padding-left: 20px; margin-bottom: 1em; }
  #htmlResultContent hr { border-top: 1px dashed var(--muted); margin: 1.5em 0; }
  #htmlResultContent, #htmlResultContent * { font-family: var(--mono) !important; }

  .log-line { margin-bottom: 0.25rem; white-space: pre-wrap; }
  .log-start { color: var(--accent-func); }       /* » : function/variable */
  .log-success { color: var(--accent-green); }    /* ✓ : green highlight */
  .log-action {
    color: #FFFFFF; /* ⇅ : White Shine */
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.7);
  }
  .log-warning { color: var(--accent-string); }   /* ! : string */
  .log-error { color: var(--accent-pink); }       /* isError: pink/red */
  .log-info { color: var(--text-secondary); }    /* ℹ : secondary text */
</style>
</head>
<body>
  <video id="background-video" autoplay loop muted playsinline src="https://bubba.macohin.ai/bg/bg.mp4"></video>
  <canvas id="rain" aria-hidden="true"></canvas>
  <div class="grid" aria-hidden="true"></div>

  <header>
    <!-- Content removed as per user request -->
  </header>

  <div class="container">
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

    <section class="section" id="purpose">
      <article class="card" aria-label="Our purpose">
        <div class="chrome">
          <span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span>
          <span>retirement.calc • /bubba-ai</span>
        </div>
        <div class="card-body">
            <div id="appArea" class="space-y-6">
                <div id="cpfSection">
                    <label for="cpfInput">CPF (somente números):</label>
                    <input type="text" id="cpfInput" name="cpf" placeholder="00000000000" required>
                    <p id="cpfError" class="hidden"></p>
                </div>

                <p id="uploadPromptText">Para iniciar a análise previdenciária, envie seus documentos — é essencial anexar, no mínimo, o CNIS e a CTPS.</p>
                <div id="dropzoneContainer">
                    <label for="dropzone-file">
                        <div id="dropzoneInstructions">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p><span style="font-weight: 600;">Clique para enviar</span> ou arraste e solte</p>
                            <p style="font-size: .75rem;">PDF, JPG, PNG</p>
                            <p style="font-size: .75rem;">Ou use a <span style="font-weight: 600;">câmera</span> (celular)</p>
                        </div>
                        <input id="dropzone-file" type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png" capture="environment" />
                    </label>
                </div>
                <div id="fileListPreviewContainer" class="hidden">
                    <p style="font-weight: 600; margin-bottom: .5rem;">Arquivos selecionados:</p>
                    <ul id="selectedFilesList"></ul>
                </div>
                <button id="startAnalysisBtn" class="btn primary" style="width: 100%; font-size: 1rem; text-align: center;">
                    Bubba, efetue a análise previdenciária
                </button>
            </div>

            <div id="resultsArea" class="hidden space-y-6">
                <div id="htmlResultContent" class="prose-invert"></div>
                <!-- Export buttons removed as per user request -->
            </div>
            <button id="simulateCallbackBtn" class="hidden" style="margin-top: 1rem; padding: .5rem; background-color: #7c3aed; color: white; border-radius: .5rem; font-size: .75rem;">Simular Resultado Final (Teste)</button>
        </div>
      </article>
    </section>

    <section class="examples" id="use-cases" aria-label="Use cases">
      <div class="ex">
        <h4>&gt; Conferência Inteligente</h4>
        <p>Anexe seus documentos previdenciários; o Bubba AI executa data cross-check, varre CNIS × CTPS, identifica lacunas e sinaliza inconsistências para correção.</p>
        <div class="meta">Output: 📄 DOCX • 📋 Compliance checklist com trilha de auditoria</div>
      </div>
      <div class="ex">
        <h4>&gt; Relatório Automatizado</h4>
        <p>Dados de planilhas ou APIs; o Bubba AI compila, processa e renderiza gráficos e simulações com algorithmic precision, mantendo hash log de cada etapa.</p>
        <div class="meta">Output: 📄 PDF • 🔍 JSON técnico via API com assinatura digital</div>
      </div>
      <div class="ex">
        <h4>&gt; Simulação e Cenários</h4>
        <p>O Bubba AI calcula tempo de contribuição, carência e RMI, executa multi-rule engine com regras pré e pós-EC 103/2019, e retorna o cenário previdenciário mais vantajoso.</p>
        <div class="meta">Output: 📄 PDF • 📊 Comparativo de cenários com métricas avançadas</div>
      </div>
    </section>
  </div>

  <footer>
    MACOHIN AI • Asynchronous AI Agents<br/>
    Florida Limited Liability Company<br/>
    bubba.macohin.ai
  </footer>

  <script>
        // DOM Element References
        const appArea = document.getElementById('appArea');
        const resultsArea = document.getElementById('resultsArea');
        const htmlResultContent = document.getElementById('htmlResultContent');
        const exportButtonsContainer = document.getElementById('exportButtonsContainer');
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        const exportDocxBtn = document.getElementById('exportDocxBtn');
        const dropzoneFileInput = document.getElementById('dropzone-file');
        const selectedFilesList = document.getElementById('selectedFilesList');
        const fileListPreviewContainer = document.getElementById('fileListPreviewContainer');
        const startAnalysisBtn = document.getElementById('startAnalysisBtn');
        const bgVideo = document.getElementById('bgVideo');
        const videoOverlay = document.getElementById('videoOverlay');
        const simulateCallbackBtn = document.getElementById('simulateCallbackBtn');
        const cpfInput = document.getElementById('cpfInput');
        const cpfError = document.getElementById('cpfError');

        let pollingIntervalId = null;
        let lastKnownTimestamp = 0;
        let currentCpf = null; // Store CPF for polling status files
        let isDisplayingFrases = false;
        const fraseQueue = [];
        let initialMessagesLooping = false;
        let initialMessageTimeout;

        function appendLogMessage(message, isError = false) {
            if (!htmlResultContent) { console.error("DOM: htmlResultContent not found for log."); return; }
            if (message) {
                const logEntry = document.createElement('p');
                logEntry.className = 'log-line'; // Base class

                let logClass = '';
                const trimmedMessage = message.trim();
                const firstChar = trimmedMessage.charAt(0);
                // Unicode property escapes for emoji detection
                const startsWithEmoji = /^\p{Emoji}/u.test(trimmedMessage);

                if (isError) {
                    logClass = 'log-error';
                } else {
                    switch (firstChar) {
                        case '»':
                            logClass = 'log-start';
                            break;
                        case '✓':
                            logClass = 'log-success';
                            break;
                        case '⇅':
                            logClass = 'log-action';
                            break;
                        case '!':
                            logClass = 'log-warning';
                            break;
                        case 'ℹ':
                            logClass = 'log-info';
                            break;
                        default:
                            if (startsWithEmoji) {
                                logClass = 'log-action'; // Default color for other emojis
                            }
                            break;
                    }
                }

                if (logClass) {
                    logEntry.classList.add(logClass);
                }

                logEntry.textContent = message;
                htmlResultContent.appendChild(logEntry);

                // Scroll to the bottom to show the newest log
                resultsArea.scrollTop = resultsArea.scrollHeight;

                // ** Log Rotation Logic **
                // If the content is overflowing, remove old logs from the top until it fits.
                // A timeout of 0 allows the DOM to render the new log entry before we check the height.
                setTimeout(() => {
                    while (resultsArea.scrollHeight > resultsArea.clientHeight) {
                        if (htmlResultContent.firstChild) {
                            htmlResultContent.removeChild(htmlResultContent.firstChild);
                        } else {
                            break; // Safety break
                        }
                    }
                }, 0);
            }
        }

        function changeVideoSourceUI(videoPath) {
            if (!bgVideo) { console.error("DOM: bgVideo not found."); return; }
            if (!videoPath) { console.warn("Video: No path provided."); return; }

            let sourceElement = bgVideo.querySelector('source');
            if (!sourceElement) {
                sourceElement = document.createElement('source');
                bgVideo.appendChild(sourceElement);
            }
            const currentVideoFile = bgVideo.currentSrc.substring(bgVideo.currentSrc.lastIndexOf('/') + 1);
            const newVideoFile = videoPath.substring(videoPath.lastIndexOf('/') + 1);

            if (newVideoFile && (!bgVideo.currentSrc || currentVideoFile !== newVideoFile)) {
                console.log(`Video: Changing from ${currentVideoFile || 'none'} to ${newVideoFile}`);
                sourceElement.setAttribute('src', videoPath);
                sourceElement.setAttribute('type', 'video/mp4');
                bgVideo.load();
                const playPromise = bgVideo.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => console.error("Video: play() failed:", error));
                }
            } else if (bgVideo.paused && bgVideo.currentSrc && currentVideoFile === newVideoFile) {
                console.log(`Video: ${newVideoFile} is current, ensuring play if paused.`);
                const playPromise = bgVideo.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => console.error("Video: play() (resume) failed:", error));
                }
            }
        }

        function adaptVideoPathForDevice(videoPathFromCallback) {
            if (!videoPathFromCallback) return '';
            const isMobileDevice = window.matchMedia("(max-width: 768px) and (orientation: portrait)").matches ||
                                 (window.matchMedia("(orientation: landscape)").matches && window.innerWidth < 900 && window.innerHeight < 600);
            let adaptedPath = videoPathFromCallback;
            if (isMobileDevice) {
                if (!videoPathFromCallback.includes('_mobile.mp4')) {
                    adaptedPath = videoPathFromCallback.replace('_desktop.mp4', '_mobile.mp4');
                }
            } else {
                if (!videoPathFromCallback.includes('_desktop.mp4')) {
                    adaptedPath = videoPathFromCallback.replace('_mobile.mp4', '_desktop.mp4');
                }
            }
            return adaptedPath;
        }

        function startPollingStatus() {
            stopPollingStatus();
            console.log("Polling: Starting (3s interval)...");
            lastKnownTimestamp = 0;
            fetchAndUpdateStatus();
            pollingIntervalId = setInterval(fetchAndUpdateStatus, 3000);
        }

        function stopPollingStatus() {
            if (pollingIntervalId) {
                clearInterval(pollingIntervalId);
                pollingIntervalId = null;
                console.log("Polling: Stopped.");
            }
        }

        async function displayFrasesWithDelay() {
            if (isDisplayingFrases) return;
            isDisplayingFrases = true;

            while (fraseQueue.length > 0) {
                const frase = fraseQueue.shift();
                appendLogMessage(frase);
                await new Promise(resolve => setTimeout(resolve, 1500));
            }

            isDisplayingFrases = false;
        }

        function fetchAndUpdateStatus() {
            let url = 'get_latest_status.php?r=' + Date.now();
            if (currentCpf) url += '&cpf=' + encodeURIComponent(currentCpf);
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        response.text().then(text => console.error(`Polling HTTP Error! Status: ${response.status}, Body: ${text}`));
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) { console.warn('Polling: No data from get_latest_status.php.'); return; }
                    console.log('Polling: Status Received:', JSON.stringify(data));

                    const isRealLogStarting = (data.status === 'frases_received' && data.frases.length > 0) || data.status === 'result_ready' || data.status === 'error';

                    if (initialMessagesLooping) {
                        if (isRealLogStarting) {
                            initialMessagesLooping = false;
                            clearTimeout(initialMessageTimeout);
                            htmlResultContent.innerHTML = '';
                        } else {
                            return; // Ignore intermediate status updates
                        }
                    }

                    const newTimestamp = Number(data.timestamp);
                    if (isNaN(newTimestamp)) {
                        console.warn('Polling: Invalid timestamp received:', data.timestamp);
                    } else {
                        if (newTimestamp <= lastKnownTimestamp) { return; }
                        lastKnownTimestamp = newTimestamp;
                        console.log(`Polling: Updated lastKnownTimestamp: ${lastKnownTimestamp}`);
                    }

                    if (data.message) appendLogMessage(data.message);
                    if (data.video) changeVideoSourceUI(adaptVideoPathForDevice(data.video));

                    if (data.status === 'frases_received' && Array.isArray(data.frases)) {
                        fraseQueue.push(...data.frases);
                        displayFrasesWithDelay();
                    } else if (data.status === 'result_ready' && data.link) {
                        console.log('Polling: Result is ready. Redirecting to:', data.link);
                        stopPollingStatus();
                        appendLogMessage('✓ Análise finalizada. Redirecionando para o seu parecer...');
                        // Use a short delay to allow the user to read the final message
                        setTimeout(() => {
                            window.location.href = data.link;
                        }, 1500);
                    } else if (data.status === 'error' || data.status === 'failed' || data.status === 'error_reading_status') {
                        console.log(`Polling: Error/Failed status from server: ${data.status}.`);
                        stopPollingStatus();
                        appendLogMessage(`Erro no processamento: ${data.message || 'Falha desconhecida.'}`, true);
                    }
                })
                .catch(error => {
                    console.error('Polling: Fetch/JSON Error:', error);
                    appendLogMessage(`Falha crítica ao buscar atualização de status. Verifique o console.`, true);
                });
        }

        function initializeBackgroundVideo() {
            let initialVideoSrc = adaptVideoPathForDevice('BackgroundVideos/desktop.mp4');
            changeVideoSourceUI(initialVideoSrc);
        }

        initializeBackgroundVideo();

        function setVideoOverlayOpacity(opacity) {
            if(videoOverlay) videoOverlay.style.backgroundColor = `rgba(0, 0, 0, ${opacity})`;
            else console.error("DOM Error: videoOverlay element not found.");
        }

        if(dropzoneFileInput) {
            dropzoneFileInput.addEventListener('change', function(event) {
                if (!selectedFilesList) { console.error("DOM: selectedFilesList not found in 'change'."); return; }
                selectedFilesList.innerHTML = '';
                if (fileListPreviewContainer) {
                    if (event.target.files.length > 0) {
                        fileListPreviewContainer.classList.remove('hidden');
                        for (const file of event.target.files) {
                            const listItem = document.createElement('li');
                            listItem.textContent = file.name;
                            selectedFilesList.appendChild(listItem);
                        }
                    } else {
                        fileListPreviewContainer.classList.add('hidden');
                    }
                } else { console.warn("DOM: fileListPreviewContainer not found in 'change'.");}
            });
        } else { console.error("DOM Error: dropzoneFileInput not found."); }

        if (fileListPreviewContainer && dropzoneFileInput && dropzoneFileInput.files.length === 0) {
            fileListPreviewContainer.classList.add('hidden');
        }

        if(startAnalysisBtn) {
            startAnalysisBtn.addEventListener('click', function() {
                if(!cpfInput || !cpfError) { console.error("DOM: CPF elements not found."); alert("Erro na página (CPF)."); return;}
                if(!dropzoneFileInput) { console.error("DOM: dropzoneFileInput not found."); alert("Erro na página."); return; }
                if(!appArea) { console.error("DOM: appArea not found."); alert("Erro na página."); return; }
                if(!resultsArea) { console.error("DOM: resultsArea not found."); alert("Erro na página."); return; }
                if(!htmlResultContent) { console.error("DOM: htmlResultContent not found."); alert("Erro na página."); return; }

                const cpfValue = cpfInput.value.trim();
                const cleanedCpf = cpfValue.replace(/[.\-\/]/g, ''); // Remove '.', '-', '/'
                currentCpf = cleanedCpf; // Save CPF for status polling

                cpfError.classList.add('hidden');
                cpfError.textContent = '';

                if (cleanedCpf === '') {
                    cpfError.textContent = 'CPF é obrigatório.';
                    cpfError.classList.remove('hidden');
                    cpfInput.focus();
                    return;
                }

                if (!/^\d{11}$/.test(cleanedCpf)) {
                    cpfError.textContent = 'CPF inválido. Deve conter 11 dígitos numéricos.';
                    cpfError.classList.remove('hidden');
                    cpfInput.focus();
                    return;
                }

                if (dropzoneFileInput.files.length === 0) {
                    alert('Por favor, selecione um ou mais arquivos para análise.');
                    return;
                }

                appArea.classList.add('hidden');
                resultsArea.classList.remove('hidden');
                if(exportButtonsContainer) exportButtonsContainer.classList.add('hidden');
                htmlResultContent.innerHTML = '';

                const initialMessages = [
                    "» Recebendo seus documentos... 🐾 já estou afiando minhas garras de analista previdenciário!",
                    "⇅ Olhando para o volume de arquivos... calculo que vou levar uns 7 minutinhos para processar tudo com carinho e precisão.",
                    "ℹ Se quiser, pode ficar aqui me acompanhando... é sempre divertido ver um dog nerd em ação!",
                    "⇅ Mas, se precisar sair, sem problema... já deixo combinado que te mando um alô no WhatsApp quando tudo estiver pronto 📱",
                    "✓ Pronto, combinado fechado! Agora vamos ligar as turbinas e começar essa maratona previdenciária.",
                    "⇅ Validando formatos dos arquivos recebidos para garantir compatibilidade com meu sistema.",
                    "✓ Todos os arquivos estão nos formatos aceitos (PDF, JPG, PNG)! 📂",
                    "⇅ Iniciando organização dos documentos por tipo e data de envio.",
                    "⇅ Convertendo PDFs em imagens para garantir leitura mais precisa pelo OCR.",
                    "⇅ Ajustando resolução das imagens para alcançar máxima qualidade de reconhecimento.",
                    "✓ Conversão concluída! Todas as páginas prontas para leitura detalhada.",
                    "⇅ Limpando bordas e corrigindo inclinações nas imagens capturadas.",
                    "⇅ Preparando diretório temporário para esta sessão de análise.",
                    "⇅ Criando identificador único para rastrear este processo do início ao fim.",
                    "✓ Identificador gerado com sucesso. 🔑",
                    "⇅ Compactando todos os arquivos para envio seguro ao servidor.",
                    "⇅ Verificando integridade do pacote antes do disparo.",
                    "✓ Pacote validado! Nenhum erro encontrado na compressão.",
                    "⇅ Enviando arquivos para o servidor Macohin de Inteligência Artificial... 🚀",
                    "⇅ Estabelecendo conexão segura com o data center na Flórida.",
                    "✓ Conexão estabelecida com sucesso. 🔒",
                    "⇅ Transferindo dados criptografados para processamento.",
                    "⇅ Aguardando confirmação de recebimento do servidor remoto.",
                    "✓ Servidor confirmou o recebimento dos arquivos! 📡",
                    "⇅ Acionando módulo Bubba A.I. para iniciar a análise previdenciária.",
                    "» Olá! Eu sou o Bubba, seu dog nerd previdenciário, e já estou no comando. 🐶",
                    "⇅ Carregando bibliotecas especializadas de leitura previdenciária.",
                    "⇅ Iniciando rotina de reconhecimento de texto (OCR) nas imagens recebidas.",
                    "✓ OCR ativado e pronto para decifrar cada detalhe dos seus documentos.",
                    "⇅ Extraindo texto das páginas para análise semântica.",
                    "⇅ Aplicando filtros de correção em palavras e números detectados.",
                    "✓ Extração de texto concluída com alta precisão! 📖",
                    "⇅ Iniciando varredura para identificar documentos CNIS, CTPS, PPP e GPS.",
                    "⇅ Catalogando cada documento conforme tipo e origem.",
                    "✓ Catalogação finalizada. Tudo organizado para o próximo passo.",
                    "⇅ Preparando ambiente de análise cruzada entre documentos.",
                    "⇅ Carregando modelos de IA treinados para detecção de vínculos e lacunas.",
                    "✓ Modelos carregados com sucesso. 🧠",
                    "⇅ Enviando dados para pré-processamento e limpeza de inconsistências.",
                    "⇅ Rodando algoritmos de detecção de datas e períodos contributivos.",
                    "✓ Pré-processamento concluído sem falhas.",
                    "⇅ Validando legibilidade e consistência das informações extraídas.",
                    "⇅ Ajustando caracteres e formatação para manter integridade dos dados.",
                    "✓ Dados preparados para análise detalhada!",
                    "⇅ Iniciando cálculo preliminar de tempo e carência para conferência futura.",
                    "⇅ Preparando logs técnicos para auditoria interna.",
                    "✓ Logs técnicos ativados. Tudo sendo registrado.",
                    "⇅ Conectando com módulos de simulação previdenciária.",
                    "⇅ Testando comunicação com os agentes internos do Bubba A.I.",
                    "✓ Todos os agentes internos respondendo corretamente. ✅",
                    "⇅ Liberando pipeline de execução para as próximas etapas.",
                    "⇅ Garantindo redundância e backups para evitar perda de dados.",
                    "✓ Backup inicial concluído com sucesso.",
                    "⇅ Sincronizando informações com o painel de controle do Bubba.",
                    "⇅ Atualizando status da análise no sistema central.",
                    "✓ Status sincronizado com o backend.",
                    "⇅ Preparando índice de navegação para facilitar acesso aos dados.",
                    "⇅ Ordenando documentos na sequência lógica da análise.",
                    "✓ Ordenação finalizada e pronta para uso.",
                    "⇅ Disparando gatilho para ativação do motor de análise principal.",
                    "⇅ Executando diagnósticos finais antes de prosseguir.",
                    "✓ Diagnóstico aprovado! Sistema pronto para trabalhar.",
                    "⇅ Acionando subsistema de extração de indicadores previdenciários.",
                    "⇅ Checando se há documentos duplicados ou ilegíveis.",
                    "✓ Nenhuma duplicata ou falha detectada.",
                    "⇅ Enfileirando tarefas para execução paralela.",
                    "⇅ Configurando prioridade para documentos críticos.",
                    "✓ Configuração de prioridade concluída.",
                    "⇅ Estabelecendo parâmetros de análise para este cliente.",
                    "⇅ Aplicando políticas específicas conforme tipo de benefício investigado.",
                    "✓ Políticas aplicadas com sucesso.",
                    "⇅ Abrindo canal de monitoramento em tempo real.",
                    "⇅ Registrando início oficial da análise no log mestre.",
                    "✓ Registro efetuado no log mestre.",
                    "⇅ Carregando contexto de regras previdenciárias vigentes.",
                    "⇅ Injetando pacotes de conhecimento especializado no motor de decisão.",
                    "✓ Conhecimento carregado com êxito.",
                    "⇅ Ajustando tolerância de erro para garantir alta precisão.",
                    "⇅ Preparando cálculos preliminares de projeção.",
                    "✓ Projeções iniciais geradas e aguardando refinamento.",
                    "⇅ Fazendo última checagem antes da execução em larga escala.",
                    "⇅ Validando conectividade com serviços auxiliares.",
                    "✓ Todos os serviços auxiliares online.",
                    "⇅ Ligando os motores principais do Bubba A.I. para iniciar a inteligência.",
                    "⇅ Sincronizando fuso horário para padronização de datas.",
                    "✓ Fuso horário sincronizado.",
                    "⇅ Ativando modo narrador para acompanhamento passo a passo.",
                    "⇅ Pronto para começar a interpretação profunda dos dados recebidos.",
                    "✓ Ambiente completamente configurado para análise.",
                    "⇅ Fazendo último salvamento automático antes de mergulhar na análise.",
                    "✓ Salvamento concluído. Agora é comigo! 🐾",
                    "⇅ Dando play no motor de raciocínio previdenciário.",
                    "⇅ Criando checkpoints para permitir retomada em caso de falha.",
                    "✓ Checkpoints criados com sucesso.",
                    "⇅ Carregando sequências de análise pré-definidas.",
                    "⇅ Confirmando que todos os módulos estão atualizados.",
                    "✓ Versões atualizadas confirmadas.",
                    "⇅ Disparando inicialização do Agente Narrador para registro interativo.",
                    "⇅ Encaminhando dados iniciais para pré-leitura detalhada.",
                    "✓ Pré-leitura iniciada. Bubba no comando!",
                    "⇅ Respire fundo... o show previdenciário vai começar. 🐶"
                ];

                let messageIndex = 0;
                initialMessagesLooping = true;

                function displayNextInitialMessage() {
                    if (!initialMessagesLooping || messageIndex >= initialMessages.length) {
                        initialMessagesLooping = false;
                        return;
                    }
                    appendLogMessage(initialMessages[messageIndex]);
                    messageIndex++;
                    initialMessageTimeout = setTimeout(displayNextInitialMessage, 2000); // New 2000ms delay
                }

                setVideoOverlayOpacity(0.2);

                const formData = new FormData();
                formData.append('cpf', cleanedCpf); // Add cleaned CPF
                for (const file of dropzoneFileInput.files) { formData.append('files[]', file); }

                fetch('upload.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    console.log('Upload Response:', data);
                    if(data.success){
                        if(dropzoneFileInput) dropzoneFileInput.value = '';
                        // Start the fake logs and the real polling concurrently
                        displayNextInitialMessage();
                        startPollingStatus();
                    } else {
                        appendLogMessage(`Falha ao enviar arquivos: ${data.message || 'Erro desconhecido.'}`, true);
                        appArea.classList.remove('hidden');
                        resultsArea.classList.add('hidden');
                    }
                })
                .catch((error) => {
                    initialMessagesLooping = false; // Stop loop on error
                    clearTimeout(initialMessageTimeout);
                    console.error('Upload Fetch Error:', error);
                    appendLogMessage(`Erro crítico no envio: ${error.message || 'Não foi possível contactar o servidor.'}`, true);
                    appArea.classList.remove('hidden');
                    resultsArea.classList.add('hidden');
                });
            });
        } else { console.error("DOM Error: startAnalysisBtn not found."); }

        function displayFinalResult(htmlParam, videoNameParam) {
            console.log("Displaying Final Result. Video:", videoNameParam);
            const happyVideoPath = videoNameParam || adaptVideoPathForDevice('BackgroundVideos/happy_desktop.mp4');

            changeVideoSourceUI(happyVideoPath);
            setVideoOverlayOpacity(0.8);

            if(appArea && !appArea.classList.contains('hidden')) appArea.classList.add('hidden');
            if(simulateCallbackBtn && !simulateCallbackBtn.classList.contains('hidden')) simulateCallbackBtn.classList.add('hidden');

            if(htmlResultContent) htmlResultContent.innerHTML = htmlParam;
            else console.error("DOM Error: htmlResultContent not found.");

            if(resultsArea && resultsArea.classList.contains('hidden')) resultsArea.classList.remove('hidden');
            else if (!resultsArea) console.error("DOM Error: resultsArea not found.");

            if(exportButtonsContainer) {
                exportButtonsContainer.classList.remove('hidden');
                exportButtonsContainer.classList.add('flex');
            } else { console.error("DOM Error: exportButtonsContainer not found.");}

            if(resultsArea) resultsArea.scrollTop = 0;
            const mainContainer = document.getElementById('mainContentContainer') || document.documentElement;
            mainContainer.scrollTop = 0;
        }

        if(simulateCallbackBtn) {
            simulateCallbackBtn.addEventListener('click', () => {
                const sampleHtml = `
                    <h2 class='text-2xl font-bold mb-4 text-center'>Análise Previdenciária Concluída!</h2>
                    <p class='mb-2'><strong>Prezado(a) Segurado(a),</strong></p>
                    <p class='mb-2'>Após uma análise detalhada dos documentos fornecidos (CNIS, CTPS), apresentamos os seguintes pontos relevantes para sua aposentadoria:</p>
                    <ul class='list-disc list-inside mb-4 space-y-1'>
                        <li>Tempo de contribuição total verificado: <strong>XX anos, YY meses e ZZ dias.</strong></li>
                        <li>Possíveis pendências identificadas no CNIS: Sim (detalhar se houver).</li>
                        <li>Direito à aposentadoria por idade: Verificado/Não verificado.</li>
                        <li>Direito à aposentadoria por tempo de contribuição: Verificado/Não verificado.</li>
                        <li>Valor estimado do benefício (RMI): R$ X.XXX,XX (simulação).</li>
                    </ul>
                    <p class='mb-2'><strong>Recomendações:</strong></p>
                    <p class='mb-4'>Recomendamos agendar uma consulta para discutir as estratégias e próximos passos.</p>
                    <hr class='my-4 border-gray-500'>
                    <p class='text-sm text-gray-400'>Este é um relatório preliminar gerado por Bubba A.I. e não substitui a consulta com um profissional especializado.</p>
                `;
                const happyVideo = adaptVideoPathForDevice('BackgroundVideos/happy_desktop.mp4');
                if(resultsArea && resultsArea.classList.contains('hidden')) {
                    resultsArea.classList.remove('hidden');
                }
                if(appArea && !appArea.classList.contains('hidden')) {
                     appArea.classList.add('hidden');
                }
                if(htmlResultContent) htmlResultContent.innerHTML = '';
                displayFinalResult(sampleHtml, happyVideo);
            });
        } else { console.error("DOM Error: simulateCallbackBtn not found."); }

        if(exportPdfBtn) {
            exportPdfBtn.addEventListener('click', () => {
                if (typeof html2pdf === 'undefined') { alert('Erro: Biblioteca html2pdf não carregada.'); return; }
                appendLogMessage('Gerando PDF...', false);
                if (!htmlResultContent) { console.error("htmlResultContent not found for PDF export"); return; }
                const element = htmlResultContent;
                const opt = { margin: [0.5,0.5,0.5,0.5], filename: 'Analise_Previdenciaria_BubbaAI.pdf', image: { type: 'jpeg', quality: 0.95 }, html2canvas:  { scale: 2, logging: false, useCORS: true }, jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }};
                html2pdf().from(element).set(opt).save()
                    .then(() => { appendLogMessage('PDF gerado com sucesso!', false); })
                    .catch(err => { appendLogMessage('Erro ao gerar PDF.', true); console.error("Error generating PDF:", err); });
            });
        } else { console.error("DOM Error: exportPdfBtn not found."); }

        if(exportDocxBtn) {
            exportDocxBtn.addEventListener('click', () => {
                if (typeof htmlDocx === 'undefined' || typeof saveAs === 'undefined') { alert('Erro: Bibliotecas de export DOCX não carregadas.'); return; }
                try {
                    appendLogMessage('Gerando DOCX...', false);
                    if (!htmlResultContent) { console.error("htmlResultContent not found for DOCX export"); return; }
                    const contentToConvert = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Análise Previdenciária</title></head><body>${htmlResultContent.innerHTML}</body></html>`;
                    var converted = htmlDocx.asBlob(contentToConvert);
                    saveAs(converted, 'Analise_Previdenciaria_BubbaAI.docx');
                    appendLogMessage('DOCX gerado com sucesso!', false);
                } catch(err) {
                    appendLogMessage('Erro ao gerar DOCX.', true);
                    console.error("Error generating DOCX:", err);
                }
            });
        } else { console.error("DOM Error: exportDocxBtn not found."); }
    </script>
  <script>
    // Matrix rain — visível e leve
    (function matrix(){
      const canvas = document.getElementById('rain');
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      const ctx = canvas.getContext('2d');
      const chars = '01░▓█<>[]{}/*-=+^$#@_';
      let w,h,cols,ypos,step;

      function resize(){
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
        step = Math.max(16, Math.min(24, Math.floor(w/52)));
        cols = Math.floor(w/step);
        ypos = Array(cols).fill(0);
        ctx.font = step + 'px ' + getComputedStyle(document.body).fontFamily;
      }
      window.addEventListener('resize', resize, {passive:true});
      resize();

      function draw(){
        ctx.fillStyle = 'rgba(0,0,0,0.10)';
        ctx.fillRect(0,0,w,h);

        ctx.fillStyle = '#58a6ff';
        const ix=(Math.random()*cols)|0;
        ctx.fillText(chars[(Math.random()*chars.length)|0], ix*step, ypos[ix]);

        ctx.fillStyle = '#37e3c3';
        for(let i=0;i<cols;i++){
          const x=i*step;
          ctx.fillText(chars[(Math.random()*chars.length)|0], x, ypos[i]);
          ypos[i] = (ypos[i] > 100 + Math.random()*h) ? 0 : (ypos[i] + step);
        }
        requestAnimationFrame(draw);
      }
      draw();
    })();
  </script>
</body>
</html>
