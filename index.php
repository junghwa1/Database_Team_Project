<?php
session_start();

// 로그인 상태 확인
if (isset($_SESSION['username'])) {
    $loggedIn = true;
    $username = $_SESSION['username'];

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

    // Query to get user's playlist information
    $playlistSql = "SELECT playlistId, playlistTitle FROM playlist WHERE username = '$username'";
    $playlistResult = $conn->query($playlistSql);

    // Close the database connection
    $conn->close();
} else {
    $loggedIn = false;
}
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
            display: flex;
            justify-content: space-between; /* 왼쪽과 오른쪽을 각각의 끝으로 정렬 */
        }

        .left, .right {
            width: 48%; /* 수정: 각 영역의 너비를 조정 */
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

        table {
            width: 100%;
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
        ?>
        <div class="song-list-container">
            <!-- 노래 목록이 여기에 동적으로 로드됩니다. -->
        </div>
    </div>

    <div class="right">
        <?php if ($loggedIn): ?>
            <h2>User Playlists</h2>
            <?php
            if ($playlistResult->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Playlist ID</th><th>Playlist Title</th></tr>";

                while ($playlistRow = $playlistResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $playlistRow["playlistId"] . "</td>";
                    echo "<td>" . $playlistRow["playlistTitle"] . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>No playlists found for the user.</p>";
            }
            ?>
        <?php endif; ?>
    </div>

</body>
</html>
