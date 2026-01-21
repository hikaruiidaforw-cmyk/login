<?php
session_start();

// if(!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()  ){
//  exit('LOGIN ERROR');
// }

// session_regenerate_id(true);
// $_SESSION['chk_ssid'] =session_id();

require_once 'config.php';

loginCheck();


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$text = $_POST['text'] ?? '';
$targetLang = $_POST['target_lang'] ?? 'JA';

if (empty($text)) {
    echo json_encode(['success' => false, 'error' => 'No text provided']);
    exit;
}

// DeepL APIで翻訳
function translateText($text, $targetLang) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, DEEPL_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'auth_key' => DEEPL_API_KEY,
        'text' => $text,
        'target_lang' => $targetLang
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['success' => false, 'error' => 'API request failed'];
    }

    $data = json_decode($response, true);

    if (isset($data['translations'][0]['text'])) {
        return [
            'success' => true,
            'translated' => $data['translations'][0]['text'],
            'detected_lang' => $data['translations'][0]['detected_source_language'] ?? null
        ];
    }

    return ['success' => false, 'error' => 'Translation failed'];
}

$result = translateText($text, $targetLang);
echo json_encode($result);
