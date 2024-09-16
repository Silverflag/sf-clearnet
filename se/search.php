<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Function to perform a search
function performSearch($query, $engine) {
    $url = ($engine === 'google') 
        ? "https://www.google.com/search?q=" . urlencode($query)
        : "https://www.bing.com/search?q=" . urlencode($query);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

// Function to parse search results
function parseResults($html, $engine) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $results = [];

    if ($engine === 'google') {
        $nodes = $xpath->query('//div[@class="Gx5Zad fP1Qef xpd EtOod pkphOe"]');
        foreach ($nodes as $node) {
            $siteNameNode = $xpath->query('.//h3[@class="zBAuLc l97dzf"]', $node)->item(0);
            $linkNode = $xpath->query('.//a[@href]', $node)->item(0);
            
            if ($siteNameNode && $linkNode) {
                $siteName = $siteNameNode->textContent;
                $link = $linkNode->getAttribute('href');
                $parsedLink = parse_url($link);
                $cleanLink = isset($parsedLink['scheme']) ? $link : "https://www.google.com" . $link;
                $results[] = ['name' => $siteName, 'link' => $cleanLink];
            }
        }
    } else {
        $nodes = $xpath->query('//li[@class="b_algo"]');
        foreach ($nodes as $node) {
            $siteNameNode = $xpath->query('.//h2', $node)->item(0);
            $linkNode = $xpath->query('.//a[@href]', $node)->item(0);
            
            if ($siteNameNode && $linkNode) {
                $siteName = $siteNameNode->textContent;
                $link = $linkNode->getAttribute('href');
                $results[] = ['name' => $siteName, 'link' => $link];
            }
        }
    }

    return $results;
}

// Function to remove duplicate links
function removeDuplicates($results) {
    $uniqueResults = [];
    $seenLinks = [];

    foreach ($results as $result) {
        if (!in_array($result['link'], $seenLinks)) {
            $uniqueResults[] = $result;
            $seenLinks[] = $result['link'];
        }
    }

    return $uniqueResults;
}

// Function to cache search results
function cacheResults($query, $results) {
    $cacheDir = 'cache';
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }

    $cacheFile = $cacheDir . '/' . md5($query) . '.json';
    $cacheData = [
        'timestamp' => time(),
        'results' => $results
    ];

    file_put_contents($cacheFile, json_encode($cacheData));
}

// Function to get cached results
function getCachedResults($query) {
    $cacheFile = 'cache/' . md5($query) . '.json';
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        $cacheAge = time() - $cacheData['timestamp'];
        
        // If cache is less than a month old, return cached results
        if ($cacheAge < 30 * 24 * 60 * 60) {
            return $cacheData['results'];
        }
    }
    return null;
}

// Main execution
if (!isset($_GET['flag'])) {
    echo json_encode(['error' => 'No search query provided']);
    exit;
}

$searchQuery = $_GET['flag'];

// Check cache first
$cachedResults = getCachedResults($searchQuery);
if ($cachedResults !== null) {
    echo json_encode($cachedResults);
    exit;
}

// Perform searches
$googleHtml = performSearch($searchQuery, 'google');
$bingHtml = performSearch($searchQuery, 'bing');

$googleResults = parseResults($googleHtml, 'google');
$bingResults = parseResults($bingHtml, 'bing');

// Combine and remove duplicates
$allResults = array_merge($googleResults, $bingResults);
$uniqueResults = removeDuplicates($allResults);

// Cache the results
cacheResults($searchQuery, $uniqueResults);

// Output results
echo json_encode($uniqueResults);

// Log the search term
file_put_contents('searches.txt', $searchQuery . PHP_EOL, FILE_APPEND);
?>
