<?php

$config = include('config.php');
include('SitemapParser.php');

$sitemapUrl = $config['sitemap_url'];
$parser = new SitemapParser();
$parser->fetchSitemap($sitemapUrl);

$urls = $parser->getUrls();

$totalUrls = count($urls);

if ($totalUrls > 0) {
    echo "Total URLs retrieved from the sitemap: $totalUrls\n";
    echo "URLs:\n";
//    foreach ($urls as $url) {
//        echo $url . "\n";
//    }
} else {
    echo "No URLs found in the sitemap.\n";
}
