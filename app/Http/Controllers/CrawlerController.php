<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CrawledPages;
use App\Models\CrawlSession;

class CrawlerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //pick website
        $targetHomepage = "https://agencyanalytics.com";

        //generate hash for session
        $hash = bin2hex(random_bytes(32));

        //randomly pick between 4-6 pages to crawl
        $numberPages = mt_rand(4, 6);

        //arrays to store data from all crawled pages
        $internalLinks = [];
        $externalLinks = [];
        $timeLoad = [];
        $titleLength = [];
        $numberWords = [];
        $pageImages = [];

        //get internal and external links of homepage
        $html = file_get_contents($targetHomepage);
        $links = getLinks($html);
        foreach ($links[0] as $key => $link){
            array_push($externalLinks, $link);
        }
        foreach ($links[1] as $key => $link){
            array_push($internalLinks, $link);
        }  
       
        #pick 3-5 random internal links, the homepage counts as one of the required 4-6 crawl pages
        $uniqueNumbers = range(0, count($internalLinks) - 1);
        shuffle($uniqueNumbers);
        $selectionInternalLinks = array_slice($uniqueNumbers, 0, $numberPages - 1);

        #create array of target urls
        $urlsToTest = [$targetHomepage];
        foreach ($selectionInternalLinks as $key => $link){
            array_push($urlsToTest, $targetHomepage.$internalLinks[$link]);
        }

        foreach ($urlsToTest as $key => $currentLink){
            #get http status code 
            $pageHeader = get_headers($currentLink);
            $httpStatus = substr($pageHeader[0], 9, 3);

            #update crawl session table
            $session = new CrawlSession([
                'hash' => $hash,
                'url' => $currentLink,
                'http_status_code' => $httpStatus
            ]);
            $session->save();

            //get internal and external links of each crawled page
            //find page load time
            $startTime = microtime(TRUE);
            $html = file_get_contents($currentLink);
            array_push($timeLoad, microtime(TRUE) - $startTime);

            #already have homepage internal and external links
            if($currentLink != $targetHomepage){
                $links = getLinks($html);
                foreach ($links[0] as $key => $link){
                    array_push($externalLinks, $link);
                }
                foreach ($links[1] as $key => $link){
                    array_push($internalLinks, $link);
                }
            }

            #get webpage title length
            $document = new \DOMDocument();
            @$document->loadHTMLFile($currentLink);
            $xpath = new \DOMXPath($document);
            $title = $xpath->query('//title')->item(0)->nodeValue."\n";
            array_push($titleLength, strlen($title) - 1);

            #get number of words
            array_push($numberWords, array_sum(array_count_values(str_word_count(strip_tags(strtolower($html)), 1))));

            #get images
            preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matches );
            foreach ($matches[1] as $value)
                array_push($pageImages, $value);
        }
         
        #get unique internal links
        $uniqueInternalLinks = array_unique($internalLinks);
        $uniqueExternalLinks = array_unique($externalLinks);

        #update crawled pages table
        $pages = new CrawledPages([
            'hash' => $hash,
            'number_pages_crawled' => $numberPages,
            'unique_images' => count(array_unique($pageImages)),
            'unique_internal_links' => count(array_unique($internalLinks)),
            'unique_external_links' => count(array_unique($externalLinks)),
            'page_load' => array_sum($timeLoad)/$numberPages,
            'word_count' => array_sum($numberWords)/$numberPages,
            'title_length' => array_sum($titleLength)/$numberPages,
        ]);
        $pages->save();

        #query database for latest crawl session info
        $crawledPages = CrawledPages::all()->sortByDesc('id')->take(1)->toArray();
        $getLatestHash = CrawledPages::latest('id')->first();
        $crawlSession = CrawlSession::all()->where('hash',$getLatestHash->hash)->toArray();

        #return latest session data to welcome page
        return view('welcome', compact('crawledPages'), compact('crawlSession'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
