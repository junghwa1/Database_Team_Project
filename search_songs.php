<?php
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

// Get search term from AJAX request
$searchTerm = $_GET['searchTerm'];

// Query to search for songs based on the provided search term
$sql_search = "SELECT s.songId, s.albumNumber, s.trackNumber, s.musicTitle, a.artist, s.songLength, s.heart
               FROM song s
               JOIN album a ON s.albumNumber = a.albumNumber
               WHERE s.musicTitle LIKE '%$searchTerm%'
               OR a.artist LIKE '%$searchTerm%'
               OR s.songId LIKE '%$searchTerm%'
               OR s.heart LIKE '%$searchTerm%'
               ORDER BY s.songId";

// Get sort type from AJAX request

$result_search = $conn->query($sql_search);

// Check if there are matching songs
if ($result_search->num_rows > 0) {
    echo "<h2>Search Results</h2>";

    echo "<div class='sort-container'>";
    echo "<button class='sort-button' sort-type='songId'>Sort by Song ID</button>";
    echo "<button class='sort-button' sort-type='musicTitle'>Sort by Music Title</button>";
    echo "<button class='sort-button' sort-type='artist'>Sort by Artist</button>";
    echo "<button class='sort-button' sort-type='heart'>Sort by Heart Count</button>";
    echo "</div>";

    echo "<table class='searched-results'>";
    echo "<tr><th>Song ID</th><th>Music Title</th><th>Artist</th><th>Song Length</th><th>Heart Count</th><th>Action</th></tr>";

    // Output data of each row
    while ($row_search = $result_search->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row_search["songId"] . "</td>";
        echo "<td>" . $row_search["musicTitle"] . "</td>";
        echo "<td>" . $row_search["artist"] . "</td>";
        echo "<td>" . $row_search["songLength"] . "</td>";
        echo "<td>" . $row_search["heart"] . "</td>";
        echo "<td>";
        echo "<button class='add-to-playlist' data-song-id='" . $row_search["songId"] . "'>Add to Playlist</button>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No matching songs found.</p>";
}

// Close the database connection
$conn->close();
?>
<html>
<body>
<script>
    $('.sort-button').click(function () {
    var sortType = $(this).attr('sort-type'); // Change this line
    var searchTerm = '<?php echo $searchTerm; ?>';
    $.ajax({
        url: 'sort_searched_songs.php',
        type: 'GET',
        data: {
            searchTerm: searchTerm,
            sortType: sortType
        },
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                console.error(data.error);
                alert('Failed to sort songs. Check the console for details.');
            } else {
                // 정렬된 노래 목록을 동적으로 로드
                displaySortedResults(data);
            }
        },
        error: function (xhr, status, error) {
            console.error(status, error);
            alert('Failed to sort songs. Check the console for details.');
        }
    });
});
    // 정렬된 결과를 표시하는 함수
    function displaySortedResults(sortedData) {
        var tableHtml = "<table class='searched-results'>";
        tableHtml += "<tr><th>Song ID</th><th>Music Title</th><th>Artist</th><th>Song Length</th><th>Heart Count</th><th>Action</th></tr>";

        // Output data of each row
        for (var i = 0; i < sortedData.length; i++) {
            tableHtml += "<tr>";
            tableHtml += "<td>" + sortedData[i]["songId"] + "</td>";
            tableHtml += "<td>" + sortedData[i]["musicTitle"] + "</td>";
            tableHtml += "<td>" + sortedData[i]["artist"] + "</td>";
            tableHtml += "<td>" + sortedData[i]["songLength"] + "</td>";
            tableHtml += "<td>" + sortedData[i]["heart"] + "</td>";
            tableHtml += "<td>";
            tableHtml += "<button class='add-to-playlist' data-song-id='" + sortedData[i]["songId"] + "'>Add to Playlist</button>";
            tableHtml += "</td>";
            tableHtml += "</tr>";
        }

        tableHtml += "</table>";

        // 정렬된 테이블을 동적으로 로드
        $('.searched-results-container').html(tableHtml);
    }
</script>


</body>
</html>