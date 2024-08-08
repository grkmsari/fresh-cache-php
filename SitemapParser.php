<?php

class SitemapParser {
    private $urls = [];
    private $sitemaps = [];
    private $failedSitemaps = [];
    private $crawlDelay;

    public function __construct($crawlDelay = 1) {
        $this->crawlDelay = $crawlDelay;
    }

    // Fetch the sitemap and extract URLs
    public function fetchSitemap($sitemapUrl) {
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

    // Crawl the URLs and return the status
    public function crawlUrls() {
        $status = [];
        foreach ($this->urls as $url) {
            $response = @file_get_contents($url);
            $status[$url] = $response !== false ? 'Success' : 'Failed';
            sleep($this->crawlDelay); // Wait for the specified delay
        }
        return $status;
    }
}
