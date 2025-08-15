<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Bubba A.I. - Análise Previdenciária</title>
<meta name="theme-color" content="#1A0033" />
<meta name="description" content="Bubba A.I. - Análise Previdenciária" />
<script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;700&family=VT323&display=swap" rel="stylesheet">
<style>
  :root{
    --bg-main: #1A0033;
    --text-primary: #FFFFFF;
    --text-secondary: #9B84D4;
    --accent-string: #FFD166;
    --accent-func: #00E5FF;
    --accent-keyword: #FF00FF;
    --accent-green: #39FF14;
    --accent-pink: #FF3366;
    --grid: rgba(155, 132, 212, .1);
    --card-bg: linear-gradient(180deg, rgba(32, 16, 65, 0.85), rgba(37, 17, 77, 0.85));
    --shadow: 0 0 18px rgba(88,166,255,.28), 0 0 36px rgba(55,227,195,.18);
    --mono: 'Fira Code', monospace;
  }
  *{box-sizing:border-box}
  body{
    margin:0;
    color:var(--text-primary);
    font-family:var(--mono);
    background-color: var(--bg-main);
    overflow-x:hidden;
    display:flex;
    flex-direction:column;
    min-height:100svh;
  }
  #background-video {
    position: fixed; top: 50%; left: 50%;
    min-width: 100%; min-height: 100%;
    width: auto; height: auto;
    transform: translateX(-50%) translateY(-50%);
    z-index: -2; opacity: 0.5; object-fit: cover;
  }
  .container{position:relative; z-index:2; width:100%; max-width:1120px; margin:0 auto;
    padding: clamp(16px, 3vw, 32px); display:flex; flex-direction:column; align-items:center;
    flex-grow: 1; justify-content: center;
  }
  .hero{text-align:center; margin-bottom: 1rem;}
  .ascii{
    white-space:pre; user-select:none; max-width:100%;
    line-height:1.02; letter-spacing:0; font-size: clamp(10px, 2vw, 18px);
    color: transparent;
    background-image: radial-gradient(120% 120% at 50% 20%, #c7e6ff, #ffffff 45%, #dff7ff 70%);
    -webkit-background-clip: text; background-clip: text;
    filter: drop-shadow(0 0 6px #a3d4ff) drop-shadow(0 0 14px #78ffe6);
  }
  .tagline{
    margin-top:10px; font-size:clamp(12px,1.2vw,14px); letter-spacing:1px;
    color: var(--text-secondary);
  }
  .card{
    width:min(900px, 95%);
    background: var(--card-bg);
    border:2px dotted var(--text-secondary);
    border-radius:18px;
    box-shadow: var(--shadow);
    overflow:hidden;
    display:flex; flex-direction:column;
  }
  .card-body{padding: 2rem; display: flex; flex-direction: column; gap: 1rem;}
  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
  }
  .form-group input, .form-group select {
    background: var(--bg-main);
    border: 1px solid var(--text-secondary);
    border-radius: 8px; padding: 12px;
    color: var(--text-primary); width: 100%; font-family: var(--mono);
  }
  .form-group input:focus, .form-group select:focus {
    outline: none; border-color: var(--accent-func); box-shadow: 0 0 8px var(--accent-func);
  }
  .phone-input-group { display: flex; gap: 10px; }
  .phone-input-group select {
      flex: 0 0 120px;
      background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2300E5FF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E');
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: .65em auto;
      -webkit-appearance: none; appearance: none;
  }
  .error-message { color: var(--accent-pink); font-size: 0.8em; margin-top: 0.5rem; min-height: 1.2em; }
  .dropzone {
    border: 2px dashed var(--text-secondary);
    border-radius: 12px; padding: 2rem;
    text-align: center; cursor: pointer;
  }
  .dropzone:hover { border-color: var(--accent-func); }
  .file-list {
    display: flex; flex-wrap: wrap; gap: 8px;
    list-style: none; padding: 0; margin-top: 1rem;
  }
  .file-pill {
    background: rgba(0, 229, 255, 0.1);
    border: 1px solid rgba(0, 229, 255, 0.2);
    border-radius: 12px; padding: 5px 12px;
    font-size: 0.8em; color: var(--accent-func);
  }
  .submit-btn {
    width: 100%; padding: 1rem; font-family: var(--mono);
    font-size: 1.2rem; text-transform: uppercase;
    color: var(--bg-main);
    background: linear-gradient(90deg, var(--accent-green), var(--accent-func));
    border: none; border-radius: 8px; cursor: pointer;
  }
  #resultsArea {
    width:min(900px, 95%);
    background: rgba(0,0,0,0.5);
    border: 2px solid var(--accent-magenta);
    border-radius: 18px; padding: 2rem;
    height: 70vh;
    display: flex; flex-direction: column-reverse;
    overflow: hidden;
  }
  #htmlResultContent { font-size: 1em; line-height: 1.6; white-space: pre-wrap; }
