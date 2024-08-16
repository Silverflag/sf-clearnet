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

// Function to check if a table exists in the database
function tableExists($pdo, $table) {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    return $stmt->rowCount() > 0;
}

// Check if the 'tools' table exists, create it if it doesn't
if (!tableExists($pdo, 'tools')) {
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

// Check if the 'users' table exists, create it if it doesn't
if (!tableExists($pdo, 'users')) {
    try {
        $createTable = $pdo->exec("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL
            )
        ");
        if ($createTable !== false) {
            error_log('Created table "users" successfully' . PHP_EOL, 3, $logFile);
        } else {
            error_log('Failed to create table "users". No error returned.' . PHP_EOL, 3, $logFile);
        }
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to create table "users": ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if unable to create the table
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create table "users". Please check server logs for details.']);
        exit();
    }
}

// Check if there are any users in the database, if not, it's the first login
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ($stmt->fetchColumn() == 0) {
    // First time login, create an admin account

    // Get username and password from .env.php
    $adminUsername = ADMIN_USERNAME;
    $adminPassword = ADMIN_PASSWORD;

    try {
        // Hash the password
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

        // Insert the admin user into the database
        $statement = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $statement->execute([$adminUsername, $hashedPassword]);

        // Log the creation of admin user
        error_log('Created admin user successfully' . PHP_EOL, 3, $logFile);
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to create admin user: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if insertion fails
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create admin user. Please check server logs for details.']);
        exit();
    }
}

// API endpoint to set password if not already set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];

    try {
        // Check if the username already exists
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            // Username already exists, return error
            http_response_code(400);
            echo json_encode(['error' => 'Username already exists']);
            exit();
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $statement = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $statement->execute([$username, $hashedPassword]);

        // Return a success message
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Password set successfully']);
    } catch (PDOException $e) {
        // Log the error
        error_log('Failed to set password: ' . $e->getMessage() . PHP_EOL, 3, $logFile);

        // Return an error message if insertion fails
        http_response_code(500);
        echo json_encode(['error' => 'Failed to set password. Please check server logs for details.']);
    }
}

// If user is not logged in, show login form
if (!isset($_SESSION['username'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hidden Management | Silverflag Tools</title>
        <style>
            body {
                background-color: #000;
                color: #fff;
                font-family: 'JetBrains Mono', monospace;
                font-weight: 800;
                margin: 0;
                padding: 20px;
            }

            h1 {
                text-align: center;
                margin-top: 0;
            }

            input[type='text'],
            input[type='password'] {
                width: 100%;
                max-width: 40rem;
                padding: 0.5rem;
                font-family: 'JetBrains Mono', monospace;
                font-weight: 800;
                border-radius: 0.25rem;
                border: 3px solid #9f9;
                color: #fff;
                background-color: #000;
                margin-bottom: 0.5rem;
                transition: border-color 0.25s ease-in-out;
            }

            input[type='text']::placeholder,
            input[type='password']::placeholder {
                color: #fff;
                font-weight: 800;
                opacity: 0.8;
            }

            input[type='text']:focus,
            input[type='password']:focus {
                outline: none;
                border-color: rgb(255, 255, 255);
            }

            input[type='button'] {
                background-color: #9f9;
                color: #000;
                padding: 0.5rem 1.5rem;
                border: none;
                cursor: pointer;
                font-family: 'JetBrains Mono', monospace;
                color: rgb(0, 0, 0);
                font-weight: 800;
                border-radius: 0.25rem;
                transition: background-color 250ms ease-in-out, transform 250ms ease-in-out;
            }

            input[type='button']:hover {
                background-color: #3f3;
                transform: scale(1.05);
            }

            input[type='button']:active {
                background-color: #2c2;
                transform: scale(0.95);
            }

            #container {
                max-width: 40rem;
                margin: 0 auto;
                padding: 20px;
            }
        </style>
    </head>

    <body>
    <h1>Hidden Management | Silverflag Tools</h1>

    <div id="container">
        <input type="text" id="usernameInput" placeholder="Username">
        <input type="password" id="passwordInput" placeholder="Password">
        <input type="button" value="Login" onclick="login()">
    </div>

    <script>
        // Function to handle login
        function login() {
            // Get input values
            let username = document.getElementById('usernameInput').value;
            let password = document.getElementById('passwordInput').value;

            // Make a POST request to check login
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to login');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                alert(data.message);
                // Redirect to hidden management page
                window.location.href = 'hiddenmanagement.php';
            })
            .catch(error => {
                console.error('Error:', error.message);
                alert(error.message || 'Failed to login');
            });
        }
    </script>
</body>

    </html>
<?php
    exit();
}

// If the user is logged in, show the hidden management page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidden Management | Silverflag Tools</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-top: 0;
        }

        input[type='text'],
        input[type='password'] {
            width: 100%;
            max-width: 40rem;
            padding: 0.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            border-radius: 0.25rem;
            border: 3px solid #9f9;
            color: #fff;
            background-color: #000;
            margin-bottom: 0.5rem;
            transition: border-color 0.25s ease-in-out;
        }

        input[type='text']::placeholder,
        input[type='password']::placeholder {
            color: #fff;
            font-weight: 800;
            opacity: 0.8;
        }

        input[type='text']:focus,
        input[type='password']:focus {
            outline: none;
            border-color: rgb(255, 255, 255);
        }

        input[type='button'] {
            background-color: #9f9;
            color: #000;
            padding: 0.5rem 1.5rem;
            border: none;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
            color: rgb(0, 0, 0);
            font-weight: 800;
            border-radius: 0.25rem;
            transition: background-color 250ms ease-in-out, transform 250ms ease-in-out;
        }

        input[type='button']:hover {
            background-color: #3f3;
            transform: scale(1.05);
        }

        input[type='button']:active {
            background-color: #2c2;
            transform: scale(0.95);
        }

        #container {
            max-width: 40rem;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>

<body>
    <h1>Hidden Management | Silverflag Tools</h1>

    <div id="container">
        <h2>Change Password</h2>
        <input type="password" id="newPasswordInput" placeholder="New Password">
        <input type="password" id="confirmPasswordInput" placeholder="Confirm New Password">
        <input type="button" value="Change Password" onclick="changePassword()">
    </div>

    <script>
        // Function to handle password change
        function changePassword() {
            // Get input values
            let newPassword = document.getElementById('newPasswordInput').value;
            let confirmPassword = document.getElementById('confirmPasswordInput').value;

            // Check if passwords match
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            // Make a POST request to change password
            fetch('changepassword.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        password: newPassword
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to change password');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    // Clear input fields
                    document.getElementById('newPasswordInput').value = '';
                    document.getElementById('confirmPasswordInput').value = '';
                })
                .catch(error => {
                    console.error('Error:', error.message);
                    alert('Failed to change password');
                });
        }
    </script>
</body>

</html>