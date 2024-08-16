<?php
session_start();

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

// If the form is submitted, attempt login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        exit();
    }

    $username = $data['username'];
    $password = $data['password'];

    try {
        // Check if the username exists in the database
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, set session
            $_SESSION['username'] = $username;
            echo json_encode(['message' => 'Login successful']);
            exit();
        } else {
            // Username or password is incorrect
            http_response_code(401);
            echo json_encode(['error' => 'Incorrect username or password']);
            exit();
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to login: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if login fails
        http_response_code(500);
        echo json_encode(['error' => 'Failed to login. Please check server logs for details.']);
        exit();
    }
}


// If the form is not submitted directly, return an error
http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
exit();
?>