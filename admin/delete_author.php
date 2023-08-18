<?php
session_start();
ob_start(); // Start output buffering
include('secure.php');
include('header.php');
?>
    <div class="container-fluid py-4">
    <h4 class="mb-3">حذف مؤلف</h4>
      <?php
            // Check if the delete_success session variable is set
            if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
                // Unset the session variable to avoid displaying the message on page refresh
                unset($_SESSION['delete_success']);
                // Redirect to the display_authors page with a success message
                header("Location: display_authors.php?delete_success=1");
                exit;
            }
            // Check if the item_not_found session variable is set
            if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
                // Unset the session variable to avoid displaying the message on page refresh
                unset($_SESSION['item_not_found']);
                // Redirect to the display_authors page with a success message
                header("Location: display_authors.php?item_not_found=1");
                exit;
            }

            include('../connect.php');
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = intval($_GET['id']);
                
                // Determine the child table based on the author type query parameter
                $authorType = isset($_GET['type']) ? $_GET['type'] : '';
            
                $childTable = '';
                switch ($authorType) {
                    case 'student':
                        $childTable = 'student_data';
                        break;
                    case 'teacher':
                        $childTable = 'teacher_data';
                        break;
                    case 'inspector':
                        $childTable = 'inspector_data';
                        break;
                    case 'doctor':
                        $childTable = 'doctor_data';
                        break;
                    case 'trainer':
                        $childTable = 'trainer_data';
                         break;
                    case 'novelist':
                        $childTable = 'novelist_data';
                        break;      
                  
                }
            
                if (!empty($childTable)) {
                    include('../connect.php');
            
                    if (isset($_POST['confirm'])) {
                        // Delete from the child table
                        $childDeleteQuery = "DELETE FROM $childTable WHERE author_id = ?";
                        $stmtChildDelete = mysqli_prepare($conn, $childDeleteQuery);
                        mysqli_stmt_bind_param($stmtChildDelete, "i", $id);
                        mysqli_stmt_execute($stmtChildDelete);
            
                        // Delete from the authors table
                        $authorsDeleteQuery = "DELETE FROM authors WHERE id = ?";
                        $stmtAuthorsDelete = mysqli_prepare($conn, $authorsDeleteQuery);
                        mysqli_stmt_bind_param($stmtAuthorsDelete, "i", $id);
            
                        if (mysqli_stmt_execute($stmtAuthorsDelete)) {
                            mysqli_stmt_close($stmtAuthorsDelete);
                            mysqli_close($conn);
                            $_SESSION['delete_success'] = true;
                            header("Location: display_authors.php?delete_success=1");
                            exit;
                        } else {
                            echo '<div class="alert alert-danger text-right">خطأ في عملية الحذف: ' . mysqli_error($conn) . '</div>';
                        }
                
                        mysqli_stmt_close($stmtChildDelete);
                    } else {
                        echo '
                        <p class="text-right">هل أنت متأكد أنك تريد حذف العنصر؟</p>
                        <form action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&type=' . $authorType . '" method="post" class="d-inline">
                            <button type="submit" name="confirm" value="yes" class="btn btn-danger">نعم</button>
                        </form>
                        <a href="display_authors.php" class="btn btn-secondary">لا</a>
                        ';
                    }
                } else {
                    echo '<div class="alert alert-danger text-right">نوع المؤلف غير صحيح.</div>';
                }
            }

     // Close the database connection
     mysqli_close($conn);
include('footer.php');
ob_end_flush();
?>