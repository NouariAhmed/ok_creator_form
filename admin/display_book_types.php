<?php
session_start();
include('../connect.php');
$table = "book_types";

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
$result = mysqli_query($conn, "SELECT * FROM book_types LIMIT $startIndex, $itemsPerPage");
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        // Validate user input
        $type_name = htmlspecialchars($_POST['type_name']);
        if (empty($type_name)) {
            echo "<div class='alert alert-danger text-right'>عنوان الكتاب مطلوب</div>";
        } else {
            // Prepare and execute SQL query
            $stmt = mysqli_prepare($conn, "INSERT INTO book_types (type_name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $type_name);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['create_update_success'] = true;
                header("Location: display_book_types.php");
                exit;
            } else {
                echo "<div class='alert alert-danger text-right'>حدث خطأ</div>";
            }
            mysqli_stmt_close($stmt);
        }
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
        <h4 class="mb-3">إنشاء نوع كتاب</h4>
          <div class="input-group input-group-outline my-3">
            <label class="form-label">اسم نوع الكتاب:</label>
              <input type="text" name="type_name" class="form-control" value="<?php echo htmlspecialchars(isset($item['type_name']) ? $item['type_name'] : ''); ?>" required>
              </div>
                <button type="submit" name="submit" class="btn bg-gradient-primary" >Create</button>
        </form>
    <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize pe-3">Book Types table</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7" >المعرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">نوع الكتاب</th>
                      <th class="text-center text-secondary text-lg font-weight-bolder opacity-7">الإجراءات</th>
                    
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                foreach ($items as $item) {
                    $id = htmlspecialchars($item["id"]);
                    $type_name = htmlspecialchars($item["type_name"]);
?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <!-- <div>
                            <img src="../assets/img/team-2.jpg" class="avatar avatar-sm ms-3 border-radius-lg" alt="user1">
                          </div> -->
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo $id;?></h6>
                            <!-- <p class="text-xs text-secondary mb-0">john@creative-tim.com</p> -->
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo $type_name;?></h6>
                        <!-- <span class="badge badge-sm bg-gradient-success">Online</span> -->
                      </td>
                      <td class="align-middle text-center">
                         
                            <a href="update_book_type.php?id=<?php echo $id;?>" class="btn badge-sm bg-gradient-primary"> <i class="material-icons-round align-middle" style="font-size: 18px;">edit</i></a>
                            <a href="delete_book_type.php?id=<?php echo $id;?>" class="btn badge-sm bg-gradient-danger"> <i class="material-icons-round align-middle" style="font-size: 18px;">delete</i></a>
                          
                        <!-- <span class="text-secondary text-xs font-weight-bold">23/04/18</span> -->
                      </td>
                      <!-- <td class="align-middle">
                        <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                          Edit
                        </a>
                      </td> -->
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

          