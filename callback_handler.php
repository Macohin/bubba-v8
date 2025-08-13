<?php
header('Content-Type: application/json');
error_reporting(0); // Suppress warnings/errors from being sent in the response

// --- Configuration ---
$logFilePath = __DIR__ . '/callbacks.log';
$statusDir = __DIR__ . '/status_files';

// --- Helper function for logging ---
function log_message($message) {
    global $logFilePath;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFilePath, "[$timestamp] " . $message . "\n", FILE_APPEND | LOCK_EX);
}

// --- Main Execution ---
log_message("--- Callback Handler Triggered ---");

// 1. Ensure the status directory exists and is writable
if (!is_dir($statusDir)) {
    if (!@mkdir($statusDir, 0775, true)) {
        http_response_code(500);
        $error_msg = 'Server Error: Unable to create status directory. Please check permissions.';
        log_message("FATAL: " . $error_msg);
        echo json_encode(['success' => false, 'message' => $error_msg]);
        exit;
    }
}

// 2. Get data from the request - Handle both raw JSON and Form Data
$input = file_get_contents('php://input');
log_message("Received Raw Callback Data: " . $input);
$data = json_decode($input, true);

// If raw JSON decoding fails or it's not an associative array, try parsing as form-data
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    log_message("Raw payload is not valid JSON. Attempting to parse as form-data from POST.");
    $data = $_POST;
}

// 3. Extract key data: CPF, link, and/or phrases
$cpf = $data['cpf'] ?? null;
$link = $data['link'] ?? null;
$phrases = [];

// 4. Validate CPF
if (empty($cpf)) {
    http_response_code(400);
    $error_msg = 'Error: CPF not found or invalid in callback payload.';
    log_message($error_msg . " Inspected payload: " . json_encode($data));
    echo json_encode(['success' => false, 'message' => $error_msg]);
    exit;
}
$cpf = preg_replace('/\D/', '', $cpf); // Sanitize CPF

// 5. Determine workflow: Final Result (link) or Intermediate Update (frases)

// --- Workflow A: Final Result Link ---
if (!empty($link)) {
    log_message("Processing 'link' workflow for CPF {$cpf}. Link: {$link}");

    $statusToSave = [
        'status'       => 'result_ready',
        'link' => $link,
        'timestamp'    => time()
    ];

// --- Workflow B: Intermediate Phrases ---
} else {
    log_message("Processing 'frases' workflow for CPF {$cpf}.");
    if (isset($data['frases'])) {
        if (is_string($data['frases'])) {
            $decoded_output = json_decode($data['frases'], true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded_output['frases']) && is_array($decoded_output['frases'])) {
                $phrases = $decoded_output['frases'];
                log_message("Extracted " . count($phrases) . " phrases from nested JSON string.");
            }
        } elseif (is_array($data['frases'])) {
            $phrases = $data['frases'];
            log_message("Extracted " . count($phrases) . " phrases from direct array.");
        }
    }

    if (empty($phrases)) {
        http_response_code(400);
        $error_msg = 'Error: Payload for CPF ' . $cpf . ' contained neither a "link" nor a valid "frases" array.';
        log_message($error_msg . " Inspected payload: " . json_encode($data));
        echo json_encode(['success' => false, 'message' => $error_msg]);
        exit;
    }

    $statusToSave = [
        'status'       => 'frases_received',
        'frases'       => $phrases,
        'timestamp'    => time()
    ];
}

// 6. Save the final status file for the frontend
$statusFilePath = "{$statusDir}/latest_status_{$cpf}.json";

if (file_put_contents($statusFilePath, json_encode($statusToSave, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) === false) {
    http_response_code(500);
    $error_msg = "Server Error: Failed to write to status file: {$statusFilePath}";
    log_message("FATAL: " . $error_msg);
    echo json_encode(['success' => false, 'message' => $error_msg]);
    exit;
}

log_message("Success: Status file for CPF {$cpf} updated successfully.");
echo json_encode(['success' => true, 'message' => 'Callback processed and status updated successfully.']);
?>
