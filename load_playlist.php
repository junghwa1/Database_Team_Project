<?php
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
            </tr>";

        while ($row_playlist = $result_playlist->fetch_assoc()) {
            echo "<tr>
                    <td>{$row_playlist["musicTitle"]}</td>
                    <td>{$row_playlist["artist"]}</td>
                    <td>{$row_playlist["songLength"]}</td>
                    <td>{$row_playlist["heart"]}</td>
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
