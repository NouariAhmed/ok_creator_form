<?php
session_start();
ob_start(); // Start output buffering
include('secure.php');
include('header.php');
?>
    <div class="container-fluid py-4">
      <?php
      if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
        // Unset the session variable to avoid displaying the message on page refresh
        unset($_SESSION['create_update_success']);
        // Redirect to the display_book_levels page with a success message
        header("Location: display_book_levels.php?create_update_success=1");
        exit;
    }
    // Check if the item_not_found session variable is set
    if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
        // Unset the session variable to avoid displaying the message on page refresh
        unset($_SESSION['item_not_found']);
        // Redirect to the display_book_levels page with a success message
        header("Location: display_book_levels.php?item_not_found=1");
        exit;
    }
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
            if (isset($_POST['updateData'])) {
                // Validate user input
                $level_name = htmlspecialchars($_POST['level_name']);
                $book_type_id = $_POST['book_type_id'];

                // Prepare and execute SQL query
                $stmt = mysqli_prepare($conn, "UPDATE book_levels SET level_name = ?, book_type_id = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "sii", $level_name, $book_type_id, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['create_update_success'] = true;
                    header("Location: display_book_levels.php");
                    exit;
                } else {
                    echo "<div class='alert alert-danger text-right'>حدث خطأ أثناء الإضافة</div>";
                }
                mysqli_stmt_close($stmt);
            }
        }
        // Fetch book types for dropdown
        $bookTypesResult = mysqli_query($conn, "SELECT * FROM book_types");

        ?>
          <form role="form" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post">
              <h4 class="mb-3">تحديث مستوى كتاب</h4>
              <div class="input-group input-group-outline my-3">
                <select name="book_type_id" class="form-control" required>
                <option value="" disabled selected> -- اختر نوع الكتاب -- </option>
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
                 <div class="form-group">
                  <label class="form-label">اسم مستوى الكتاب:</label>
                  <input type="text" name="level_name" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($level_name); ?>" required>
              </div>
              <div class="form-group mt-3">
                  <button type="submit" name="updateData" class="btn btn-primary">Update</button>
              </div>
          </form>
          <hr>
          <a href="display_book_levels.php" class="btn btn-secondary">العودة إلى قائمة مستويات الكتب</a>
<?php
     // Close the database connection
     mysqli_close($conn);
include('footer.php');
ob_end_flush();
?>