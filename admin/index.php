<?php
session_start();
include('includes/config.php');

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            // Get user from database
            $sql = "SELECT id, UserName, Password FROM admin WHERE UserName = :username";
            $query = $dbh->prepare($sql);
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_OBJ);

            // Verify password
            if ($user && password_verify($password, $user->Password)) {
                $_SESSION['alogin'] = $user->id; // Store user ID instead of username
                $_SESSION['admin_username'] = $user->UserName;
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Tourism Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-green-600">Admin Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="block mb-1 font-semibold text-gray-700">Username</label>
                <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block mb-1 font-semibold text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex justify-between items-center text-sm">
                <a href="forgot-password.php" class="text-black hover:underline">Forgot Password?</a>
                <a href="../index.php" class="text-black hover:underline">Back to Home</a>
            </div>

            <div>
                <button type="submit" name="login" class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-grey-700 cursor-pointer">
                    Sign In
                </button>
            </div>

            <div class="mt-4 text-center">
                <a href="register.php" class="text-black-600 hover:underline">Don't have an account? Register as Admin</a>
            </div>
        </form>
    </div>

</body>
</html>