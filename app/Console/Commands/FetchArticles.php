<?php

namespace App\Console\Commands;

use App\Models\Article;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class FetchArticles extends Command
{
    protected $signature = 'articles:fetch';

    public function handle()
    {
        $client = new Client([
            'verify' => storage_path('cacert.pem'), // Path to the cacert.pem file
        ]);

        // Fetch articles from NewsAPI
        $this->fetchNewsAPIArticles($client);

        // Fetch articles from The Guardian API
        $this->fetchGuardianArticles($client);

        // Fetch articles from New York Times API
        $this->fetchNYTArticles($client);

        // Fetch articles from NewsCred API
        // $this->fetchFromNewsCred($client);

        $this->info('Articles fetched successfully.');
    }

    protected function fetchNewsAPIArticles($client)
    {
        try {
            $newsApiResponse = $client->get('https://newsapi.org/v2/top-headlines?country=us&apiKey=64187e14c582491991711f103dab4732');
            $articles = json_decode($newsApiResponse->getBody()->getContents())->articles;

            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['title' => $article->title],
                    [
                        'author' => $article->author,
                        'source' => $article->source->name,
                        'category' => 'general', // You can modify this as per the article
                        'content' => $article->content ?: 'Content not available',
                        'published_at' => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $article->publishedAt),
                    ]
                );
            }

            $this->info("Fetched articles from NewsAPI successfully.");

        } catch (\Exception $e) {
            $this->error("Failed to fetch articles from NewsAPI: " . $e->getMessage());
        }
    }

    protected function fetchGuardianArticles($client)
    {
        try {
            $guardianApiResponse = $client->get('https://content.guardianapis.com/search?page=2&q=debate&api-key=5aec5c3d-5d01-4584-8436-7f045046c5d4');
            $articles = json_decode($guardianApiResponse->getBody()->getContents())->response->results;

            foreach ($articles as $article) {
                // Check if the 'fields' object and 'body' are present before accessing them
                $content = isset($article->fields->body) ? $article->fields->body : 'Content not available';

                Article::updateOrCreate(
                    ['title' => $article->webTitle],
                    [
                        'author' => null, // The Guardian API does not return authors for all articles
                        'source' => 'The Guardian',
                        'category' => $article->pillarName, // You can modify this as per the article category
                        'content' => $content, // Accessing the full article content
                        'published_at' => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $article->webPublicationDate),
                    ]
                );
            }

            $this->info("Fetched articles from The Guardian successfully.");

        } catch (\Exception $e) {
            $this->error("Failed to fetch articles from The Guardian: " . $e->getMessage());
        }
    }

    protected function fetchNYTArticles($client)
    {
        try {
            // Use your NYT API Key
            $nytApiKey = 'p4VwyHl3OdWHx2QpG73MlJT832habR35';
            $nytApiResponse = $client->get("https://api.nytimes.com/svc/topstories/v2/home.json?api-key={$nytApiKey}");
            $articles = json_decode($nytApiResponse->getBody()->getContents())->results;

            foreach ($articles as $article) {
                // Check if the abstract exists and use it as the content
                $content = isset($article->abstract) ? $article->abstract : 'Content not available';

                Article::updateOrCreate(
                    ['title' => $article->title],
                    [
                        'author' => implode(', ', array_column($article->byline->item ?? [], 'original')) ?: null, // Use original byline
                        'source' => 'The New York Times',
                        'category' => $article->section, // Using section as category
                        'content' => $content,
                        'published_at' => \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $article->published_date),
                    ]
                );
            }

            $this->info("Fetched articles from The New York Times successfully.");
        } catch (\Exception $e) {
            $this->error("Failed to fetch articles from The New York Times: " . $e->getMessage());
        }
    }

    /**
     * Fetch articles from NewsCred API.
     */
    // private function fetchFromNewsCred($client)
    // {
    //     try {
    //         // Replace 'YOUR_NEWSCRED_API_KEY' with your actual NewsCred API key
    //         $newsCredResponse = $client->get('https://api.newscred.com/v2/articles?apiKey=YOUR_NEWSCRED_API_KEY');
    //         $articles = json_decode($newsCredResponse->getBody()->getContents())->articles;

    //         foreach ($articles as $article) {
    //             Article::updateOrCreate(
    //                 ['title' => $article->headline],
    //                 [
    //                     'author' => $article->author,
    //                     'source' => $article->source->name ?? 'NewsCred',
    //                     'category' => $article->category ?? 'general',
    //                     'content' => $article->body,
    //                     'published_at' => $article->published_at,
    //                 ]
    //             );
    //         }
    //     } catch (\Exception $e) {
    //         $this->error("Failed to fetch articles from NewsCred: " . $e->getMessage());
    //     }
    // }
}
