<?php
session_start();
include('../connect.php');
$table = "subjects";

$itemsPerPage = 10; // Number of items per page

$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;

// Get the total number of items in the database
$sql = "SELECT COUNT(*) AS total_items FROM $table";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalItems = $row['total_items'];

$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = max(1, min($currentPage, $totalPages));

if (!is_numeric($_GET['page']) || $_GET['page'] <= 0) {
    header("Location: ?page=1");
    exit;
}

if ($currentPage > $totalPages) {    
    header("Location: ?page=$totalPages");
    exit;
}

$startIndex = ($currentPage - 1) * $itemsPerPage;

// Retrieve items for the current page
$result = mysqli_query($conn, "SELECT s.id, s.subject_name, bl.level_name, bt.type_name, bl.book_type_id 
FROM subjects s INNER JOIN book_levels bl ON s.book_level_id = bl.id INNER JOIN book_types bt ON bl.book_type_id = bt.id LIMIT $startIndex, $itemsPerPage");

$items = mysqli_fetch_all($result, MYSQLI_ASSOC);


include('../connect.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $book_level_id = $_POST['book_level_id'];
    $subject_name = htmlspecialchars($_POST['subject_name']);
        // Insert new subject
        $sql = "INSERT INTO subjects (subject_name, book_level_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $subject_name, $book_level_id);
    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Set session variable for success message
        $_SESSION['create_update_success'] = true;
        // Redirect to the display_subjects page after successful creation/update
        header("Location: display_subjects.php");
        exit; // Important! Ensure the script stops executing after redirection header is sent
    } else {
        echo '<div class="alert alert-danger text-right">خطأ: ' . mysqli_error($conn) . '</div>';
    }
        // Close the prepared statement
        mysqli_stmt_close($stmt);
}
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Subjects CRUD</title>
</head>
<body>
    <div class="container">
        <h1 class="text-right">إدارة المواد الدراسية</h1>
        <?php
        // Check if create_update_success session variable is set
        if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
            echo '<div class="alert alert-success text-right">تم إنشاء/تحديث العنصر بنجاح.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['create_update_success']);
        }
        // Check if delete_success session variable is set
        if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
            echo '<div class="alert alert-success text-right">تم حذف العنصر بنجاح.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['delete_success']);
        }
        // Check if item_not_found session variable is set
        if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
            echo '<div class="alert alert-danger text-right">العنصر غير موجود.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['item_not_found']);
        }
        ?>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars(isset($item['id']) ? $item['id'] : ''); ?>">

            <div class="form-group">
                <label class="text-right">نوع الكتاب:</label>
                <select name="book_type_id" id="book_type"  class="form-control" required>
                <option value="" disabled selected>-- اختر نوع الكتاب --</option>
                    <?php
                    // Database connection configuration
                    include('../connect.php');

                    // Fetch book types for dropdown
                    $bookTypesResult = mysqli_query($conn, "SELECT * FROM book_types");
                    while ($bookType = mysqli_fetch_assoc($bookTypesResult)) {
                        $type_id = htmlspecialchars($bookType["id"]);
                        $type_name = htmlspecialchars($bookType["type_name"]);
                        echo '<option value="' . $type_id . '">' . $type_name . '</option>';
                    }

                    // Close the database connection
                    mysqli_close($conn);
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="text-right">مستوى الكتاب:</label>
                <select name="book_level_id" id="book_level" class="form-control" required>
                    <option value="" disabled selected>-- اختر مستوى الكتاب --</option>
                    <?php
                    // Database connection configuration
                    include('../connect.php');

                    // Fetch book levels for dropdown
                    $bookLevelsResult = mysqli_query($conn, "SELECT * FROM book_levels");
                    while ($bookLevel = mysqli_fetch_assoc($bookLevelsResult)) {
                        $level_id = htmlspecialchars($bookLevel["id"]);
                        $level_name = htmlspecialchars($bookLevel["level_name"]);
                        echo '<option value="' . $level_id . '">' . $level_name . '</option>';
                    }

                    // Close the database connection
                    mysqli_close($conn);
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="text-right">اسم المادة الدراسية:</label>
                <input type="text" name="subject_name" id="subject" class="form-control" value="<?php echo htmlspecialchars(isset($item['subject_name']) ? $item['subject_name'] : ''); ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">حفظ</button>
        </form>
        <hr>
       
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>المعرف</th>
                    <th>اسم المادة الدراسية</th>
                    <th>نوع الكتاب</th>
                    <th>مستوى الكتاب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
    <?php
    // Database connection configuration
    include('../connect.php');

    // Fetch and display items from the database using procedural approach

                                    
    foreach ($items as $item) {
        $id = htmlspecialchars($item["id"]);
        $subject_name = htmlspecialchars($item["subject_name"]);
        $level_name = htmlspecialchars($item["level_name"]);
        $type_name = htmlspecialchars($item["type_name"]);
        $book_type_id = htmlspecialchars($item["book_type_id"]);
        echo "<tr>";
        echo "<td>" . $id . "</td>";
        echo "<td>" . $subject_name . "</td>";
        echo "<td>" . $level_name . "</td>";
        echo "<td>" . $type_name . "</td>"; // Display the book type
        echo '<td>
                <a href="create_update_subject.php?id=' . $id . '&book_type_id=' . $item['book_type_id'] . '" class="btn btn-sm btn-primary">تحرير</a>
                <a href="delete_subject.php?id=' . $id . '" class="btn btn-sm btn-danger">حذف</a>
              </td>';
        echo "</tr>";
    }

    // Close the database connection
    mysqli_close($conn);
    ?>
</tbody>
        </table>
        <div id="pagination-controls" class="text-center mt-4">
        <?php
         include('../pagination.php');
          ?>
        </div>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="script.js"></script>
</body>
</html>
