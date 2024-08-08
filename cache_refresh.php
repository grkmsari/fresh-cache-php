<?php

$config = include('config.php');
include('SitemapParser.php');

$sitemapUrl = $config['sitemap_url'];
$parser = new SitemapParser();
$parser->fetchSitemap($sitemapUrl);

$urls = $parser->getUrls();
$sitemaps = $parser->getSitemaps();

$totalUrls = count($urls);

if ($totalUrls > 0) {
    echo "Total URLs retrieved from the sitemap: $totalUrls\n";
    echo "URLs:\n";
//    foreach ($urls as $url) {
//        echo $url . "\n";
//    }
    echo "\nSitemaps and their URL counts:\n";
    foreach ($sitemaps as $sitemap => $count) {
        echo "$sitemap: $count URLs\n";
    }
} else {
    echo "No URLs found in the sitemap.\n";
}
