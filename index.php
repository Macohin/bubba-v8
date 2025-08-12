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
<style>
  :root{
    --bg:#18092d; --bg2:#1f0b3f;
    --txt:#e8e8f0; --muted:#b7b7c7;
    --c1:#37e3c3; --c2:#58a6ff; --c3:#c7e6ff;
    --grid:rgba(255,255,255,.045);
    --card:#201041; --card2:#25114d;
    --mono: ui-monospace, SFMono-Regular, Menlo, Monaco, "Courier New", monospace;
    --shadow: 0 0 18px rgba(88,166,255,.28), 0 0 36px rgba(55,227,195,.18);
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0; color:var(--txt); font-family:var(--mono);
    background:
      radial-gradient(1200px 700px at 70% -10%, #2a145f22, transparent),
      radial-gradient(900px 600px at -10% 30%, #4f1b8a22, transparent),
      var(--bg);
    overflow-x:hidden; display:flex; flex-direction:column; min-height:100svh;
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
    z-index: -2;
    opacity: 0.2;
    object-fit: cover;
  }

  /* matrix + grid (sutil) */
  #rain{position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.12}
  .grid{position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.26;
    background:
      linear-gradient(transparent 31px, var(--grid) 32px),
      linear-gradient(90deg, transparent 31px, var(--grid) 32px);
    background-size:32px 32px;
    mask-image: radial-gradient(1100px 520px at 50% -10%, #000 35%, transparent 80%);
  }

  /* header CENTRALIZADO com badges abaixo */
  header{
    position:relative; z-index:2;
    display:flex; flex-direction:column; align-items:center; gap:10px;
    padding:16px;
  }
  .brand{
    display:flex; align-items:center; gap:10px;
    padding:6px 12px; border:1px solid rgba(255,255,255,.12); border-radius:999px;
    background:linear-gradient(180deg,#ffffff10,#0000); box-shadow:var(--shadow);
  }
  .mark{width:26px;height:26px;border-radius:8px;display:grid;place-items:center;
    background:linear-gradient(135deg,var(--c1),var(--c2))}
  .mark svg{width:16px;height:16px}
  .name{font-weight:800; letter-spacing:.5px}
  .sub{font-size:11px; color:var(--muted)}
  .badges{display:flex; gap:8px; flex-wrap:wrap; justify-content:center}
  .badge{
    padding:6px 10px; border-radius:999px; border:1px solid rgba(255,255,255,.18);
    font-size:11px; font-weight:800; letter-spacing:.4px;
  }
  .badge.primary{color:#0b1120; background:linear-gradient(180deg,#9dffce,#37e3c3)}
  .badge.alt{color:#fff; background:linear-gradient(180deg,#00c6ff,#0072ff)}
  .badge.ghost{color:#d4d7ff; background:#ffffff12; backdrop-filter:blur(6px)}

  /* container central */
  .container{position:relative; z-index:2; width:100%; max-width:1120px; margin:0 auto;
    padding: clamp(16px, 3vw, 32px); display:flex; flex-direction:column; align-items:center;}

  /* HERO central */
  .hero{display:flex; flex-direction:column; align-items:center; text-align:center;
    margin-top: clamp(6px, 3vh, 16px);}
  /* ASCII mais nítido (menos “bugado”): */
  .ascii{
    white-space:pre; user-select:none; margin:0 auto; max-width:100%;
    line-height:1.02; letter-spacing:0; /* apertado = menos serrilhado */
    font-size: clamp(10px, 2vw, 18px);
    color: transparent;
    background-image: radial-gradient(120% 120% at 50% 20%, var(--c3), #ffffff 45%, #dff7ff 70%);
    -webkit-background-clip: text; background-clip: text;
    text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
    filter: drop-shadow(0 0 6px #a3d4ff) drop-shadow(0 0 14px #78ffe6);
  }
  .tagline{
    margin-top:10px; font-size:clamp(12px,1.2vw,14px); letter-spacing:1px;
    color:#e9fefb; text-shadow:0 0 10px #37e3c333, 0 0 20px #58a6ff33;
  }

  /* card “purpose” centralizado */
  .section{width:100%; display:grid; place-items:center; margin-top: clamp(18px, 4vh, 32px);}
  .card{
    width:min(900px, 95%); background:linear-gradient(180deg, rgba(32, 16, 65, 0.85), rgba(37, 17, 77, 0.85));
    border:2px dotted rgba(255,255,255,.15); border-radius:18px;
    box-shadow:0 12px 60px rgba(0,0,0,.35), var(--shadow); overflow:hidden;
  }
  .chrome{display:flex; align-items:center; gap:8px; padding:10px 12px;
    border-bottom:1px dashed rgba(255,255,255,.12);
    background:linear-gradient(0deg,#ffffff10,#0000); font-size:12px; color:var(--muted)}
  .dot{width:10px;height:10px;border-radius:50%}
  .d1{background:#ff5f56}.d2{background:#ffbd2e}.d3{background:#27c93f}
  .card-body{padding:18px; font-size:clamp(13px,1.6vw,15px); line-height:1.55; color:#eaf6ff}
  .divider{margin:12px 0; border-top:1px dashed rgba(255,255,255,.16)}
  .comment{color:#9fb1ff; opacity:.9}
  .chips{display:flex; flex-wrap:wrap; gap:8px; margin-top:14px}
  .chip{border:1px solid rgba(255,255,255,.15); padding:6px 10px; border-radius:10px;
    background:linear-gradient(180deg,#ffffff12,#00000010)}
  .cta{display:flex; gap:10px; flex-wrap:wrap; margin-top:16px}
  .btn{ text-decoration:none; font-weight:800; letter-spacing:.3px; padding:10px 14px;
    border-radius:10px; border:1px solid rgba(255,255,255,.15); display:inline-block}
  .btn.primary{color:#091017; background:linear-gradient(180deg,#9dffce,#37e3c3)}
  .btn.alt{color:#fff; background:linear-gradient(180deg,#00c6ff,#0072ff)}

  /* use cases */
  .examples{width:100%; max-width:1120px; margin-top: clamp(16px, 5vh, 40px);
    display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:18px}
  .ex{background:linear-gradient(180deg,var(--card),var(--card2)); border:1px dashed rgba(255,255,255,.15);
    border-radius:16px; padding:16px; box-shadow:var(--shadow)}
  .ex h4{margin:0 0 8px 0; font-size:clamp(13px,1.4vw,14px); letter-spacing:.4px; color:#cfe3ff}
  .ex p{margin:0; font-size:clamp(12px,1.2vw,13px); color:#eaf6ff; line-height:1.55}
  .ex .meta{margin-top:10px; font-size:12px; color:#b7c7ff; opacity:.85}
  @media (max-width:900px){ .examples{grid-template-columns:1fr} }

  /* footer */
  footer{margin-top:auto; position:relative; z-index:2; text-align:center; color:var(--muted);
    font-size:12px; opacity:.8; padding:22px 12px 28px}

  @media (prefers-reduced-motion: reduce){ #rain{display:none!important} }

  /* App-specific styles */
  .card-body .space-y-6 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1.5rem;
  }
  .card-body label {
    color: var(--muted);
    font-size: 13px;
    letter-spacing: .5px;
    margin-bottom: .5rem;
    display: block;
  }
  .card-body input[type="text"] {
    background: var(--bg);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 8px;
    padding: 10px 12px;
    color: var(--txt);
    width: 100%;
    font-family: var(--mono);
  }
  .card-body input[type="text"]::placeholder {
    color: var(--muted);
    opacity: 0.5;
  }
  .card-body #cpfError {
    color: #ff5f56; /* from .d1 */
    font-size: 12px;
    margin-top: .5rem;
  }
  .card-body #uploadPromptText {
    text-align: center;
    font-size: 1.125rem;
    margin-bottom: 1rem;
  }
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
  .card-body #dropzoneContainer svg {
    color: var(--muted);
    width: 2rem;
    height: 2rem;
    margin-bottom: 1rem;
  }
  .card-body #dropzoneContainer p {
    color: var(--muted);
    font-size: .875rem;
  }
  .card-body #fileListPreviewContainer {
    margin-top: 1rem;
  }
  .card-body #selectedFilesList {
    list-style-type: disc;
    list-style-position: inside;
  }
  .card-body #selectedFilesList li {
    color: var(--muted);
    font-size: 13px;
  }
  #resultsArea {
    border-top: 1px dashed rgba(255,255,255,.16);
    padding-top: 18px;
    margin-top: 18px;
  }
  #htmlResultContent .log-entry-new {
    color: var(--c2);
    animation: fadeInAndPulse 1.5s ease-out;
  }
  #htmlResultContent .text-red-400 {
      color: #ff5f56 !important; /* from .d1 */
  }
  #exportButtonsContainer {
    display: flex;
    gap: 12px;
    margin-top: 16px;
    flex-direction: column;
  }
  @media (min-width: 640px) {
    #exportButtonsContainer {
      flex-direction: row;
    }
  }

  /* Hide old video background elements */
  #bgVideo, #videoOverlay {
      display: none !important;
  }
  #htmlResultContent {
      font-size:clamp(13px,1.6vw,15px);
      line-height:1.55;
      color:#eaf6ff;
  }
  #htmlResultContent h2 {
      font-size: 1.5em;
      font-weight: 800;
      color: var(--c1);
      margin-bottom: 1em;
  }
  #htmlResultContent p {
      margin-bottom: 1em;
  }
  #htmlResultContent strong {
      color: var(--c1);
      font-weight: 800;
  }
  #htmlResultContent ul {
      list-style-type: disc;
      padding-left: 20px;
      margin-bottom: 1em;
  }
  #htmlResultContent hr {
      border-top: 1px dashed rgba(255,255,255,.16);
      margin: 1.5em 0;
  }
  .prose-invert {
      color: var(--txt);
  }
  #htmlResultContent, #htmlResultContent * {
    font-family: var(--mono) !important;
  }
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
███╗   ███╗ █████╗  ██████╗ ██████╗ ██╗  ██╗██╗███╗   ██╗
████╗ ████║██╔══██╗██╔════╝██╔═══██╗██║  ██║██║████╗  ██║
██╔████╔██║███████║██║     ██║   ██║███████║██║██╔██╗ ██║
██║╚██╔╝██║██╔══██║██║     ██║   ██║██╔══██║██║██║╚██╗██║
██║ ╚═╝ ██║██║  ██║╚██████╗╚██████╔╝██║  ██║██║██║ ╚████║
╚═╝     ╚═╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝ ╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝
</pre>
      <div class="tagline">// Asynchronous AI Agents — secure, scalable, compliant</div>
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
                <div id="exportButtonsContainer" class="hidden">
                    <button id="exportPdfBtn" class="btn primary">
                        <svg xmlns="http://www.w3.org/2000/svg" style="display: inline-block; width: 1.25rem; height: 1.25rem; margin-right: .5rem; margin-top: -0.25rem;" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0111.293 2.707L14.586 6H16a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8H4a2 2 0 01-2-2V4zm2 0v4h5.586L8.293 4.707A1 1 0 007.586 4H6zm5 4h2.586L11 5.414V8zM8 10a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm1 3a1 1 0 100 2h2a1 1 0 100-2H9z" clip-rule="evenodd" /></svg>
                        Exportar para PDF
                    </button>
                    <button id="exportDocxBtn" class="btn alt">
                        <svg xmlns="http://www.w3.org/2000/svg" style="display: inline-block; width: 1.25rem; height: 1.25rem; margin-right: .5rem; margin-top: -0.25rem;" viewBox="0 0 20 20" fill="currentColor"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" /><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v4m0 0l-2-2m2 2l2-2" /></svg>
                        Exportar para DOCX
                    </button>
                </div>
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

        function appendLogMessage(message, isError = false) {
            if (!htmlResultContent) { console.error("DOM: htmlResultContent not found for log."); return; }
            if (message) {
                const logEntry = document.createElement('p');
                logEntry.className = isError ? 'text-red-400 mb-2' : 'text-sky-300 mb-2 log-entry-new';
                logEntry.textContent = message;
                htmlResultContent.appendChild(logEntry);
                if (resultsArea) resultsArea.scrollTop = resultsArea.scrollHeight;
                if (!isError) {
                    setTimeout(() => logEntry.classList.remove('log-entry-new'), 1500);
                }
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
                    }

                    if (data.status === 'result_ready') {
                        console.log('Polling: Result is ready.');
                        stopPollingStatus();
                        if (data.html_content) {
                            const finalVideoPath = data.video ? adaptVideoPathForDevice(data.video) : adaptVideoPathForDevice('BackgroundVideos/happy_desktop.mp4');
                            displayFinalResult(data.html_content, finalVideoPath);
                        } else {
                            console.error('Polling: Result ready, but no html_content.');
                            appendLogMessage('Análise concluída, mas nenhum conteúdo HTML recebido.', true);
                        }
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
                appendLogMessage('Enviando arquivos e iniciando análise...');
                setVideoOverlayOpacity(0.2);

                const formData = new FormData();
                formData.append('cpf', cleanedCpf); // Add cleaned CPF
                for (const file of dropzoneFileInput.files) { formData.append('files[]', file); }

                fetch('upload.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    console.log('Upload Response:', data);
                    if(data.success){
                        appendLogMessage(data.message || 'Arquivos enviados. Aguardando processamento...');
                        if(dropzoneFileInput) dropzoneFileInput.value = '';
                        startPollingStatus();
                    } else {
                        appendLogMessage(`Falha ao enviar arquivos: ${data.message || 'Erro desconhecido.'}`, true);
                        appArea.classList.remove('hidden');
                        resultsArea.classList.add('hidden');
                    }
                })
                .catch((error) => {
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
