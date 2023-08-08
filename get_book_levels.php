<?php
// Connect to the database
include('connect.php');

// Get the book type ID from the query parameter
$book_type_id = $_GET['type_id'];

// Fetch book levels for the selected book type
$sql = "SELECT * FROM book_levels WHERE book_type_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $book_type_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch all the book levels as an array
$book_levels = [];
while ($row = mysqli_fetch_assoc($result)) {
  $book_levels[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Return the book levels as JSON data
echo json_encode($book_levels);
?>
