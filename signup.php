<?php
// Database connection
$servername = "192.168.84.3";
$port = 4567;
$username = "junghwa";
$password = "dua6531";
$database = "music_management_system";

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sign Up Logic
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $signupUsername = $_POST["username"];
    $signupPassword = $_POST["password"];
    $signupNickname = $_POST["nickname"];

    // Check if username already exists
    $checkUsernameQuery = "SELECT id FROM users WHERE username='$signupUsername'";
    $checkUsernameResult = $conn->query($checkUsernameQuery);

    if ($checkUsernameResult->num_rows > 0) {
        $message = "Username already exists. Please choose another.";
    } else {
        // Insert new user
        $hashedPassword = password_hash($signupPassword, PASSWORD_DEFAULT);
        $insertUserQuery = "INSERT INTO users (username, password, nickname) VALUES ('$signupUsername', '$hashedPassword', '$signupNickname')";
        if ($conn->query($insertUserQuery) === TRUE) {
            $message = "Sign up successful!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            text-align: center;
            margin-top: 50px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button-container a,input[type="submit"] {
            margin: 0 10px;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border: 1px solid #333;
            border-radius: 5px;
            color: #333;
            background-color: #fff;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .button-container a:hover, input[type="submit"]:hover {
            background-color: #333;
            color: #fff;
        }

        .message {
            color: #ff0000;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="top">
        <h1>music manager</h1>
    </div>

    <div class="button-container">
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </div>

    <h2>Sign Up</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="nickname">Nickname:</label>
        <input type="text" id="nickname" name="nickname" required>
        <br>
        <div class="button-container">
            <input type="submit" value="Sign Up">
        </div>
    </form>

    <div class="message"><?php echo $message; ?></div>
</body>
</html>
