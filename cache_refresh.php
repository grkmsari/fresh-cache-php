<?php

$config = include('config.php');
include('SitemapParser.php');

$sitemapUrl = $config['sitemap_url'];
$crawlDelay = $config['crawl_delay'];
$parser = new SitemapParser($crawlDelay);
$parser->fetchSitemap($sitemapUrl);

$urls = $parser->getUrls();
$sitemaps = $parser->getSitemaps();
$failedSitemaps = $parser->getFailedSitemaps();
$crawlStatus = $parser->crawlUrls();

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
    echo "\nURL Crawl Status:\n";
    foreach ($crawlStatus as $url => $status) {
        echo "$url: $status\n";
    }
} else {
    echo "No URLs found in the sitemap.\n";
}

if (!empty($failedSitemaps)) {
    echo "\nFailed to fetch or parse the following sitemaps:\n";
    foreach ($failedSitemaps as $failedSitemap) {
        echo $failedSitemap . "\n";
    }
}
