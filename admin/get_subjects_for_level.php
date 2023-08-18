<?php
include('../connect.php');

if (isset($_GET['book_level_id'])) {
    $bookLevelId = $_GET['book_level_id'];

    $query = "SELECT * FROM subjects WHERE book_level_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $bookLevelId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $subjects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = [
            'id' => $row['id'],
            'subject_name' => $row['subject_name']
        ];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode($subjects);
}
?>
