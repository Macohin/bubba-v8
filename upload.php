<?php

header('Content-Type: application/json');

// --- Configuration ---
$n8nWebhookUrl = 'https://macohin.app.n8n.cloud/webhook-test/3007315a-23a6-444f-9034-8d14ffebbf4b';
$baseUploadsDir = __DIR__ . '/uploads'; // Store uploads in a folder named 'uploads' in the same directory as upload.php

// --- Helper Functions ---
function sendJsonResponse($success, $message, $data = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

function cleanupDirectory($dirPath) {
    if (!is_dir($dirPath)) {
        return;
    }
    $files = array_diff(scandir($dirPath), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dirPath/$file")) ? cleanupDirectory("$dirPath/$file") : unlink("$dirPath/$file");
    }
    rmdir($dirPath);
}

// --- Main Execution ---

// 1. Create base uploads directory if it doesn't exist
if (!is_dir($baseUploadsDir)) {
    if (!mkdir($baseUploadsDir, 0775, true)) { // Permissions for web server to write
        sendJsonResponse(false, 'Server error: Could not create base uploads directory.');
    }
}
if (!is_writable($baseUploadsDir)) {
    sendJsonResponse(false, 'Server error: Base uploads directory is not writable.');
}

// 2. Create a unique temporary directory for this request
$uniqueRequestId = uniqid('bubba_', true);
$tempDir = $baseUploadsDir . '/' . $uniqueRequestId;

if (!mkdir($tempDir, 0775, true)) {
    sendJsonResponse(false, 'Server error: Could not create temporary directory for processing.');
}

$jpgFilePaths = [];
$errors = [];

// 3. Retrieve and sanitize CPF
$cpf = '';
if (isset($_POST['cpf'])) {
    $cpf = trim($_POST['cpf']);
    // Remove non-digits (already done client-side, but good to ensure server-side)
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) {
        // Optional: stricter validation if needed, for now just ensure it's 11 digits if provided
        $errors[] = "CPF format received by server is invalid (expected 11 digits, got " . strlen($cpf) . " digits).";
        // Decide if this is a fatal error. For now, we'll still try to proceed if files exist.
        // If CPF is critical for n8n, then:
        // cleanupDirectory($tempDir);
        // sendJsonResponse(false, 'CPF inválido recebido pelo servidor.', ['details' => $errors]);
    }
} else {
    // CPF is mandatory as per new requirement
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'CPF não fornecido.');
}


// 4. Check if files were uploaded
if (empty($_FILES['files']) || empty($_FILES['files']['name'][0])) { // Check if first file name is empty
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'Nenhum arquivo foi enviado.');
}

$uploadedFiles = $_FILES['files'];

// 4. Process each uploaded file
// Loop through each file (if multiple files are uploaded with the name 'files[]')
$fileCount = count($uploadedFiles['name']);

