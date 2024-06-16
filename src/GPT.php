<?php

namespace App;

use OpenAI\Client;

class GPT
{
    private const MAX_LENGTH = 16385 / 4.5;
    public const DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config';
    private const MODEL = 'gpt-3.5-turbo';
    private const TEMPERATURE = 0.7;

    public function __construct(
        private readonly ?Client $client = null
    ) {}

    public function response(string $text) : string
    {
        if ($this->client === null) {
            return 'Scanned_from_a_Lexmark_Multifunction_sdfsd';
        }

        $response = $this->client->chat()->create([
            'model' => self::MODEL,
            'temperature' => self::TEMPERATURE,
            'messages' => [
                ['role' => 'user', 'content' => $text]
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    public function prompt(string $prompt, string ...$args) : string
    {
        foreach ($args as $i => $arg) {
            $args[$i] = preg_replace('/[[:^print:]]/', '', preg_replace('/\s+/', ' ', $arg));
        }

        $prompt = file_get_contents(self::DIR . DIRECTORY_SEPARATOR . $prompt);

        return $this->response(substr(sprintf($prompt, ...$args), 0, floor(self::MAX_LENGTH)));
    }
}