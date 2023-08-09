<?php
session_start();

// Database connection configuration
include('../connect.php');

// Initialize variables
$id = isset($_GET['id']) ? $_GET['id'] : '';
$level_name = '';
$book_type_id = '';

// If ID is provided, fetch existing data
if (!empty($id)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM book_levels WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
        $level_name = htmlspecialchars($item['level_name']);
        $book_type_id = $item['book_type_id'];
    } else {
        $_SESSION['item_not_found'] = true;
        // Close the statement result
        mysqli_stmt_close($stmt);
        // Redirect to the display_book_levels page after item not found
        header("Location: display_book_levels.php");
        exit;
    }

    // Close the statement result
    mysqli_stmt_close($stmt);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit']) || isset($_POST['updateData'])) {
        $level_name = htmlspecialchars($_POST['level_name']);
        $book_type_id = $_POST['book_type_id'];

        // Prepare the SQL statement
        if (!empty($id)) {
            $stmt = mysqli_prepare($conn, "UPDATE book_levels SET level_name = ?, book_type_id = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sii", $level_name, $book_type_id, $id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO book_levels (level_name, book_type_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "si", $level_name, $book_type_id);
        }

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Set session variable for success message
            $_SESSION['create_update_success'] = true;
            // Redirect to the display_book_levels page after successful creation/update
            header("Location: display_book_levels.php");
            exit; // Important! Ensure the script stops executing after redirection header is sent
        } else {
            echo '<div class="alert alert-danger text-right">خطأ: ' . mysqli_error($conn) . '</div>';
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    }
}

// Fetch book types for dropdown
$bookTypesResult = mysqli_query($conn, "SELECT * FROM book_types");

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create/Update Book Level</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-right">إنشاء/تحديث مستوى الكتاب</h1>
        <?php
        // Check if create_update_success session variable is set
        if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
            echo '<div class="alert alert-success text-right">تم إنشاء/تحديث العنصر بنجاح.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['create_update_success']);
        }
        // Check if item_not_found session variable is set
        if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
            echo '<div class="alert alert-danger text-right">العنصر غير موجود.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['item_not_found']);
        }
        ?>
        <!-- Form For Edit Data -->
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post">
            <div class="form-group">
                <label class="text-right">اسم مستوى الكتاب:</label>
                <input type="text" name="level_name" class="form-control" value="<?php echo $level_name; ?>" required>
            </div>
            <div class="form-group">
                <label class="text-right">نوع الكتاب:</label>
                <select name="book_type_id" class="form-control" Required>
                <option value="" disabled selected>-- اختر نوع الكتاب --</option>
                    <?php
                    while ($bookType = mysqli_fetch_assoc($bookTypesResult)) {
                        $type_id = htmlspecialchars($bookType["id"]);
                        $type_name = htmlspecialchars($bookType["type_name"]);
                        $selected = ($type_id == $book_type_id) ? 'selected' : '';
                        echo '<option value="' . $type_id . '" ' . $selected . '>' . $type_name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="updateData" class="btn btn-primary">حفظ</button>
        </form>
        <br>
        <a href="display_book_levels.php" class="btn btn-secondary">العودة إلى قائمة مستويات الكتب</a>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
