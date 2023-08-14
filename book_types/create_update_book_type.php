<?php
session_start();
// Check if the create_update_success session variable is set
if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
    // Unset the session variable to avoid displaying the message on page refresh
    unset($_SESSION['create_update_success']);
    // Redirect to the display_book_types page with a success message
    header("Location: display_book_types.php?create_update_success=1");
    exit;
}
// Check if the item_not_found session variable is set
if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
    // Unset the session variable to avoid displaying the message on page refresh
    unset($_SESSION['item_not_found']);
    // Redirect to the display_book_types page with a success message
    header("Location: display_book_types.php?item_not_found=1");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create/Update Book Type</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-right">إنشاء/تحديث العنصر</h1>
        <?php
        // Database connection configuration
        include('../connect.php');
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $type_name = '';

        if (!empty($id)) {
            $stmt = mysqli_prepare($conn, "SELECT * FROM book_types WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        
            if (mysqli_num_rows($result) > 0) {
                $item = mysqli_fetch_assoc($result);
                $type_name = htmlspecialchars($item['type_name']);
            } else {
                $_SESSION['item_not_found'] = true;
                // Close the statement result
                mysqli_stmt_close($stmt);
                // Redirect to the display_book_types page after item not found
                header("Location: display_book_types.php");
                exit;
            }
        
            // Close the statement result
            mysqli_stmt_close($stmt);
        }
       

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateData'])) {
                // Validate user input
                $type_name = htmlspecialchars($_POST['type_name']);
                if (empty($type_name)) {
                    echo "<div class='alert alert-danger text-right'>عنوان الكتاب مطلوب</div>";
                } else {
                    // Prepare and execute SQL query
                    $stmt = mysqli_prepare($conn, "UPDATE book_types SET type_name = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $type_name, $id);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['create_update_success'] = true;
                        header("Location: display_book_types.php");
                        exit;
                    } else {
                        $error_message = "حدث خطأ أثناء التحديث: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
        // Close the database connection
        mysqli_close($conn);
        ?>
        <!-- Form For Edit Data-->
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post">
           
            <div class="form-group">
                <label for="name" class="text-right">عنوان الكتاب:</label>
                <input type="text" name="type_name" class="form-control" value="<?php echo $type_name; ?>" required>
            </div>
            <button type="submit" name="updateData" class="btn btn-primary">حفظ</button>
        </form>
        <br>
        <a href="display_book_types.php" class="btn btn-secondary">العودة إلى قائمة العناصر</a>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>