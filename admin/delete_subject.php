<?php
session_start();
ob_start(); // Start output buffering
include('secure.php');
include('header.php');
?>
    <div class="container-fluid py-4">
    <h4 class="mb-3">حذف مادة</h4>
      <?php
        if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
            unset($_SESSION['delete_success']);
            header("Location: display_subjects.php?delete_success=1");
            exit;
        }

        if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
            unset($_SESSION['item_not_found']);
            header("Location: display_subjects.php?item_not_found=1");
            exit;
        }

            include('../connect.php');

            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = intval($_GET['id']);
    
                // Check if there are any records in the authors table referencing the subject
                $stmt = mysqli_prepare($conn, "SELECT id FROM authors WHERE subject_id = ?");
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
    
                if (mysqli_stmt_num_rows($stmt) === 0) {
                    if (isset($_POST['confirm'])) {
                        $sql = "DELETE FROM subjects WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $id);
    
                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_close($stmt);
                            mysqli_close($conn);
                            $_SESSION['delete_success'] = true;
                            header("Location: display_subjects.php?delete_success=1");
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
                        <a href="display_subjects.php" class="btn btn-secondary">لا</a>
                        ';
                    }
                } else {
                    echo '<div class="alert alert-warning text-right">لا يمكن حذف هاته المادة لأن هنالك  مؤلفين مرتبطين بها في جدول المؤلفين.</div>';
                    echo '<a href="display_subjects.php" class="btn btn-secondary">العودة إلى قائمة المواد الدراسية</a>';
                }
    
                mysqli_stmt_close($stmt);
            } 
     // Close the database connection
     mysqli_close($conn);
include('footer.php');
ob_end_flush();
?>
