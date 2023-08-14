<?php
session_start();

if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
    unset($_SESSION['delete_success']);
    header("Location: display_book_levels.php?delete_success=1");
    exit;
}

if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
    unset($_SESSION['item_not_found']);
    header("Location: display_book_levels.php?item_not_found=1");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Item</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-right">حذف العنصر</h1>
        <?php
        include('../connect.php');

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = intval($_GET['id']);

            // Check if there are any records in the subjects table referencing the book level
        $stmt = mysqli_prepare($conn, "SELECT id FROM subjects WHERE book_level_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Check if there are any records in the authors table referencing the book level
        $stmt2 = mysqli_prepare($conn, "SELECT id FROM authors WHERE book_level_id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_store_result($stmt2);

        if (mysqli_stmt_num_rows($stmt) === 0 && mysqli_stmt_num_rows($stmt2) === 0) {
            if (isset($_POST['confirm'])) {
                // Delete book level from book_levels table
                $sql = "DELETE FROM book_levels WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $id);

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    $_SESSION['delete_success'] = true;
                    header("Location: display_book_levels.php?delete_success=1");
                    exit;
                } else {
                    echo '<div class="alert alert-danger text-right">خطأ في عملية الحذف: ' . mysqli_error($conn) . '</div>';
                }

                mysqli_stmt_close($stmt);
            } else {
                echo '
                <p class="text-right">هل أنت متأكد أنك تريد حذف العنصر؟</p>
                <form action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '" method="post" class="d-inline">
                    <button type="submit" name="confirm" value="yes" class="btn btn-danger">نعم</button>
                </form>
                <a href="display_book_levels.php" class="btn btn-secondary">لا</a>
                ';
            }
        } else {
            echo '<div class="alert alert-warning text-right">لا يمكن حذف هذا المستوى لأنه مستخدم من قبل مؤلفين أو مواد دراسية.</div>';
            echo '<a href="display_book_levels.php" class="btn btn-secondary">العودة إلى قائمة المستويات</a>';
        }

        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt2);
        } 

        mysqli_close($conn);
        ?>

    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>
