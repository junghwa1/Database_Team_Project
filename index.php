<?php
session_start();

// 로그인 상태 확인
if (isset($_SESSION['username'])) {
    $loggedIn = true;
    $username = $_SESSION['username'];
} else {
    $loggedIn = false;
}

// 로그아웃 처리
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

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

// Query to get album information
$sql = "SELECT albumNumber, albumTitle, artist, releaseDate FROM album";
$result = $conn->query($sql);
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

        .left {
            float: left;
            width: 50%;
            margin: 0 auto; /* 가운데 정렬을 위한 스타일 추가 */
        }

        table {
            margin: 0 auto; /* 수정: 테이블을 가운데 정렬 */
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #333;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            // 앨범을 클릭하면 노래 목록을 동적으로 로드
            $('.album-row').click(function () {
                var albumNumber = $(this).data('album-number');

                $.ajax({
                    url: 'get_songs.php',
                    type: 'GET',
                    data: { albumNumber: albumNumber },
                    success: function (data) {
                        $('.song-list-container').html(data);
                    },
                    error: function () {
                        alert('Failed to load songs.');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="top">
        <h1>music manager</h1>
    </div>

    <div class="button-container">
        <?php if ($loggedIn): ?>
            <p>User ID: <?php echo $username; ?></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="submit" name="logout" value="Logout">
            </form>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </div>
<div class="left">
    <?php
    // Check if there are albums
    if ($result->num_rows > 0) {
        echo "<h2>Albums</h2>";
        echo "<table>";
        echo "<tr><th>Album Number</th><th>Album Title</th><th>Artist</th><th>Release Date</th></tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr class='album-row' data-album-number='" . $row["albumNumber"] . "'>";
            echo "<td>" . $row["albumNumber"] . "</td>";
            echo "<td>" . $row["albumTitle"] . "</td>";
            echo "<td>" . $row["artist"] . "</td>";
            echo "<td>" . $row["releaseDate"] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No albums found.</p>";
    }

    $conn->close();
    ?>

    <div class="song-list-container">
        <!-- 노래 목록이 여기에 동적으로 로드됩니다. -->
    </div>
</div>
</body>
</html>
