<?php
include('../connect.php');

if (isset($_GET['book_type_id'])) {
    $bookTypeId = $_GET['book_type_id'];

    $query = "SELECT * FROM book_levels WHERE book_type_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $bookTypeId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $bookLevels = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookLevels[] = [
            'id' => $row['id'],
            'level_name' => $row['level_name']
        ];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode($bookLevels);
}
?>
