<?php

$config = include('config.php');
include('SitemapParser.php');

// Determine the mode (CLI or Web)
$isCLI = php_sapi_name() === 'cli';
$isCountOnly = false;

// CLI mode
if ($isCLI) {
    $isCountOnly = in_array('--count', $argv);
} else {
    // Web mode (check for GET parameter)
    $isCountOnly = isset($_GET['count']) && $_GET['count'] === '1';
}

// Capture output to save to file later
ob_start();

$sitemapUrl = $config['sitemap_url'];
$crawlDelay = $config['crawl_delay'];
$logFile = $config['log_file'];
$outputFile = $config['output_file'];

$parser = new SitemapParser($crawlDelay, $logFile);
$parser->fetchSitemap($sitemapUrl);

$urls = $parser->getUrls();
$sitemaps = $parser->getSitemaps();
$failedSitemaps = $parser->getFailedSitemaps();

if (!$isCountOnly) {
    $crawlStatus = $parser->crawlUrls();
}

$totalUrls = count($urls);

if ($totalUrls > 0) {
    echo "Total URLs retrieved from the sitemap: $totalUrls\n";
    echo "\nSitemaps and their URL counts:\n";
    foreach ($sitemaps as $sitemap => $count) {
        echo "$sitemap: $count URLs\n";
    }
    if (!$isCountOnly) {
        echo "\nCrawl Summary:\n";
        echo "Successful crawls: " . $crawlStatus['success'] . "\n";
        echo "Failed crawls: " . $crawlStatus['failure'] . "\n";
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

// Save output to file
$output = ob_get_contents();
ob_end_clean();

if ($outputFile) {
    file_put_contents($outputFile, $output);
}

echo $output;