for ($i = 0; $i < $fileCount; $i++) {
    $fileName = $uploadedFiles['name'][$i];
    $fileTmpName = $uploadedFiles['tmp_name'][$i];
    $fileError = $uploadedFiles['error'][$i];
    $fileType = $uploadedFiles['type'][$i]; // MIME type from browser
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileError !== UPLOAD_ERR_OK) {
        $errors[] = "Error uploading file '{$fileName}': Code {$fileError}";
        continue;
    }

    // Basic server-side validation for file types
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];

    // Use finfo for more reliable MIME type detection
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $actualMimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);

    if (!in_array($fileExtension, $allowedExtensions) || !in_array($actualMimeType, $allowedMimeTypes)) {
        $errors[] = "Invalid file type for '{$fileName}'. Allowed types: PDF, JPG, PNG. Detected: {$actualMimeType}";
        continue;
    }

    $sanitizedFileName = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));

    try {
        if ($actualMimeType === 'application/pdf') {
            if (!extension_loaded('imagick')) {
                $errors[] = "Imagick extension is not loaded. Cannot process PDF: {$fileName}";
                continue;
            }
            $im = new Imagick();
            $im->setResolution(150, 150); // Adjust resolution as needed for quality/size
            $im->readImage($fileTmpName);
            $numPages = $im->getNumberImages();
            for ($p = 0; $p < $numPages; $p++) {
                $im->setIteratorIndex($p);
                $im->setImageFormat('jpeg');
                $im->setImageCompressionQuality(85); // Adjust quality
                $outputJpgPath = $tempDir . '/' . $sanitizedFileName . '_page_' . ($p + 1) . '.jpg';
                if ($im->writeImage($outputJpgPath)) {
                    $jpgFilePaths[] = $outputJpgPath;
                } else {
                    $errors[] = "Failed to convert page " . ($p + 1) . " of PDF '{$fileName}' to JPG.";
                }
            }
            $im->clear();
            $im->destroy();
        } elseif ($actualMimeType === 'image/jpeg' || $actualMimeType === 'image/png') {
            $outputJpgPath = $tempDir . '/' . $sanitizedFileName . '.' . ($actualMimeType === 'image/png' ? 'png' : 'jpg'); // Keep original ext for now, will convert to jpg if png
            if (move_uploaded_file($fileTmpName, $outputJpgPath)) {
                 if ($actualMimeType === 'image/png') { // Convert PNG to JPG
                    if (!extension_loaded('imagick')) {
                        $errors[] = "Imagick extension is not loaded. Cannot convert PNG to JPG: {$fileName}";
                        unlink($outputJpgPath); // remove the original PNG
                        continue;
                    }
                    $im = new Imagick($outputJpgPath);
                    $im->setImageFormat('jpeg');
                    $im->setImageCompressionQuality(85);
                    $finalJpgPath = $tempDir . '/' . $sanitizedFileName . '.jpg';
                    if ($im->writeImage($finalJpgPath)) {
                        $jpgFilePaths[] = $finalJpgPath;
                        if ($outputJpgPath !== $finalJpgPath) unlink($outputJpgPath); // remove original PNG if different name
                    } else {
                        $errors[] = "Failed to convert PNG '{$fileName}' to JPG.";
                    }
                    $im->clear();
                    $im->destroy();
                } else {
                    $jpgFilePaths[] = $outputJpgPath; // It's already a JPG
                }
            } else {
                $errors[] = "Failed to move uploaded image '{$fileName}'.";
            }
        }
    } catch (Exception $e) {
        $errors[] = "Error processing file '{$fileName}': " . $e->getMessage();
    }
}


// 5. If errors occurred during file processing, report them
if (!empty($errors) && empty($jpgFilePaths)) {
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'Errors occurred during file processing.', ['details' => $errors]);
}
if (empty($jpgFilePaths)) {
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'No valid JPG files were generated or found.', ['details' => $errors]);
}


// 6. Compress JPGs into a ZIP file
$zipFileName = 'bubba_analysis_docs_' . $uniqueRequestId . '.zip';
$zipFilePath = $tempDir . '/' . $zipFileName;
$zip = new ZipArchive();

if (!extension_loaded('zip')) {
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'Server error: ZipArchive extension is not enabled.');
}

if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'Server error: Could not create ZIP file.', ['details' => $errors]);
}

foreach ($jpgFilePaths as $filePath) {
    if (file_exists($filePath)) {
        $zip->addFile($filePath, basename($filePath));
    }
}
$zip->close();

if (!file_exists($zipFilePath) || filesize($zipFilePath) == 0) {
    cleanupDirectory($tempDir);
    sendJsonResponse(false, 'Server error: ZIP file creation failed or ZIP is empty.', ['details' => $errors]);
}

// 7. Send ZIP file and CPF to n8n webhook using cURL
$curlFile = new CURLFile($zipFilePath, 'application/zip', basename($zipFilePath));
$postData = [
    'file' => $curlFile,
    'cpf'  => $cpf  // Add the cleaned CPF here
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $n8nWebhookUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FAILONERROR, true); // Fail on HTTP errors >= 400
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 2 minutes timeout for n8n processing

$n8nResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// 8. Clean up temporary files and directory
cleanupDirectory($tempDir); // This will remove the temp dir along with JPGs and ZIP inside it.

// 9. Handle n8n response
if ($curlError) {
    sendJsonResponse(false, "Error sending files to n8n: cURL Error - {$curlError}", ['details' => $errors]);
}

if ($httpCode >= 400) {
     sendJsonResponse(false, "n8n webhook returned an error (HTTP {$httpCode}).", ['n8n_response' => $n8nResponse, 'details' => $errors]);
}

// Attempt to decode n8n response if it's JSON, otherwise pass as string
$n8nData = json_decode($n8nResponse, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $n8nData = ['raw_response' => $n8nResponse]; // Send raw if not JSON
}

sendJsonResponse(true, 'Files processed and sent to n8n successfully.', ['n8n_response' => $n8nData, 'processing_errors' => $errors]);

?>
