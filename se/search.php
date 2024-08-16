<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Check if the 'flag' parameter is set
if (!isset($_GET['flag'])) {
    echo json_encode(['error' => 'No search query provided']);
    exit;
}

$searchQuery = urlencode($_GET['flag']);

// Google search URL
$url = "https://www.google.com/search?q={$searchQuery}";

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
$response = curl_exec($ch);

if ($response === FALSE) {
    echo json_encode(['error' => 'Failed to fetch search results']);
    exit;
}
curl_close($ch);

// For debugging: output the response HTML
// echo $response;
// exit;

// Load the HTML into DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Disable errors due to invalid HTML
$dom->loadHTML($response);
libxml_clear_errors();

// Create an XPath object
$xpath = new DOMXPath($dom);

// Extract site names and links
$results = [];

// Update the XPath query to match Google's current structure
$nodes = $xpath->query('//div[@class="Gx5Zad fP1Qef xpd EtOod pkphOe"]');

foreach ($nodes as $node) {
    // Extract the site name
    $siteNameNode = $xpath->query('.//h3[@class="zBAuLc l97dzf"]', $node)->item(0);
    $siteName = $siteNameNode ? $siteNameNode->textContent : '';

    // Extract the link
    $linkNode = $xpath->query('.//a[@href]', $node)->item(0);
    $link = $linkNode ? $linkNode->getAttribute('href') : '';

    // Add to results
    if ($siteName && $link) {
        // Clean up the link
        $parsedLink = parse_url($link);
        $cleanLink = isset($parsedLink['scheme']) ? $link : "https://www.google.com" . $link;

        $results[] = ['name' => $siteName, 'link' => $cleanLink];
    }
}

// Return the results as JSON
echo json_encode($results);
?>
