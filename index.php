<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Bubba A.I. - Análise Previdenciária</title>
<meta name="theme-color" content="#1A0033" />
<meta name="description" content="Bubba A.I. - Asynchronous AI Multi-Agents" />
<script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Fira+Code&family=IBM+Plex+Mono&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #1A0033;
    --text-primary: #FFFFFF;
    --text-secondary: #9B84D4;
    --accent-cyan: #00ffea;
    --accent-green: #00ff80;
    --accent-magenta: #ff00d4;
    --mono: 'JetBrains Mono', 'Fira Code', 'IBM Plex Mono', monospace;
  }
  @keyframes gradientAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  @keyframes blink {
    50% { opacity: 0; }
  }
  @keyframes scanline {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(100%); }
  }
  *{box-sizing:border-box}
  body{
    margin:0;
    color:var(--text-primary);
    font-family:var(--mono);
    background-color: var(--bg);
    overflow:hidden;
    display:flex;
    flex-direction:column;
    align-items: center;
    justify-content: center;
    min-height:100vh;
    padding: 1em;
  }
  #background-video {
    position: fixed; top: 50%; left: 50%;
    min-width: 100%; min-height: 100%;
    width: auto; height: auto;
    transform: translateX(-50%) translateY(-50%);
    z-index: -2; opacity: 0.3; object-fit: cover;
  }
  .ready-indicator {
    position: fixed;
    top: 20px;
    right: 20px;
    color: var(--accent-green);
    font-size: 1.2em;
    text-shadow: 0 0 5px var(--accent-green);
    animation: blink 1.5s infinite;
  }
  .container{
    position:relative; z-index:2; width:100%; max-width:1120px; margin:0 auto;
    display:flex; flex-direction:column; align-items:center;
    flex-grow: 1; justify-content: center;
  }
  .hero{text-align:center; margin-bottom: 2rem;}
  .ascii{
    white-space:pre; user-select:none; max-width:100%;
    line-height:1.02; letter-spacing:0; font-size: clamp(10px, 2vw, 18px);
    color: transparent;
    background-image: radial-gradient(120% 120% at 50% 20%, #c7e6ff, #ffffff 45%, #dff7ff 70%);
    -webkit-background-clip: text; background-clip: text;
    filter: drop-shadow(0 0 6px #a3d4ff) drop-shadow(0 0 14px var(--accent-cyan));
  }
  .subtitle {
    color: var(--text-secondary);
    font-size: 1.1em;
    height: 1.5em; /* Prevent layout shift */
  }
  .card {
    width:min(900px, 95%);
    border: 2px solid var(--accent-cyan);
    border-radius: 18px;
    box-shadow: 0 0 25px rgba(0, 255, 234, 0.3), inset 0 0 15px rgba(0, 255, 234, 0.2);
    overflow:hidden;
    display:flex; flex-direction:column;
    background: linear-gradient(135deg, rgba(0, 255, 234, 0.1), rgba(155, 132, 212, 0.1), rgba(255, 0, 212, 0.1));
    background-size: 200% 200%;
    animation: gradientAnimation 15s ease infinite;
  }
  .card-body { padding: 2rem; }
  .form-group { margin-bottom: 1.5rem; }
  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--accent-cyan);
    text-shadow: 0 0 3px var(--accent-cyan);
  }
  .form-group input, .form-group select {
    background: rgba(0,0,0,0.3);
    border: 1px solid var(--text-secondary);
    border-radius: 8px; padding: 12px;
    color: var(--text-primary); width: 100%; font-family: var(--mono);
    transition: all 0.3s ease;
  }
  .form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: var(--accent-cyan);
    box-shadow: 0 0 10px var(--accent-cyan);
  }
  .phone-input-group { display: flex; gap: 10px; }
  .phone-input-group select {
      flex: 0 0 120px;
      -webkit-appearance: none; -moz-appearance: none; appearance: none;
      background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2300ffea%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E');
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: .65em auto;
  }
  .error-message { color: var(--accent-magenta); font-size: 0.8em; margin-top: 0.5rem; min-height: 1.2em; text-shadow: 0 0 4px var(--accent-magenta); }
  .dropzone {
    border: 2px dashed var(--text-secondary);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .dropzone:hover { border-color: var(--accent-cyan); background: rgba(0, 255, 234, 0.05); }

  .submit-btn {
    width: 100%;
    padding: 1rem;
    font-family: var(--mono);
    font-size: 1.2rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--bg);
    background: linear-gradient(90deg, var(--accent-green), var(--accent-cyan));
    border: none;
    border-radius: 8px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 0 15px rgba(0, 255, 128, 0.4);
  }
  .submit-btn:hover {
    box-shadow: 0 0 30px rgba(0, 255, 234, 0.6);
  }
  .submit-btn .scanline {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--text-primary);
    opacity: 0.5;
    transform: translateY(-100%);
    animation: scanline 0.5s linear infinite;
    display: none;
  }
  .submit-btn:hover .scanline { display: block; }

  #resultsArea {
    width:min(900px, 95%);
    background: rgba(0,0,0,0.5);
    border: 2px solid var(--accent-magenta);
    border-radius: 18px;
    padding: 2rem;
    height: 70vh;
    display: flex;
    flex-direction: column-reverse; /* New logs appear at the bottom and push old ones up */
    overflow: hidden;
  }
  #htmlResultContent {
    font-size: 1em;
    line-height: 1.6;
    white-space: pre-wrap;
  }