</style>
</head>
<body>
  <video id="background-video" autoplay loop muted playsinline src="https://bubba.macohin.ai/bg/bg.mp4"></video>
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
      <div class="tagline">// Asynchronous AI Multi-Agents — Automated Legal Analysis</div>
    </section>

    <div id="appArea" class="card">
      <div class="card-body">
        <div class="form-group">
          <label for="cpfInput">🐾 Informe abaixo o CPF do segurado</label>
          <input type="text" id="cpfInput" name="cpf" placeholder="Apenas números" required>
          <p id="cpfError" class="error-message"></p>
        </div>

        <div class="form-group">
            <label for="phoneInput">WhatsApp (Obrigatório):</label>
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
            <label for="dropzone-file">Documentos do Segurado</label>
            <div id="dropzoneContainer" class="dropzone">
                <p style="font-size: 0.9em; line-height: 1.45;">📂 Para iniciar a análise, envie os documentos do segurado — é fundamental incluir, no mínimo, o CNIS e a CTPS, sendo recomendável também anexar PPP, comprovantes de vínculos e quaisquer outros registros que possam complementar o estudo.</p>
                <input id="dropzone-file" type="file" class="hidden" multiple />
            </div>
            <ul id="fileList" class="file-list"></ul>
        </div>

        <button id="startAnalysisBtn" class="submit-btn">
            EXECUTAR ANÁLISE
        </button>
      </div>
    </div>

    <div id="resultsArea" class="hidden">
        <div id="htmlResultContent"></div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- DOM Element References ---
        const appArea = document.getElementById('appArea');
        const resultsArea = document.getElementById('resultsArea');
        const htmlResultContent = document.getElementById('htmlResultContent');
        const dropzoneFileInput = document.getElementById('dropzone-file');
        const dropzoneContainer = document.getElementById('dropzoneContainer');
        const fileList = document.getElementById('fileList');
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
            '1': { mask: '(000) 000-0000', placeholder: '(xxx) xxx-xxxx' },
            '55': { mask: '(00) 00000-0000', placeholder: '(xx) xxxxx-xxxx' }
        };
        let phoneMask = IMask(phoneInput, { mask: maskOptions[countryCodeSelect.value] });

        countryCodeSelect.addEventListener('change', () => {
            const countryCode = countryCodeSelect.value;
            phoneMask.updateOptions({ mask: maskOptions[countryCode].mask });
            phoneInput.placeholder = maskOptions[countryCode].placeholder || '';
            validateAndSanitizePhone();
        });

        // --- File Dropzone & List Logic ---
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
            fileList.innerHTML = '';
            if (dropzoneFileInput.files.length > 0) {
                Array.from(dropzoneFileInput.files).forEach(file => {
                    const pill = document.createElement('li');
                    pill.className = 'file-pill';
                    pill.textContent = file.name;
                    fileList.appendChild(pill);
                });
            }
        }

        // --- Validation and Sanitization ---
        function validateAndSanitizePhone() {
            const countryCode = countryCodeSelect.value;
            const rawValue = phoneInput.value;
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
                        appArea.style.display = 'block';
                        resultsArea.classList.add('hidden');
                        alert(`Erro no envio: ${data.message || 'Falha desconhecida.'}`);
                    }
                })
                .catch(err => {
                    appArea.style.display = 'block';
                    resultsArea.classList.add('hidden');
                    alert(`ERRO CRÍTICO: ${err.message}`);
                });
        });

        // --- Polling and Log Display ---
        let initialMessagesLooping = false;
        let initialMessageTimeout;
        let realLogsStarted = false;

        function startPollingStatus() {
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
                    // Start real polling after initial messages are done
                    stopPollingStatus();
                    pollingIntervalId = setInterval(fetchAndUpdateStatus, 3000);
                    return;
                }
                appendLogMessage(initialMessages[messageIndex]);
                messageIndex++;
                initialMessageTimeout = setTimeout(displayNextInitialMessage, 2000);
            }

            displayNextInitialMessage();
            fetchAndUpdateStatus(); // Initial fetch
        }

        function stopPollingStatus() {
            clearInterval(pollingIntervalId);
            clearTimeout(initialMessageTimeout);
            initialMessagesLooping = false;
        }

        function fetchAndUpdateStatus() {
            fetch(`get_latest_status.php?cpf=${currentCpf}&r=${Date.now()}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.frases && data.frases.length > 0) {
                        if (!realLogsStarted) {
                            realLogsStarted = true;
                            stopPollingStatus(); // Stop initial message loop
                            htmlResultContent.innerHTML = ''; // Clear initial messages
                        }
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
            htmlResultContent.insertBefore(p, htmlResultContent.firstChild);

            new Typed(span, {
                strings: [message],
                typeSpeed: 20,
                showCursor: false,
                onComplete: () => {
                    // Clean up old logs if container overflows
                    while (resultsArea.scrollHeight > resultsArea.clientHeight) {
                        if (htmlResultContent.lastChild) {
                            htmlResultContent.removeChild(htmlResultContent.lastChild);
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
