<?php
session_start();

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

// Login Logic
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginUsername = $_POST["username"];
    $loginPassword = $_POST["password"];

    $sql = "SELECT id, username,nickname, password FROM users WHERE username='$loginUsername'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($loginPassword, $row["password"])) {
            // 로그인 성공 시 세션에 사용자 정보 저장
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nickname'] = $row['nickname'];
            $message = "Login successful!";
            
            // 로그인 성공 시 index.php로 이동
            header("Location: index.php");
            exit(); // 리다이렉트 후 스크립트 종료
        } else {
            $message = "Login failed. Check your username and password.";
        }
    } else {
        $message = "Login failed. Check your username and password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
    <h1>Welcome to the Music Management System!</h1>

    <div class="button-container">
        <a href="index.php">Home</a>
        <a href="signup.php">Sign Up</a>
    </div>

    <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <div class="button-container">
        <input type="submit" value="Login">
    </div>
    </form>

    <div class="message"><?php echo $message; ?></div>
</body>
</html>
