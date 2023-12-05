<?php
session_start(); // 세션 시작

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // 로그인되어 있지 않으면 로그인 페이지로 이동
    exit();
}

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

// 노래 ID 확인
if (isset($_GET['songId'])) {
    $songId = $_GET['songId'];

    // 앨범과 곡 정보를 가져오는 쿼리
    $songQuery = "SELECT s.musicTitle, a.artist FROM song s
                  INNER JOIN album a ON s.albumNumber = a.albumNumber
                  WHERE s.songId = $songId";
    $songResult = $conn->query($songQuery);

    if ($songResult->num_rows > 0) {
        $songInfo = $songResult->fetch_assoc();
        $musicTitle = $songInfo['musicTitle'];
        $artist = $songInfo['artist'];
    } else {
        echo "Song not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
    $commentText = $_POST['comment_text'];
    $userId = $_SESSION['user_id'];

    //users 테이블로부터 username을 가져오는 쿼리
    $usernameQuery = "SELECT username FROM users WHERE id = $userId";
    $usernameResult = $conn->query($usernameQuery);

    if ($usernameResult->num_rows > 0) {
        $usernameRow = $usernameResult->fetch_assoc();
        $username = $usernameRow['username'];

        $addCommentQuery = "INSERT INTO comment (songId, username, commentText) VALUES ($songId, '$username', '$commentText')";
        if ($conn->query($addCommentQuery) === TRUE) {
            echo "Comment added successfully.";
        } else {
            echo "Error adding comment: " . $conn->error;
        }
    } else {
        echo "Error getting username.";
    }
}

// 댓글 삭제 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_comment'])) {
    $commentIdToDelete = $_POST['delete_comment'];

    // comment 삭제 쿼리
    $deleteCommentQuery = "DELETE FROM comment WHERE commentId = $commentIdToDelete";
    if ($conn->query($deleteCommentQuery) === TRUE) {
        echo "Comment deleted successfully.";
    } else {
        echo "Error deleting comment: " . $conn->error;
    }
}

// 코멘트 조회
$commentQuery = "SELECT commentId, username, commentText, timestamp FROM comment WHERE songId = $songId";
$commentResult = $conn->query($commentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments for <?php echo $musicTitle; ?></title>
    <style>
        body {
            text-align: center;
            margin-top: 50px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
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

        .comment-container {
            margin-top: 20px;
            text-align: left;
        }

        .comment-item {
            border: 1px solid #333;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .comment-item .meta {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Comments for <?php echo $musicTitle; ?> by <?php echo $artist; ?></h1>

    <div class="button-container">
        <a href="index.php">Home</a>
        <a href="comment.php?songId=<?php echo $songId; ?>">Refresh</a>
    </div>

    <?php if ($commentResult->num_rows > 0): ?>
        <div class="comment-container">
            <?php while ($comment = $commentResult->fetch_assoc()): ?>
                <div class="comment-item">
                    <p><?php echo $comment['commentText']; ?></p>
                    <div class="meta">
                        <span>Comment by: <?php echo $comment['username']; ?></span>
                        <span>Posted on: <?php echo $comment['timestamp']; ?></span>
                        <?php if ($comment['username'] === $_SESSION['username']): ?>
                            <!-- 로그인한 사용자가 작성한 댓글에만 삭제 버튼 표시 -->
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?songId=' . $songId; ?>" style="display: inline;">
                                <input type="hidden" name="delete_comment" value="<?php echo $comment['commentId']; ?>">
                                <input type="submit" value="Delete">
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?songId=' . $songId; ?>">
            <label for="comment_text">Add Comment:</label>
            <textarea id="comment_text" name="comment_text" rows="4" cols="50" required></textarea>
            <br>
            <div class="button-container">
                <input type="submit" name="add_comment" value="Add Comment">
            </div>
        </form>
    <?php else: ?>
        <p>Login to add a comment.</p>
    <?php endif; ?>
</body>
</html>
