<?php
session_start();

// 로그인 상태 확인
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // 로그인되지 않았다면 홈페이지로 이동
    exit();
}

// Database connection
$servername = "192.168.84.3";
$port = 4567;
$db_username = "junghwa";
$db_password = "dua6531";
$database = "music_management_system";

$conn = new mysqli($servername, $db_username, $db_password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 닉네임 변경 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_nickname'])) {
    $newNickname = $_POST['new_nickname'];
    $userId = $_SESSION['user_id'];  // 사용자 ID를 세션에서 가져옴

    // 닉네임 업데이트 쿼리
    $sql_update_nickname = "UPDATE users SET nickname = '$newNickname' WHERE id = $userId";

    if ($conn->query($sql_update_nickname) === TRUE) {
        echo "Nickname updated successfully";
        $_SESSION['nickname'] = $newNickname;  // 세션에 새로운 닉네임 저장
    } else {
        echo "Error updating nickname: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Nickname</title>
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

        .button-container a, input[type="submit"] {
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
            display: inline-block;
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

        .form-container {
            margin-top: 20px;
        }

        .form-container label {
            margin-right: 10px;
        }

        .form-container input {
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="top">
        <h1>Music Manager</h1>
    </div>

    <div class="button-container">
        <a href="index.php">Home</a>
    </div>

    <div class="form-container">
        <h2>Change Nickname</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="new_nickname">New Nickname:</label>
            <input type="text" id="new_nickname" name="new_nickname" required>
            <input type="submit" name="change_nickname" value="Change Nickname">
        </form>
    </div>
</body>
</html>
