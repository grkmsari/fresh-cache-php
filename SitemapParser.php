<?php

class SitemapParser {
    private $urls = [];
    private $sitemaps = [];
    private $failedSitemaps = [];
    private $visitedSitemaps = [];
    private $crawlDelay;
    private $logFile;

    public function __construct($crawlDelay = 1, $logFile = null) {
        $this->crawlDelay = $crawlDelay;
        $this->logFile = $logFile;
    }

    // Fetch a sitemap and delegate URL extraction
    // (and recursive sitemap handling) to parseSitemap
    public function fetchSitemap($sitemapUrl) {
        if (isset($this->visitedSitemaps[$sitemapUrl])) {
            return;
        }
        $this->visitedSitemaps[$sitemapUrl] = true;

        $sitemapContent = @file_get_contents($sitemapUrl); // suppress warnings
        if ($sitemapContent === false) {
            echo "Failed to fetch the sitemap: $sitemapUrl\n";
            $this->failedSitemaps[] = $sitemapUrl;
            return;
        }

        $xml = @simplexml_load_string($sitemapContent); // suppress warnings
        if ($xml === false) {
            echo "Failed to parse the sitemap: $sitemapUrl\n";
            $this->failedSitemaps[] = $sitemapUrl;
            return;
        }

        $this->parseSitemap($xml, $sitemapUrl);
    }

    // Parse the sitemap XML and extract URLs
    private function parseSitemap($xml, $sitemapUrl) {
        $urlCount = 0;

        foreach ($xml->url as $urlElement) {
            $this->urls[] = (string) $urlElement->loc;
            $urlCount++;
        }

        $this->sitemaps[$sitemapUrl] = $urlCount;

        foreach ($xml->sitemap as $sitemapElement) {
            $this->fetchSitemap((string) $sitemapElement->loc);
            sleep($this->crawlDelay); // Wait for the specified delay
        }
    }

    // Get the list of URLs
    public function getUrls() {
        return $this->urls;
    }

    // Get the list of sitemaps and their URL counts
    public function getSitemaps() {
        return $this->sitemaps;
    }

    // Get the list of failed sitemaps
    public function getFailedSitemaps() {
        return $this->failedSitemaps;
    }

    // Crawl the URLs and return the status counts
    public function crawlUrls() {
        $successCount = 0;
        $failureCount = 0;
        $failedUrls = [];

        foreach ($this->urls as $url) {
            $response = @file_get_contents($url);
            if ($response !== false) {
                $successCount++;
            } else {
                $failureCount++;
                $failedUrls[] = $url;
            }
            sleep($this->crawlDelay); // Wait for the specified delay
        }

        if ($this->logFile && !empty($failedUrls)) {
            file_put_contents($this->logFile, implode("\n", $failedUrls) . "\n", FILE_APPEND);
        }

        return [
            'success' => $successCount,
            'failure' => $failureCount,
        ];
    }
}
