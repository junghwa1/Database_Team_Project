<?php
//delete_from_playlist
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_from_playlist'])) {
    // 사용자가 로그인한 상태인지 확인
    if (isset($_SESSION['username'])) {
        // 사용자 ID 및 삭제할 플레이리스트 항목 ID 가져오기
        $userId = $_SESSION['user_id'];
        $playlistItemIdToDelete = $_POST['delete_from_playlist'];

        // 데이터베이스 연결 설정
        $servername = "192.168.84.3";
        $port = 4567;
        $db_username = "junghwa";
        $db_password = "dua6531";
        $database = "music_management_system";

        $conn = new mysqli($servername, $db_username, $db_password, $database, $port);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // 플레이리스트에서 노래 삭제 쿼리 실행
        $sql_delete_playlist_item = "DELETE FROM user_playlist WHERE id = $playlistItemIdToDelete AND userId = $userId";
        $result_delete_playlist_item = $conn->query($sql_delete_playlist_item);

        if ($result_delete_playlist_item) {
            echo "Song deleted from playlist successfully.";
        } else {
            echo "Failed to delete song from playlist.";
        }

        // 데이터베이스 연결 닫기
        $conn->close();
    } else {
        echo "User not logged in.";
    }
} else {
    echo "Invalid request.";
}
?>
