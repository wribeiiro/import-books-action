<?php

namespace App\Command\Import;

use Minicli\Curly\Client;
use Minicli\Command\CommandController;

class BookController extends CommandController
{
    public string $API_URL = 'https://gist.githubusercontent.com/wribeiiro/304a9c10d3bcb6ffbf4da923d02dbb2d/raw/3580133f6402eece524e79531bf3c72e318750a9/books.json';

    public function handle(): void
    {
        $this->getPrinter()->display('Fetching books from gist...');

        $crawler = new Client();
        $response = $crawler->get($this->API_URL);

        if ($response['code'] !== 200) {
            $this->getPrinter()->error('Error while contacting the gist :/');
            return;
        }

        if (!$this->getApp()->config->data_path) {
            $this->getPrinter()->error('You must define your data_path config value.');
            return;
        }

        $pathData = $this->getApp()->config->data_path;
        $books = json_decode($response['body']);

        foreach($books->data as $book) {
            $filepath = $pathData . '/books.json';
            $file = fopen($filepath, 'w+');
            fwrite($file, $book->title);
            fclose($file);

            $this->getPrinter()->info("Saved book: " . $book->title);
        }

        $this->getPrinter()->info("Finished importing.", true);
    }
}
