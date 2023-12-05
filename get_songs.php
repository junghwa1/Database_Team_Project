<html>
    <head>
    <link rel="stylesheet" href="styles.css">
</head>
    </html>
<?php
session_start(); // 세션 시작

if (isset($_GET['albumNumber'])) {
    $albumNumber = $_GET['albumNumber'];

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

    // 특정 앨범의 곡을 가져오는 쿼리
    $sql = "SELECT songId, trackNumber, musicTitle, heart, songLength FROM song WHERE albumNumber = '$albumNumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Songs</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Song ID</th><th>Track Number</th><th>Title</th><th>songLength</th><th>Heart</th><th>Action</th><th></th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["songId"] . "</td>";
            echo "<td>" . $row["trackNumber"] . "</td>";
            echo "<td>" . $row["musicTitle"] . "</td>";
            echo "<td>" . $row["songLength"] . "</td>";
            echo "<td>" . $row["heart"] . "</td>";
            echo "<td>";
            
            // 플레이리스트에 추가하는 버튼
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                $songIdToAdd = $row["songId"];
                $checkSongQuery = "SELECT * FROM user_playlist WHERE userId = $userId AND songId = $songIdToAdd";
                $checkSongResult = $conn->query($checkSongQuery);
                

                echo "<button class='add-to-playlist' data-song-id='" . $row["songId"] . "'>Add to Playlist</button>";

                echo "<td>";
                // View Comment 버튼
                echo "<button class='view-comment' data-song-id='" . $row["songId"] . "'>View Comment</button>";
            } else {
                echo "<span>Login to add to Playlist</span>";
            }

            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No songs found for the selected album.</p>";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // View Comment 버튼에 대한 클릭 이벤트 리스너 추가
        $('.view-comment').click(function () {
            var songIdToView = $(this).data('song-id');
            
            // 노래의 comment.php로 이동
            window.location.href = 'comment.php?songId=' + songIdToView;
        });
    });
</script>