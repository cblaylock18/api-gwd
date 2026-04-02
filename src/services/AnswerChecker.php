<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;

$GEMINI_API_KEY = $_ENV['GEMINI_API_KEY'];
$client = Gemini::client($GEMINI_API_KEY);

$response = $client
    ->generativeModel(model: 'gemini-2.5-flash')
    ->withSystemInstruction(
        Content::parse('You are a JSON API. Always respond with valid JSON objects in the format {"correct": "result"}. You are given a question, correct answer, and a user answer, and you must determine if the user answer is correct (result is true) or wrong (result is false). Be forgiving of minor spelling errors and typos — if the intended answer is clear, mark it correct. Only respond with the JSON object, do not include any additional text.')
    )
    ->withGenerationConfig(
        new GenerationConfig(responseMimeType: ResponseMimeType::APPLICATION_JSON)
    )
    ->generateContent('Question: Who wrote Romeo and Juliet? Correct Answer: William Shakespeare. User Answer: Willy S.');

var_dump($response->json());