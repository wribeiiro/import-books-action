<?php

namespace App\Command\Import;

use Minicli\Curly\Client;
use Minicli\Command\CommandController;

class BookController extends CommandController
{
    private string $apiURL = 'https://gist.githubusercontent.com/wribeiiro/304a9c10d3bcb6ffbf4da923d02dbb2d/raw/books.json';

    public function handle(): void
    {
        $this->getPrinter()->display('Fetching books from gist...');

        $crawler = new Client();
        $response = $crawler->get($this->apiURL);

        if ($response['code'] !== 200) {
            $this->getPrinter()->error('Error while contacting the gist :/');
            return;
        }

        $gitBooksJson = json_decode($response['body'], true);
        $localBooksJson = json_decode(file_get_contents(dirname(__DIR__, 3) . '/books.json'), true);

        if (count($gitBooksJson['data']) <= count($localBooksJson['data'])) {
            $this->getPrinter()->info("Json already updated, ignoring update.");
            exit;
        }

        file_put_contents(dirname(__DIR__, 3) . '/books.json', $response['body']);

        $strNewContent = "";
        foreach($gitBooksJson['data'] as $book) {
            $strNewContent .= "- " . $book['title'] . PHP_EOL;
            $this->getPrinter()->info("Reading book: " . $book['title']);
        }

        if (strlen($strNewContent) > 0) {
            file_put_contents(dirname(__DIR__, 3) . '/README.MD', $strNewContent, FILE_APPEND);
            $this->getPrinter()->info("Adding book: " . $book['title']);
        }

        $this->getPrinter()->success("Finished importing.", true);
    }
}
