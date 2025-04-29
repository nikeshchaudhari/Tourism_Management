<?php
// Include the configuration file that establishes the database connection
include('includes/config.php');

// Check if the connection was established
if (!isset($dbh)) {
    die("Database connection failed. Please check your config.php file.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updationDate = date("Y-m-d H:i:s");

        try {
            // Using PDO instead of MySQLi
            $stmt = $dbh->prepare("INSERT INTO admin (UserName, Password, updationDate) VALUES (:username, :password, :updateDate)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':updateDate', $updationDate);

            if ($stmt->execute()) {
                $success = "✅ Admin added successfully!";
            } else {
                $error = "❌ Error: " . implode(" ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            $error = "❌ Database error: " . $e->getMessage();
        }
    } else {
        $error = "❗ Both fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <style>
        .success { color: green; }
        .error { color: red; }
        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #2196F3;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Add Admin</h2>
    
    <?php if (isset($success)): ?>
        <p class="success" style="text-align: center;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p class="error" style="text-align: center;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Add Admin</button>
        
        <!-- Add this back to login link -->
        <a href="index.php" class="back-link">Back to Login Page</a>
    </form>
</body>
</html>