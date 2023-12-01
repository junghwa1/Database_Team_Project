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

// 소팅 타입 가져오기

if (isset($_GET['sortType'])) {
    $sortType = $_GET['sortType'];

    // Query to sort searched songs based on the selected type
    $sql_sort = "SELECT s.songId, s.albumNumber, s.trackNumber, s.musicTitle, a.artist, s.songLength, s.heart
                FROM song s
                JOIN album a ON s.albumNumber = a.albumNumber
                WHERE s.musicTitle LIKE ? OR a.artist LIKE ?
                ORDER BY ";

    switch ($sortType) {
        case 'songId':
            $sql_sort .= "s.songId";
            break;
        case 'title':
            $sql_sort .= "s.musicTitle";
            break;
        case 'artist':
            $sql_sort .= "a.artist";
            break;
        default:
            // 기본적으로 Song ID로 정렬
            $sql_sort .= "s.songId";
            break;
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare($sql_sort);
    $searchTerm = '%' . $_GET['searchTerm'] . '%';
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result_sort = $stmt->get_result();

    if ($result_sort->num_rows > 0) {
        $sortedData = array();
    
        while ($row_sort = $result_sort->fetch_assoc()) {
            $sortedData[] = $row_sort;
        }
        header('Content-Type: application/json');
        
        echo json_encode($sortedData);
    } else {
        echo json_encode(array('error' => 'No songs found.'));
    }

    $stmt->close();
}
$conn->close();
?>
