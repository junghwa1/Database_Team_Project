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
$db_username = "junghwa";
$db_password = "dua6531";
$database = "music_management_system";

$conn = new mysqli($servername, $db_username, $db_password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get songs with artist information
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'songId';
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

// SELECT 문에 사용할 컬럼들을 나열합니다.
$selectColumns = "s.songId, s.albumNumber, s.trackNumber, s.musicTitle, a.artist, s.songLength, s.heart";

// WHERE 조건을 설정합니다.
$whereCondition = "(s.musicTitle LIKE '%$searchKeyword%'
                    OR a.artist LIKE '%$searchKeyword%')";

// ORDER BY 조건을 설정합니다. 이때 정렬 기준을 유지하면서 검색어를 함께 사용합니다.
$orderCondition = "ORDER BY $sort";

// 전체 SQL 쿼리를 조합합니다.
$sql_songs = "SELECT $selectColumns
              FROM song s
              JOIN album a ON s.albumNumber = a.albumNumber
              WHERE $whereCondition
              $orderCondition";

$result_songs = $conn->query($sql_songs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Songs</title>
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

        .search-container {
            margin-top: 20px;
        }

        .search-container label {
            margin-right: 10px;
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

        .searched-results {
            margin-top: 20px;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #333;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
        .right {
            float: right;
            width: 50%;
            margin: 0 auto;
        }

        .playlist-container {
            margin-top: 20px;
        }

        .playlist-container h2 {
            margin-bottom: 10px;
        }

        .playlist-item {
            margin-bottom: 5px;
        }

        .sort-button {
        display: inline-block;
        margin-right: 10px;
        padding: 5px 10px;
        text-decoration: none;
        color: #333;
        border: 1px solid #333;
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }

    .sort-button:hover {
        background-color: #333;
        color: #fff;
    }
    </style>
</head>
<body>
    <div class="top">
        <h1>Music Manager</h1>
    </div>

    <div class="button-container">
    <?php if ($loggedIn): ?>
        <p>User ID: <?php echo $username; ?></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="submit" name="logout" value="Logout">
            <a href="index.php">Home</a>
        </form>
    <?php else: ?>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
    <?php endif; ?>
    </div>

    <div class="left">
            <div class="search-container">
    <form id="search-form" method="get" action="">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <input type="submit" value="Submit">
    </form>
</div>


<div class="searched-results">
    <h2>Song</h2>
    <div style="margin: 10px;">
        <strong>sort:</strong>
        <a class="sort-button" href="?search=<?php echo urlencode($searchKeyword); ?>&sort=songId">Song ID</a>
        <a class="sort-button" href="?search=<?php echo urlencode($searchKeyword); ?>&sort=musicTitle">Music Title</a>
        <a class="sort-button" href="?search=<?php echo urlencode($searchKeyword); ?>&sort=artist">Artist</a>
        <a class="sort-button" href="?search=<?php echo urlencode($searchKeyword); ?>&sort=songLength">Song Length</a>
</div>


    <?php
    // 노래가 있는지 확인
    if ($result_songs->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Song ID</th><th>Music Title</th><th>Artist</th><th>Song Length</th><th>Heart Count</th><th>Action</th></tr>";
        

        // 각 행의 데이터 출력
        while ($row_song = $result_songs->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row_song["songId"] . "</td>";
            echo "<td>" . $row_song["musicTitle"] . "</td>";
            echo "<td>" . $row_song["artist"] . "</td>";
            echo "<td>" . $row_song["songLength"] . "</td>";
            echo "<td>" . $row_song["heart"] . "</td>";
            echo "<td>";
            echo "<button class='add-to-playlist' data-song-id='" . $row_song["songId"] . "'>Add to Playlist</button>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>노래를 찾을 수 없습니다.</p>";
    }
    ?>
</div>
    </div>

    <div class="right">
        <?php if ($loggedIn): ?>
            <div class="playlist-container">
                <h2>Your Playlist</h2>
                <div id="playlist-table-container">
                    <!-- 플레이리스트 테이블이 여기에 동적으로 로드됩니다. -->
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            // 페이지 로드시 플레이리스트 초기 로딩
            loadPlaylist();

            // Add to Playlist 버튼에 대한 클릭 이벤트 리스너 추가
            $(document).on('click', '.add-to-playlist', function () {
                var songIdToAdd = $(this).data('song-id');

                // AJAX를 사용하여 노래를 플레이리스트에 추가
                $.ajax({
                    url: 'add_to_playlist.php',
                    type: 'POST',
                    data: { add_to_playlist: songIdToAdd },
                    success: function (response) {
                        alert(response); // 서버에서 반환한 메시지를 알림으로 표시
                        loadPlaylist(); // 플레이리스트를 다시 로드
                    },
                    error: function () {
                        alert('Failed to add song to playlist.');
                    }
                });
            });

            // 플레이리스트 로드 함수
            function loadPlaylist() {
                $.ajax({
                    url: 'load_playlist.php',
                    type: 'GET',
                    success: function (playlistData) {
                        // 플레이리스트 테이블을 동적으로 업데이트
                        $('#playlist-table-container').html(playlistData);
                    },
                    error: function () {
                        alert('Failed to load playlist.');
                    }
                });
            }

        });
    </script>
</body>
</html>
