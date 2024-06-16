<?php

namespace App;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class Job
{
    public function __construct(
        private readonly GPT $gpt,
        private readonly HttpClientInterface $http,
        private readonly string $search,
    ) {}

    public function __invoke()
    {
        $base = '/api/documents/?title__icontains=' . urlencode($this->search);
        $page = 1;
        do {
            $response = $this->http->request('GET', $base . '&page=' . $page)->toArray();
            $page++;
            $next = $response['next'];
            foreach ($response['results'] as $document) {
                echo "Process document {$document['id']}: {$document['title']}\n";
                try {
                    $title = $this->gpt->prompt('title.prompt.txt', $document['content']);
                    if (!empty($title)) {
                        echo " => Update document title: {$title}\n";
                        $document['title'] = $title;
                        $this->http->request('PUT', '/api/documents/' . $document['id'] . '/', [
                            'json' => $document,
                        ]);
                    }
                } catch (Throwable $exception) {
                    echo "Error creating title for document {$document['id']}: {$exception->getMessage()}\n";
                }
            }
        } while($next !== null);
    }
}