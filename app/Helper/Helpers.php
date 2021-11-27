<?php

namespace App\Helpers;

#gets internal and external links from $html
function getLinks($html) {
    preg_match_all( '|<a.*?href=[\'"](.*?)[\'"].*?>|i',$html, $linkMatches );
    $tempArray = [];
    $externalLinks = [];
    $internalLinks = [];
    foreach ($linkMatches[1] as $link)
        array_push($tempArray, $link);
    $uniqueLinks = array_unique($tempArray);
    foreach ($uniqueLinks as $link){
        if(str_starts_with($link, '/')) {
            array_push($internalLinks, $link);
        } else {
            array_push($externalLinks, $link);
        }
    }
    return [$externalLinks, $internalLinks];
}
