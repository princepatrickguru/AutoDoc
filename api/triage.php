<?php
// Set headers for JSON response and CORS (allows frontend to talk to backend)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Get raw input from frontend
$inputData = json_decode(file_get_contents('php://input'), true);
$symptoms = isset($inputData['symptoms']) ? trim($inputData['symptoms']) : '';

if (empty($symptoms)) {
    echo json_encode(['error' => 'Please provide symptom details.']);
    exit();
}

// 2. Define the Gemini API key and Endpoint
$apiKey = getenv('GCP_API_KEY');

if (!$apiKey) {
    throw new RuntimeException('GCP_API_KEY is not configured.');
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

// 3. System Prompt forcing Gemini to output JSON strictly
$systemInstruction = "You are an emergency medical triage assistant for AutoDoc Nigeria. "
    . "Analyze the user's symptoms and classify them into one of three risk levels:\n"
    . "1. 'GREEN' (Low Risk / Home Care)\n"
    . "2. 'YELLOW' (Moderate Risk / Clinic Visit Recommended)\n"
    . "3. 'RED' (Emergency / Immediate Action Required)\n\n"
    . "Respond ONLY in valid JSON matching this exact structure without markdown backticks:\n"
    . "{\n"
    . '  "level": "GREEN|YELLOW|RED",' . "\n"
    . '  "title": "Short Summary Title",' . "\n"
    . '  "summary": "Clear, friendly explanation of potential issues in simple language.",' . "\n"
    . '  "first_aid": ["Step 1", "Step 2", "Step 3"],' . "\n"
    . '  "warning_signs": ["Sign 1", "Sign 2"]' . "\n"
    . "}";

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $systemInstruction . "\n\nUser Symptoms: " . $symptoms]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.2,
        "response_mime_type" => "application/json"
    ]
];

// 4. Send request via cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode(['error' => 'Failed to connect to AI triage engine.']);
    exit();
}

// 5. Pass through AI JSON response directly to frontend
$data = json_decode($response, true);
if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    echo $data['candidates'][0]['content']['parts'][0]['text'];
} else {
    echo json_encode(['error' => 'Invalid AI response format.']);
}
?>