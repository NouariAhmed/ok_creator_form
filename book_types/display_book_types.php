<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Book Types CRUD</title>
</head>
<body>
    <div class="container">
        <h1 class="text-right">إدارة أنواع الكتب</h1>
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
        <form action="create_update_book_type.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars(isset($item['id']) ? $item['id'] : ''); ?>">
            <div class="form-group">
                <label class="text-right">اسم نوع الكتاب:</label>
                <input type="text" name="type_name" class="form-control" value="<?php echo htmlspecialchars(isset($item['type_name']) ? $item['type_name'] : ''); ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">حفظ</button>
        </form>
        <hr>
        <h2 class="text-right">قائمة أنواع الكتب</h2>
        <a href="create_update_book_type.php" class="btn btn-primary">إضافة نوع كتاب جديد</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>المعرف</th>
                    <th>اسم نوع الكتاب</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection configuration
                include('../connect.php');

                // Fetch and display items from the database using procedural approach
                $result = mysqli_query($conn, "SELECT * FROM book_types");
                while ($item = mysqli_fetch_assoc($result)) {
                    $id = htmlspecialchars($item["id"]);
                    $type_name = htmlspecialchars($item["type_name"]);

                    echo "<tr>";
                    echo "<td>" . $id . "</td>";
                    echo "<td>" . $type_name . "</td>";
                    echo '<td>
                            <a href="create_update_book_type.php?id=' . $id . '" class="btn btn-sm btn-primary">تحرير</a>
                            <a href="delete_book_type.php?id=' . $id . '" class="btn btn-sm btn-danger">حذف</a>
                          </td>';
                    echo "</tr>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
