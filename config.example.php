<?php
return [
    'sitemap_url' => 'http://yourwebsite.com/sitemap.xml', // Sitemap URL
    'crawl_delay' => 1, // Delay between each URL fetch in seconds
    'log_file' => __DIR__ . '/crawl_log.txt', // Log file for failed URLs
    'output_file' => __DIR__ . '/output_log.txt', // File to save script output
];
