<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set necessary headers for JSON API and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Create a log file for debugging
$logFile = __DIR__ . '/debug.log';
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "API call started\n", FILE_APPEND);

// API Configuration
// IMPORTANT: Add your own Google Gemini API key below before running this project.
// Get a free key at: https://aistudio.google.com/apikey
$api_key = 'YOUR_GEMINI_API_KEY_HERE';

// Get and validate POST data
$raw_data = file_get_contents('php://input');
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Raw data: " . $raw_data . "\n", FILE_APPEND);

$data = json_decode($raw_data, true);

if (!isset($data['message'])) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Error: No message provided\n", FILE_APPEND);
    echo json_encode(['error' => 'No message provided']);
    exit;
}

$message = $data['message'];
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Message received: " . $message . "\n", FILE_APPEND);

// API Endpoint Configuration
// Builds the Gemini API endpoint using the key set above
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key;

// Request Preparation
// Format the prompt for question generation
$postData = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => "I want you to act as a question generator. When I provide a paragraph or any block of text, your task is to read and understand it, then generate relevant questions based on the content. The questions should test comprehension of the text and can include, short questions in JSON formate with heading Questions. Here is paragraph '" . $message . "'"
                ]
            ]
        ]
    ]
];

// Initialize cURL
if (!function_exists('curl_init')) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Error: cURL is not installed\n", FILE_APPEND);
    echo json_encode(['error' => 'Server configuration error: cURL is not installed']);
    exit;
}

$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

// Debug cURL
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

// Retry Logic
// Implement retry mechanism for failed API calls
$maxRetries = 3;
$retryCount = 0;
$success = false;

while (!$success && $retryCount < $maxRetries) {
    // Make API request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Log the attempt
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Attempt " . ($retryCount + 1) . " response code: " . $httpCode . "\n", FILE_APPEND);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Verbose log: " . $verboseLog . "\n", FILE_APPEND);

    if ($httpCode === 200) {
        $success = true;
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Success! Response: " . $response . "\n", FILE_APPEND);
    } else {
        $retryCount++;
        if ($retryCount < $maxRetries) {
            // Wait before retrying to prevent overwhelming the API
            sleep(2);
        }
    }
}

// Error Handling
// Check for failed requests after retries
if (!$success) {
    $error = curl_error($ch);
    $errorMsg = [
        'error' => 'API request failed after ' . $maxRetries . ' attempts. Last status code: ' . $httpCode,
        'details' => $error ?: 'No additional error details available'
    ];
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Final error: " . json_encode($errorMsg) . "\n", FILE_APPEND);
    echo json_encode($errorMsg);
    exit;
}

// Cleanup
curl_close($ch);

// Response Processing
// Parse and format the API response
$result = json_decode($response, true);

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $generatedText = $result['candidates'][0]['content']['parts'][0]['text'];

    // Try to extract JSON from the response if available
    if (preg_match('/\{.*\}/s', $generatedText, $matches)) {
        echo json_encode([
            'response' => $matches[0]
        ]);
    } else {
        // Return raw text if no JSON format is found
        echo json_encode([
            'response' => $generatedText
        ]);
    }
} else {
    $errorMsg = [
        'error' => 'Unexpected response format',
        'response' => $result
    ];
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Response format error: " . json_encode($errorMsg) . "\n", FILE_APPEND);
    echo json_encode($errorMsg);
}