</style>
</head>
<body>
  <div id="background-video"></div> <!-- Particles.js will target this -->
  <div class="ready-indicator">[READY]</div>

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
      <div class="subtitle"><span id="typed-subtitle"></span></div>
    </section>

    <div id="appArea" class="card">
      <div class="card-body">
        <div class="form-group">
          <label for="cpfInput">&gt; Informe o CPF do segurado:</label>
          <input type="text" id="cpfInput" name="cpf" placeholder="Apenas números" required>
          <p id="cpfError" class="error-message"></p>
        </div>

        <div class="form-group">
            <label for="phoneInput">&gt; WhatsApp (Obrigatório):</label>
            <div class="phone-input-group">
                <select id="countryCodeSelect" aria-label="Country code">
                    <option value="1" data-placeholder="(xxx) xxx-xxxx" selected>🇺🇸 +1</option>
                    <option value="55" data-placeholder="(xx) xxxxx-xxxx">🇧🇷 +55</option>
                </select>
                <input type="tel" id="phoneInput" name="whatsapp" placeholder="(xxx) xxx-xxxx" required autocomplete="tel">
            </div>
            <p id="whatsappError" class="error-message"></p>
        </div>

        <div class="form-group">
            <label for="dropzone-file">&gt; Documentos (CNIS, CTPS, etc.):</label>
            <div id="dropzoneContainer" class="dropzone">
                <p>Arraste e solte ou clique para selecionar</p>
                <input id="dropzone-file" type="file" class="hidden" multiple />
            </div>
            <div id="fileListPreviewContainer" class="mt-4"></div>
        </div>

        <button id="startAnalysisBtn" class="submit-btn">
            <span class="scanline"></span>
            &gt;&gt; EXECUTAR ANÁLISE
        </button>
      </div>
    </div>

    <div id="resultsArea" class="hidden">
        <div id="htmlResultContent"></div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Library Initializations ---
        new Typed('#typed-subtitle', {
            strings: ['// Asynchronous AI Multi-Agents — Automated Legal Analysis'],
            typeSpeed: 50,
            showCursor: false,
        });

        // --- DOM Element References ---
        const appArea = document.getElementById('appArea');
        const resultsArea = document.getElementById('resultsArea');
        const htmlResultContent = document.getElementById('htmlResultContent');
        const dropzoneFileInput = document.getElementById('dropzone-file');
        const dropzoneContainer = document.getElementById('dropzoneContainer');
        const fileListPreviewContainer = document.getElementById('fileListPreviewContainer');
        const startAnalysisBtn = document.getElementById('startAnalysisBtn');
        const cpfInput = document.getElementById('cpfInput');
        const cpfError = document.getElementById('cpfError');
        const countryCodeSelect = document.getElementById('countryCodeSelect');
        const phoneInput = document.getElementById('phoneInput');
        const whatsappError = document.getElementById('whatsappError');

        let pollingIntervalId = null;
        let currentCpf = null;

        // --- Phone Input Masking ---
        const maskOptions = {
            '1': { mask: '(000) 000-0000' },
            '55': { mask: '(00) 00000-0000' }
        };
        let phoneMask = IMask(phoneInput, { mask: maskOptions[countryCodeSelect.value] });
        phoneInput.placeholder = maskOptions[countryCodeSelect.value].placeholder;

        countryCodeSelect.addEventListener('change', () => {
            const countryCode = countryCodeSelect.value;
            phoneMask.updateOptions({ mask: maskOptions[countryCode].mask });
            phoneInput.placeholder = maskOptions[countryCode].placeholder || '';
            validateAndSanitizePhone();
        });

        // --- File Dropzone Logic ---
        dropzoneContainer.addEventListener('click', () => dropzoneFileInput.click());
        dropzoneContainer.addEventListener('dragover', (e) => { e.preventDefault(); e.stopPropagation(); dropzoneContainer.style.borderColor = 'var(--accent-green)'; });
        dropzoneContainer.addEventListener('dragleave', (e) => { e.preventDefault(); e.stopPropagation(); dropzoneContainer.style.borderColor = 'var(--text-secondary)'; });
        dropzoneContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneContainer.style.borderColor = 'var(--text-secondary)';
            dropzoneFileInput.files = e.dataTransfer.files;
            updateFileList();
        });
        dropzoneFileInput.addEventListener('change', updateFileList);

        function updateFileList() {
            fileListPreviewContainer.innerHTML = '';
            if (dropzoneFileInput.files.length > 0) {
                const list = document.createElement('ul');
                list.style.listStyle = 'none';
                list.style.padding = '0';
                Array.from(dropzoneFileInput.files).forEach(file => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `> ${file.name}`;
                    listItem.style.color = 'var(--accent-cyan)';
                    list.appendChild(listItem);
                });
                fileListPreviewContainer.appendChild(list);
            }
        }

        // --- Validation and Sanitization ---
        function validateAndSanitizePhone() {
            const countryCode = countryCodeSelect.value;
            const rawValue = phoneInput.value; // Corrected this line
            let sanitized = phoneMask.unmaskedValue;

            let isValid = false;
            let errorMessage = '';

            if (rawValue.trim() === '') {
                errorMessage = 'Número de WhatsApp é obrigatório.';
            } else if (countryCode === '1') {
                if (sanitized.length === 10) sanitized = '1' + sanitized;
                isValid = sanitized.length === 11 && sanitized.startsWith('1');
                if (!isValid) errorMessage = 'Número dos EUA inválido.';
            } else if (countryCode === '55') {
                if (sanitized.length === 11) sanitized = '55' + sanitized;
                isValid = sanitized.length === 13 && sanitized.startsWith('55') && sanitized.substring(4, 5) === '9';
                if (!isValid) errorMessage = 'Celular do Brasil inválido.';
            }

            whatsappError.textContent = errorMessage;
            return { isValid, sanitizedNumber: sanitized };
        }
        phoneInput.addEventListener('input', validateAndSanitizePhone);

        // --- Form Submission ---
        startAnalysisBtn.addEventListener('click', () => {
            const cpfValue = cpfInput.value.trim();
            const cleanedCpf = cpfValue.replace(/\D/g, '');
            let isCpfValid = false;
            if (!/^\d{11}$/.test(cleanedCpf)) {
                cpfError.textContent = 'CPF inválido. Deve conter 11 dígitos.';
            } else {
                cpfError.textContent = '';
                isCpfValid = true;
            }

            const phoneResult = validateAndSanitizePhone();
            const files = dropzoneFileInput.files;
            if (files.length === 0) {
                alert('É obrigatório enviar pelo menos um documento.');
            }

            if (!isCpfValid || !phoneResult.isValid || files.length === 0) return;

            currentCpf = cleanedCpf;
            appArea.style.display = 'none';
            resultsArea.classList.remove('hidden');

            // The backend (upload.php) now handles deleting the old status file.
            // We can proceed directly with the upload.
            const formData = new FormData();
            formData.append('cpf', currentCpf);
            formData.append('whatsapp', phoneResult.sanitizedNumber);
            for (const file of files) {
                formData.append('files[]', file);
            }

            fetch('upload.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        startPollingStatus();
                    } else {
                        // If upload fails, show the form again
                        appArea.style.display = 'block';
                        resultsArea.classList.add('hidden');
                        alert(`Erro no envio: ${data.message || 'Falha desconhecida.'}`);
                    }
                })
                .catch(err => {
                    appArea.style.display = 'block';
                    resultsArea.classList.add('hidden');
                    appendLogMessage(`> ERRO CRÍTICO: ${err.message}`);
                });
        });

        // --- Polling and Log Display ---
        function startPollingStatus() {
            stopPollingStatus();
            pollingIntervalId = setInterval(fetchAndUpdateStatus, 3000);
        }
        function stopPollingStatus() {
            clearInterval(pollingIntervalId);
        }
        function fetchAndUpdateStatus() {
            fetch(`get_latest_status.php?cpf=${currentCpf}&r=${Date.now()}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.frases) {
                        data.frases.forEach(frase => appendLogMessage(frase));
                    }
                    if (data && data.status === 'result_ready' && data.link) {
                        stopPollingStatus();
                        appendLogMessage(`> Análise finalizada. Redirecionando...`);
                        setTimeout(() => window.location.href = data.link, 2000);
                    }
                }).catch(err => console.error("Polling error:", err));
        }

        function appendLogMessage(message) {
            const p = document.createElement('p');
            const span = document.createElement('span');
            p.appendChild(span);
            htmlResultContent.appendChild(p);

            new Typed(span, {
                strings: [message],
                typeSpeed: 20,
                showCursor: false,
                onComplete: () => {
                    // Clean up old logs if container overflows
                    while (resultsArea.scrollHeight > resultsArea.clientHeight) {
                        if (htmlResultContent.firstChild) {
                            htmlResultContent.removeChild(htmlResultContent.firstChild);
                        } else {
                            break;
                        }
                    }
                }
            });
        }
    });
  </script>
</body>
</html>
