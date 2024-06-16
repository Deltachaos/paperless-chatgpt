<?php

require_once __DIR__ . '/vendor/autoload.php';

$http = \Symfony\Component\HttpClient\HttpClient::createForBaseUri($_SERVER['PAPERLESS_SERVER'], [
    'headers' => [
        'Authorization' => 'Token ' . $_SERVER['PAPERLESS_KEY']
    ]
]);
$openai = OpenAI::client($_SERVER['OPENAI_KEY'] ?? '');
$gpt = new \App\GPT(isset($_SERVER['OPENAI_KEY']) ? $openai : null);
$job = new \App\Job($gpt, $http, $_SERVER['PAPERLESS_SEARCH'] ?? 'Scanned_from_a_Lexmark_Multifunction');

while (true) {
    $job();
    $interval = $_SERVER['INTERVAL'] ?? 5 * 60;
    echo "Sleep ".$interval." seconds\n";
    sleep($interval);
}
