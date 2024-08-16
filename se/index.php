<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
            padding-top: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input[type=text] {
            padding: 10px;
            width: 70%;
        }
        .search-container button {
            padding: 10px 20px;
            background-color: #1e90ff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .result {
            display: flex;
            align-items: center;
            border: 1px solid #333;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .iframe-container {
            width: 150px;
            height: 150px;
            margin-right: 20px;
            overflow: hidden;
            position: relative;
        }
        .iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .result:hover .iframe-container iframe {
            opacity: 1;
        }
        .result-content {
            flex-grow: 1;
            overflow: hidden;
        }
        .result-content a {
            color: #1e90ff;
            text-decoration: none;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .result-content a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-container">
            <form action="" method="GET">
                <input type="text" placeholder="Enter search query..." name="query">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="results">
            <?php
                if(isset($_GET['query']) && !empty($_GET['query'])) {
                    $searchQuery = $_GET['query'];
                    $apiUrl = 'http://silverflag.net/se/search.php?flag=' . urlencode($searchQuery);

                    // Fetch the search results from the API
                    $response = file_get_contents($apiUrl);
                    if ($response === FALSE) {
                        echo '<p>Error fetching search results.</p>';
                    } else {
                        $results = json_decode($response, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            foreach ($results as $result) {
                                echo '<div class="result">';
                                echo '    <div class="iframe-container">';
                                echo '        <iframe src="' . htmlspecialchars($result['link']) . '"></iframe>';
                                echo '    </div>';
                                echo '    <div class="result-content">';
                                echo '        <a href="' . htmlspecialchars($result['link']) . '" target="_blank">' . htmlspecialchars($result['name']) . '</a>';
                                echo '        <p>' . htmlspecialchars($result['link']) . '</p>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Error parsing search results.</p>';
                        }
                    }
                } else {
                    echo '<p>No search query entered.</p>';
                }
            ?>
        </div>
    </div>
</body>
</html>
