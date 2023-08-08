<?php
// Connect to the database
include('connect.php');

// Get the book level ID from the query parameter
$book_level_id = $_GET['level_id'];

// Fetch subjects for the selected book level
$sql = "SELECT * FROM subjects WHERE book_level_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $book_level_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch all the subjects as an array
$subjects = [];
while ($row = mysqli_fetch_assoc($result)) {
  $subjects[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Return the subjects as JSON data
echo json_encode($subjects);
?>
