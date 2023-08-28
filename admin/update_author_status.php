<?php
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $authorId = mysqli_real_escape_string($conn, $_POST["author_id"]);
    $newStatus = mysqli_real_escape_string($conn, $_POST["new_status"]);

    // Update status in the database
    $sql = "UPDATE authors SET author_status = '$newStatus' WHERE id = $authorId";

    $success = false;
    if (mysqli_query($conn, $sql)) {
        $success = true;
    }

    mysqli_close($conn);

    // Return a JSON response
    echo json_encode(["success" => $success]);
}
?>
