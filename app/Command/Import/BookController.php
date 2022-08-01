<?php

namespace App\Command\Import;

use Minicli\Curly\Client;
use Minicli\Command\CommandController;

class BookController extends CommandController
{
    private string $apiURL = 'https://gist.githubusercontent.com/wribeiiro/304a9c10d3bcb6ffbf4da923d02dbb2d/raw/3580133f6402eece524e79531bf3c72e318750a9/books.json';
    //private string $readmeFileUrl = 'https://raw.githubusercontent.com/wribeiiro/wribeiiro/master/README.MD';

    public function handle(): void
    {
        $this->getPrinter()->display('Fetching books from gist...');

        $crawler = new Client();
        $response = $crawler->get($this->apiURL);

        if ($response['code'] !== 200) {
            $this->getPrinter()->error('Error while contacting the gist :/');
            return;
        }

        $books = json_decode($response['body']);
        //$responseReadme = file_get_contents($this->readmeFileUrl);

        //if (!$responseReadme) {
        //    $this->getPrinter()->error('Error while contacting the readmeFileUrl :/');
        //    return;
        //}

        $strNewContent = "";
        foreach($books->data as $book) {
            $strNewContent .= "- " . $book->title . PHP_EOL;
            $this->getPrinter()->info("Reading book: " . $book->title);
        }

        if (strlen($strNewContent) > 0) {
            $file = fopen(dirname(__DIR__, 3) . '/README.MD', 'a');
            fwrite($file, $strNewContent);
            fclose($file);
        }


        $this->getPrinter()->info("Finished importing.", true);
    }
}
