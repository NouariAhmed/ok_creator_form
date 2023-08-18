<?php
session_start();
include('secure.php');
include('../connect.php');
$table = "book_levels";

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
  $result = mysqli_query($conn, "SELECT bl.id, bl.level_name, bt.type_name FROM book_levels bl INNER JOIN book_types bt ON bl.book_type_id = bt.id LIMIT $startIndex, $itemsPerPage");
  $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        // Validate user input
        $level_name = htmlspecialchars($_POST['level_name']);
        $book_type_id = $_POST['book_type_id'];

        // Prepare and execute SQL query
        $stmt = mysqli_prepare($conn, "INSERT INTO book_levels (level_name, book_type_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "si", $level_name, $book_type_id);
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

include('header.php');
?>
    <div class="container-fluid py-4">
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




        <form role="form" action="" method="post">
        <h4 class="mb-3">إنشاء مستوى كتاب</h4>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars(isset($item['id']) ? $item['id'] : ''); ?>">
          <div class="input-group input-group-outline my-3">
            <label class="form-label">اسم مستوى الكتاب:</label>
            <input type="text" name="level_name" class="form-control" value="<?php echo htmlspecialchars(isset($item['level_name']) ? $item['level_name'] : ''); ?>" required>
              </div>
              <div class="input-group input-group-outline my-3">
                <select name="book_type_id" class="form-control" required>
                <option value="" disabled selected> -- اختر نوع الكتاب -- </option>
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
                    ?>
                </select>
                 </div>
                 <button type="submit" name="submit" class="btn bg-gradient-primary" >Create</button>
         </form>
    <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize pe-3">Book Levels table</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7" >المعرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">مستوى الكتاب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">نوع الكتاب</th>
                      <th class="text-center text-secondary text-lg font-weight-bolder opacity-7">الإجراءات</th>
                    
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  foreach ($items as $item) {
                    $id = htmlspecialchars($item["id"]);
                    $level_name = htmlspecialchars($item["level_name"]);
                    $type_name = htmlspecialchars($item["type_name"]);
?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm pe-3"><?php echo $id;?></h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo $level_name;?></h6>
                       </td>
                       <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo $type_name;?></h6>
                       </td>
                      <td class="align-middle text-center">
                            <a href="update_book_level.php?id=<?php echo $id;?>" class="btn badge-sm bg-gradient-primary"> <i class="material-icons-round align-middle" style="font-size: 18px;">edit</i></a>
                            <a href="delete_book_level.php?id=<?php echo $id;?>" class="btn badge-sm bg-gradient-danger"> <i class="material-icons-round align-middle" style="font-size: 18px;">delete</i></a>
                      </td>
                    </tr>
                    <?php
                }
                ?>
                  </tbody>
                </table>
                  <?php
                include('../pagination.php');
                // Close the database connection
                mysqli_close($conn);
                  ?>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
include('footer.php');
?>