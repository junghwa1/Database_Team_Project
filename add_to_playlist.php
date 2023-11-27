<?php
session_start();

if (isset($_POST['add_to_playlist']) && isset($_SESSION['user_id'])) {
    $songIdToAdd = $_POST['add_to_playlist'];
    $userId = $_SESSION['user_id'];

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

    // 중복 체크
    $sqlCheckDuplicate = "SELECT id FROM user_playlist WHERE userId = '$userId' AND songId = '$songIdToAdd'";
    $resultCheckDuplicate = $conn->query($sqlCheckDuplicate);

    if ($resultCheckDuplicate->num_rows > 0) {
        // 이미 플레이리스트에 있는 노래인 경우
        echo "Song is already in the playlist.";
    } else {
        // 플레이리스트에 노래 추가 쿼리
        $sqlAddToPlaylist = "INSERT INTO user_playlist (userId, songId) VALUES ('$userId', '$songIdToAdd')";

        if ($conn->query($sqlAddToPlaylist) === TRUE) {
            // 추가가 성공한 경우
            echo "Song added to playlist successfully!";

            
        } else {
            // 추가에 실패한 경우
            echo "Error adding song to playlist: " . $conn->error;
        }
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
