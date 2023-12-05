<?php
session_start();

// 로그인 상태 확인
if (isset($_SESSION['username'])) {
    $loggedIn = true;
    $username = $_SESSION['username'];
    $nickname = $_SESSION['nickname'];
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

// 앨범 정보를 가져오기 위한 쿼리
$sql_album = "SELECT albumNumber, albumTitle, artist, releaseDate FROM album";
$result_album = $conn->query($sql_album);

// 사용자의 플레이리스트를 가져오기 위한 쿼리
if ($loggedIn) {
    $userId = $_SESSION['user_id'];  // 세션에 사용자 ID를 저장함
    $sql_playlist = "SELECT
                        up.id AS playlistItemId,
                        up.userId,
                        up.songId,
                        s.albumNumber,
                        s.trackNumber,
                        s.musicTitle,
                        s.heart,
                        s.songLength,
                        a.artist
                    FROM
                        user_playlist up
                    JOIN song s ON up.songId = s.songId
                    JOIN album a ON s.albumNumber = a.albumNumber
                    WHERE up.userId = $userId";

    $result_playlist = $conn->query($sql_playlist);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<script>
    $(document).ready(function () {
        // 페이지 로드시 플레이리스트 초기 로딩
        loadPlaylist();

        // 앨범 버튼을 클릭하면 노래 목록을 동적으로 로드
        $('.album-button').click(function () {
            var albumNumber = $(this).data('album-number');

            $.ajax({
                url: 'get_songs.php',
                type: 'GET',
                data: { albumNumber: albumNumber },
                success: function (data) {
                    $('.song-list-container').html(data);

                    // Add to Playlist 버튼에 대한 클릭 이벤트 리스너 추가
                    $('.add-to-playlist').click(function () {
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
                },
                error: function () {
                    alert('Failed to load songs.');
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
</head>
<body>
    <div class="top">
        <h1>music manager</h1>
    </div>

    <div class="button-container">
        <?php if ($loggedIn): ?>
            <div class="info">
            <p>User ID: <?php echo $username; ?></p>
            <p>Nick Name: <?php echo $nickname; ?></p>
        </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="submit" name="logout" value="Logout">
                <a href="change_nickname.php">Change Nickname</a>
                <a href="search.php">search</a>
            </form>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </div>
    <div class="middle">
    <div class="left">
        <?php
        // 앨범이 있는지 확인
        if ($result_album->num_rows > 0) {
            echo "<h2>Albums</h2>";
            echo "<table>";
            echo "<tr><th>Album Number</th><th>Album Title</th><th>Artist</th><th>Release Date</th><th>Action</th></tr>";

            // 각 행의 데이터 출력
            while ($row_album = $result_album->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row_album["albumNumber"] . "</td>";
                echo "<td>" . $row_album["albumTitle"] . "</td>";
                echo "<td>" . $row_album["artist"] . "</td>";
                echo "<td>" . $row_album["releaseDate"] . "</td>";
                echo "<td><button class='album-button' data-album-number='" . $row_album["albumNumber"] . "'>View details</button></td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No albums found.</p>";
        }
        ?>

        <div class="song-list-container">
            <!-- 노래 목록이 여기에 동적으로 로드 -->
        </div>
    </div>

    <div class="right">
    <?php if ($loggedIn): ?>
        <div class="playlist-container">
            <h2>Your Playlist</h2>
            <div id="playlist-table-container">
                <!-- 플레이리스트 테이블이 여기에 동적으로 로드 -->
            </div>
        </div>
    <?php endif; ?>
</div>
    </div>
</body>
</html>
