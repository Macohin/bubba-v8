<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bubba A.I. - Análise Previdenciária</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js" integrity="sha512-Qlv6VSKh1gDKGoJbnyA5RMXYcvnpIqhO++MhIM2fStMcGT9i2T//tSwYFlcyoRRDcDZ+TYHpH8azBBCyhpSeqw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.js"></script>
    <style>
        body { margin: 0; font-family: sans-serif; overflow: hidden; }
        #bgVideo { position: fixed; right: 0; bottom: 0; min-width: 100%; min-height: 100%; width: auto; height: auto; z-index: -2; object-fit: cover; }
        #videoOverlay { position: fixed; right: 0; bottom: 0; min-width: 100%; min-height: 100%; width: auto; height: auto; background-color: rgba(0, 0, 0, 0.2); z-index: -1; }
        .glass-effect { background: rgba(255, 255, 255, 0.1); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); border-radius: 1rem; border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); }
        .prose-invert a { color: #60a5fa; } .prose-invert a:hover { color: #3b82f6; }
        .log-entry-new { animation: fadeInAndPulse 1.5s ease-out; }
        @keyframes fadeInAndPulse { 0% { opacity: 0; transform: translateY(10px); } 60% { opacity: 1; transform: translateY(0); } 100% { opacity: 1; transform: translateY(0); } }
        #htmlResultContent p { margin-bottom: 0.5rem; }

        /* Fix for file list overflow */
        #fileListPreviewContainer {
            max-height: 100px;
            overflow-y: auto;
            /* Theming for the scrollbar */
            scrollbar-width: thin;
            scrollbar-color: #3b82f6 rgba(255, 255, 255, 0.1);
        }
        #fileListPreviewContainer::-webkit-scrollbar {
            width: 8px;
        }
        #fileListPreviewContainer::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        #fileListPreviewContainer::-webkit-scrollbar-thumb {
            background-color: #3b82f6;
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        #fileListPreviewContainer::-webkit-scrollbar-thumb:hover {
            background-color: #60a5fa;
        }
    </style>
</head>
<body class="bg-black text-white">

    <video autoplay loop muted playsinline id="bgVideo"></video>
    <div id="videoOverlay"></div>

    <div id="mainContentContainer" class="relative min-h-screen flex flex-col items-center justify-center p-4 overflow-y-auto">
        <h1 class="text-4xl lg:text-5xl font-bold text-white mb-6 lg:mb-8 text-center mt-8">Bubba A.I.</h1>

        <div id="appArea" class="w-full max-w-2xl p-6 md:p-8 space-y-6 glass-effect">
            <div id="cpfSection">
                <label for="cpfInput" class="block mb-2 text-sm font-medium text-gray-300">CPF (somente números):</label>
                <input type="text" id="cpfInput" name="cpf" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400" placeholder="00000000000" required>
                <p id="cpfError" class="mt-2 text-xs text-red-400 hidden"></p>
            </div>

            <p id="uploadPromptText" class="text-center text-lg mb-4">Envie seus documentos para análise:</p>
            <div id="dropzoneContainer" class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-60 md:h-64 border-2 border-gray-300/70 dark:border-gray-600/70 border-dashed rounded-lg cursor-pointer bg-white/5 hover:bg-white/10 transition-colors duration-300">
                    <div id="dropzoneInstructions" class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Clique para enviar</span> ou arraste e solte</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF, JPG, PNG</p>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Ou use a <span class="font-semibold">câmera</span> (celular)</p>
                    </div>
                    <input id="dropzone-file" type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png" capture="environment" />
                </label>
            </div>
            <div id="fileListPreviewContainer" class="mt-4 text-sm text-gray-300 dark:text-gray-400 hidden">
                <p class="font-semibold mb-2">Arquivos selecionados:</p>
                <ul id="selectedFilesList" class="list-disc list-inside"></ul>
            </div>
            <button id="startAnalysisBtn" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-3.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors duration-300">
                Bubba, efetue a análise previdenciária
            </button>
        </div>

        <div id="resultsArea" class="hidden w-full max-w-3xl p-6 md:p-8 space-y-6 glass-effect overflow-y-auto" style="max-height: 80vh;">
            <div id="htmlResultContent" class="prose prose-sm sm:prose-base prose-invert max-w-none min-h-[100px]"></div>
            <div id="exportButtonsContainer" class="hidden flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-6 pt-4 border-t border-white/20">
                <button id="exportPdfBtn" class="w-full sm:w-auto text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-md px-6 py-3 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 mr-2 -mt-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A1 1 0 0111.293 2.707L14.586 6H16a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8H4a2 2 0 01-2-2V4zm2 0v4h5.586L8.293 4.707A1 1 0 007.586 4H6zm5 4h2.586L11 5.414V8zM8 10a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm1 3a1 1 0 100 2h2a1 1 0 100-2H9z" clip-rule="evenodd" /></svg>
                    Exportar para PDF
                </button>
                <button id="exportDocxBtn" class="w-full sm:w-auto text-white bg-sky-600 hover:bg-sky-700 focus:ring-4 focus:ring-sky-300 font-medium rounded-lg text-md px-6 py-3 text-center dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:ring-sky-800 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 mr-2 -mt-1" viewBox="0 0 20 20" fill="currentColor"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" /><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v4m0 0l-2-2m2 2l2-2" /></svg>
                    Exportar para DOCX
                </button>
            </div>
        </div>
        <button id="simulateCallbackBtn" class="hidden mt-4 p-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs">Simular Resultado Final (Teste)</button>
    </div>

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

                // Auto-scroll to the bottom to show the new message
                if (resultsArea) resultsArea.scrollTop = resultsArea.scrollHeight;

                // Remove the animation class after it has played
                if (!isError) {
                    setTimeout(() => logEntry.classList.remove('log-entry-new'), 1500);
                }

                // ** New Log Rotation Logic **
                // If the content is overflowing the container, remove old logs from the top
                if (resultsArea && htmlResultContent.scrollHeight > resultsArea.clientHeight) {
                    // Use a timeout to allow the DOM to update before we check the height and remove elements
                    setTimeout(() => {
                        while (htmlResultContent.scrollHeight > resultsArea.clientHeight) {
                            if (htmlResultContent.firstChild) {
                                htmlResultContent.removeChild(htmlResultContent.firstChild);
                            } else {
                                break; // Safety break if empty
                            }
                        }
                    }, 0);
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
</body>
</html>
