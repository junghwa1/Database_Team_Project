<?php
// get_songs.php

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

    // Query to get songs for the selected album
    $sql = "SELECT songId, trackNumber, musicTitle, heart, songLength FROM song WHERE albumNumber = '$albumNumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Songs</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Song ID</th><th>Track Number</th><th>Title</th><th>songLength</th><th>Heart</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["songId"] . "</td>";
            echo "<td>" . $row["trackNumber"] . "</td>";
            echo "<td>" . $row["musicTitle"] . "</td>";
            echo "<td>" . $row["songLength"] . "</td>";
            echo "<td>" . $row["heart"] . "</td>";
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
