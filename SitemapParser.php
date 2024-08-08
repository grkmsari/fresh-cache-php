<?php

class SitemapParser {
    private $urls = [];

    // Fetch the sitemap and extract URLs
    public function fetchSitemap($sitemapUrl) {
        $sitemapContent = file_get_contents($sitemapUrl);
        if ($sitemapContent === false) {
            echo "Failed to fetch the sitemap: $sitemapUrl\n";
            return;
        }

        $xml = simplexml_load_string($sitemapContent);
        if ($xml === false) {
            echo "Failed to parse the sitemap: $sitemapUrl\n";
            return;
        }

        $this->parseSitemap($xml);
    }

    // Parse the sitemap XML and extract URLs
    private function parseSitemap($xml) {
        foreach ($xml->url as $urlElement) {
            $this->urls[] = (string) $urlElement->loc;
        }

        foreach ($xml->sitemap as $sitemapElement) {
            $this->fetchSitemap((string) $sitemapElement->loc);
        }
    }

    // Get the list of URLs
    public function getUrls() {
        return $this->urls;
    }
}
