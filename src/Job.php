<?php

namespace App;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class Job
{
    public function __construct(
        private readonly GPT $gpt,
        private readonly HttpClientInterface $http,
        private readonly int $search,
    ) {}

    public function __invoke()
    {
        $base = '/api/documents/?tags__id__all=' . urlencode($this->search);
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
                        $document['tags'] = array_values(array_diff($document['tags'], [$this->search]));
                        echo " => Update with tags: " . implode(",", $document['tags']) . "; Title: " . $document['title'] . "\n";
                        $response = $this->http->request('PUT', '/api/documents/' . $document['id'] . '/', [
                            'json' => $document,
                        ])->getContent();
                    }
                } catch (Throwable $exception) {
                    echo "Error creating title for document {$document['id']}: {$exception->getMessage()}\n";
                    if ($exception instanceof ClientException) {
                        echo "Content:" . $exception->getResponse()->getContent(false) . "\n";
                    }
                }
            }
        } while($next !== null);
    }
}
