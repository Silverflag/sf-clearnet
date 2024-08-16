<?php
include '.env.php'; // Include the configuration file

// Define log file path
$logFile = __DIR__ . '/server.log';

// Connect to MySQL database using environment variables
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log the error
    error_log('Failed to connect to database: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

    // Return an error message if unable to connect to the database
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to database. Please check server logs for details.']);
    exit();
}

// Check if the 'tools' table exists, create it if it doesn't
$tableCheck = $pdo->query("SHOW TABLES LIKE 'tools'");
if ($tableCheck->rowCount() == 0) {
    try {
        $createTable = $pdo->exec("
            CREATE TABLE tools (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                link VARCHAR(255) NOT NULL
            )
        ");
        if ($createTable !== false) {
            error_log('Created table "tools" successfully' . PHP_EOL, 3, $logFile);
        } else {
            error_log('Failed to create table "tools". No error returned.' . PHP_EOL, 3, $logFile);
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to create table "tools": ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if unable to create the table
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create table "tools". Please check server logs for details.']);
        exit();
    }
}

// API endpoint to get all tools
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $statement = $pdo->query('SELECT * FROM tools');
        $tools = $statement->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($tools);
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to fetch tools: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if unable to fetch tools
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch tools. Please check server logs for details.']);
    }
}

// API endpoint to add a new tool
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];
    $description = $data['description'];
    $link = $data['link'];

    try {
        // Insert the new tool into the database
        $statement = $pdo->prepare('INSERT INTO tools (name, description, link) VALUES (?, ?, ?)');
        $statement->execute([$name, $description, $link]);

        // Return a success message
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Tool added successfully']);
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to add tool: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if insertion fails
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add tool. Please check server logs for details.']);
    }
}

// API endpoint to delete a tool
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    try {
        // Delete the tool from the database
        $statement = $pdo->prepare('DELETE FROM tools WHERE id = ?');
        $statement->execute([$id]);

        // Return a success message
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Tool deleted successfully']);
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to delete tool: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if deletion fails
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete tool. Please check server logs for details.']);
    }
}

?>
