<?php
session_start();
ob_start(); // Start output buffering
include('header.php');
?>
    <div class="container-fluid py-4">
      <?php
      if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
        // Unset the session variable to avoid displaying the message on page refresh
        unset($_SESSION['create_update_success']);
        // Redirect to the display_subjects page with a success message
        header("Location: display_subjects.php?create_update_success=1");
        exit;
    }
    // Check if the item_not_found session variable is set
    if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
        // Unset the session variable to avoid displaying the message on page refresh
        unset($_SESSION['item_not_found']);
        // Redirect to the display_subjects page with a success message
        header("Location: display_subjects.php?item_not_found=1");
        exit;
    }
     // Database connection configuration
     include('../connect.php');
      // Initialize variables
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $subject_name = '';
        $book_level_id = '';
        $book_type_id = isset($_GET['book_type_id']) ? $_GET['book_type_id'] : '';

        // If ID is provided, fetch existing data
        if (!empty($id)) {
            $stmt = mysqli_prepare($conn, "SELECT * FROM subjects WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $item = mysqli_fetch_assoc($result);
                $subject_name = htmlspecialchars($item['subject_name']);
                $book_level_id = $item['book_level_id'];
            } else {
                $_SESSION['item_not_found'] = true;
                // Close the statement result
                mysqli_stmt_close($stmt);
                // Redirect to the display_subjects page after item not found
                header("Location: display_subjects.php");
                exit;
            }

            // Close the statement result
            mysqli_stmt_close($stmt);
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateData'])) {
                $subject_name = htmlspecialchars($_POST['subject_name']);
                $book_level_id = $_POST['book_level_id'];
                // Prepare the SQL statement
                    $stmt = mysqli_prepare($conn, "UPDATE subjects SET subject_name = ?, book_level_id = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "sii", $subject_name, $book_level_id, $id);

                // Execute the prepared statement
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
             ?>
            <form role="form" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post">
              <h4 class="mb-3">تحديث مادة </h4>
              <div class="input-group input-group-outline my-3">
              <select name="book_type_id" id="book_type" class="form-control" required>
                <option value="" disabled> -- اختر نوع الكتاب -- </option>
                    <?php
                       // Fetch book types for dropdown
                    $bookTypesResult = mysqli_query($conn, "SELECT * FROM book_types");
                    while ($bookType = mysqli_fetch_assoc($bookTypesResult)) {
                        $type_id = htmlspecialchars($bookType["id"]);
                        $type_name = htmlspecialchars($bookType["type_name"]);
                        $selected = ($type_id == $book_type_id) ? 'selected' : ''; // Check if this option is selected
                        echo '<option value="' . $type_id . '" ' . $selected . '>' . $type_name . '</option>';
                    }
                    ?>
                </select>
                 </div>
                <div class="input-group input-group-outline my-3">
                    <select name="book_level_id" id="book_level" class="form-control" required>
                    <option value="" disabled selected>-- اختر مستوى الكتاب --</option>
                    <?php
                            // Fetch book levels for dropdown
                    $bookLevelsResult = mysqli_query($conn, "SELECT * FROM book_levels");
                    // Fetch book levels for dropdown
                    while ($bookLevel = mysqli_fetch_assoc($bookLevelsResult)) {
                        $level_id = htmlspecialchars($bookLevel["id"]);
                        $level_name = htmlspecialchars($bookLevel["level_name"]);
                        $selected = ($level_id == $book_level_id) ? 'selected' : '';
                        echo '<option value="' . $level_id . '" ' . $selected . '>' . $level_name . '</option>';
                    }
                    ?>
                 </select>
             </div>
             <div class="form-group">
                <label class="form-label">اسم المادة:</label>
                <input type="text" name="subject_name" id="subject" class="form-control border pe-2" value="<?php echo htmlspecialchars(isset($item['subject_name']) ? $item['subject_name'] : ''); ?>" required>
            </div>       
              <div class="form-group mt-3">
                  <button type="submit" name="updateData" class="btn btn-primary">Update</button>
              </div>
          </form>
          <hr>
          <a href="display_subjects.php" class="btn btn-secondary">العودة إلى قائمة المواد </a>

          <script>
    // Function to populate book levels dropdown based on selected book type
    function populateBookLevels(bookTypeId, selectedLevelId) {
        var bookLevelSelect = document.getElementById('book_level');
        bookLevelSelect.innerHTML = '';

        // Create a default placeholder option
        var placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = '-- اختر مستوى الكتاب --';
        placeholderOption.disabled = true;
        placeholderOption.selected = true;
        bookLevelSelect.appendChild(placeholderOption);

        // Fetch and populate book levels based on selected book type
        if (bookTypeId !== '') {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_book_levels_for_subject.php?book_type_id=' + bookTypeId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Parse the response and create option elements
                    var bookLevels = JSON.parse(xhr.responseText);
                    bookLevels.forEach(function (bookLevel) {
                        var option = document.createElement('option');
                        option.value = bookLevel.id;
                        option.textContent = bookLevel.level_name;
                        if (bookLevel.id == selectedLevelId) {
                            option.selected = true; // Select the appropriate level
                        }
                        bookLevelSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    }

    // Add an event listener to the book_type dropdown
    document.getElementById('book_type').addEventListener('change', function () {
        var bookTypeId = this.value;
        populateBookLevels(bookTypeId, <?php echo json_encode($book_level_id); ?>);
    });

    // On page load, populate book levels based on the initial selected book type
    populateBookLevels(<?php echo json_encode($book_type_id); ?>, <?php echo json_encode($book_level_id); ?>);
</script>
<?php
     // Close the database connection
     mysqli_close($conn);
include('footer.php');
ob_end_flush();
?>