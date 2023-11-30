<?php
//load_playlist.php
session_start();

if (isset($_SESSION['username'])) {
    $loggedIn = true;
    $userId = $_SESSION['user_id'];
    
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

    // Query to get user's playlist
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

    if ($result_playlist->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Music Title</th>
                <th>Artist</th>
                <th>Song Length</th>
                <th>Heart Count</th>
                <th>Action</th>
            </tr>";

        while ($row_playlist = $result_playlist->fetch_assoc()) {
            echo "<tr>
                    <td>{$row_playlist["musicTitle"]}</td>
                    <td>{$row_playlist["artist"]}</td>
                    <td>{$row_playlist["songLength"]}</td>
                    <td>{$row_playlist["heart"]}</td>
                    <td><button class='delete-from-playlist' data-song-id='{$row_playlist["songId"]}' data-playlist-item-id='{$row_playlist["playlistItemId"]}'>Delete</button></td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No songs in your playlist.</p>";
    }
} else {
    echo "User not logged in.";
}
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // Delete from Playlist 버튼에 대한 클릭 이벤트 리스너 추가
        $('.delete-from-playlist').click(function () {
            var songIdToDelete = $(this).data('song-id');
            var playlistItemIdToDelete = $(this).data('playlist-item-id');

            // AJAX를 사용하여 노래를 플레이리스트에서 삭제
            $.ajax({
                url: 'delete_from_playlist.php', // 삭제를 처리하는 PHP 파일의 경로로 수정
                type: 'POST',
                data: { delete_from_playlist: playlistItemIdToDelete },
                success: function (response) {
                    alert(response); // 서버에서 반환한 메시지를 알림으로 표시

                    loadPlaylist();
                },
                error: function () {
                    alert('Failed to delete song from playlist.');
                }
            });
        });
    });
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
</script>
