<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Link;

class ScrapePinboardLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-pinboard-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape links from Pinboard and store them in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create new instance of HttpClient
        $client = HttpClient::create();
        $browser = new HttpBrowser($client);

        // Send an HTTP GET request to provide url 
        $crawler = $browser->request('GET', 'https://pinboard.in/u:alasdairw?per_page=120');

        // Filter content with bookmark class and iterate through it
        $crawler->filter('.bookmark')->each(function (Crawler $node) use ($client) {
            // Get individual elements
            $url = $node->filter('.bookmark_title')->attr('href');
            $title = $node->filter('.bookmark_title')->text();
            $comments = $node->filter('.description')->text();
            $tags = $node->filter('.tag')->each(function (Crawler $tagNode) {
                return $tagNode->text();
            });

            // Array of tags I am looking for
            $targetTags = ['laravel', 'vue', 'vue.js', 'php', 'api'];

            // Check if tags on current record match the tas I am looking for
            $commonTags = array_intersect($tags, $targetTags);

            if (!empty($commonTags)) {
                // Check if url is live
                $isLive = $this->checkUrlIsLive($client, $url);

                // Add record to the links table
                Link::create([
                    'url' => $url,
                    'title' => $title,
                    'comments' => $comments,
                    'tags' => json_encode($tags),
                    'is_live' => $isLive,
                ]);
            }
        });

        $this->info('Scraping completed and data stored in the database.');
    }

    private function checkUrlIsLive($client, $url)
    {
        // Check if link is live, make a request and check if an exception is thrown
        try {
            $client->request('HEAD', $url);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
