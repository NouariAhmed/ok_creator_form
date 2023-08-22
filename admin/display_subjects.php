<?php
session_start();
include('secure.php');
include('../connect.php');
$table = "subjects";

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
$result = mysqli_query($conn, "SELECT s.id, s.subject_name, bl.level_name, bt.type_name, bl.book_type_id 
FROM subjects s INNER JOIN book_levels bl ON s.book_level_id = bl.id INNER JOIN book_types bt ON bl.book_type_id = bt.id LIMIT $startIndex, $itemsPerPage");

$items = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $book_level_id = $_POST['book_level_id'];
    $subject_name = htmlspecialchars($_POST['subject_name']);
        // Insert new subject
        $sql = "INSERT INTO subjects (subject_name, book_level_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $subject_name, $book_level_id);
    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Set session variable for success message
        $_SESSION['create_update_success'] = true;
        // Redirect to the display_subjects page after successful creation/update
        header("Location: display_subjects.php");
        exit; // Important! Ensure the script stops executing after redirection header is sent
    } else {
        echo '<div class="alert alert-danger text-right text-white">حدث خطأ</div>';
    }
        // Close the prepared statement
        mysqli_stmt_close($stmt);
}
}
include('header.php');
?>
    <div class="container-fluid py-4">
      <?php
        // Check if create_update_success session variable is set
        if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
            echo '<div class="alert alert-success text-right text-white">تم إنشاء/تحديث العنصر بنجاح.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['create_update_success']);
        }
        // Check if delete_success session variable is set
        if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === true) {
            echo '<div class="alert alert-success text-right text-white">تم حذف العنصر بنجاح.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['delete_success']);
        }
        // Check if item_not_found session variable is set
        if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
            echo '<div class="alert alert-danger text-right text-white">العنصر غير موجود.</div>';
            // Unset the session variable to avoid displaying the message on page refresh
            unset($_SESSION['item_not_found']);
        }
        ?>
        <form role="form" action="" method="post">
        <h4 class="mb-3">إضافة مادة جديدة</h4>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars(isset($item['id']) ? $item['id'] : ''); ?>">

        <div class="input-group input-group-outline my-3">
            <select name="book_type_id" id="book_type"  class="form-control" required>
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
        <div class="input-group input-group-outline my-3">
            <select name="book_level_id" id="book_level" class="form-control" required>
                    <option value="" disabled selected> -- اختر مستوى الكتاب -- </option>
                    <?php
                    // Database connection configuration
                    include('../connect.php');

                    // Fetch book levels for dropdown
                    $bookLevelsResult = mysqli_query($conn, "SELECT * FROM book_levels");
                    while ($bookLevel = mysqli_fetch_assoc($bookLevelsResult)) {
                        $level_id = htmlspecialchars($bookLevel["id"]);
                        $level_name = htmlspecialchars($bookLevel["level_name"]);
                        echo '<option value="' . $level_id . '">' . $level_name . '</option>';
                    }
                    ?>
             </select>
         </div>
         <div class="input-group input-group-outline my-3">
                <label class="form-label">اسم المادة:</label>
                <input type="text" name="subject_name" id="subject" class="form-control" value="<?php echo htmlspecialchars(isset($item['subject_name']) ? $item['subject_name'] : ''); ?>" required>
            </div>

                 <button type="submit" name="submit" class="btn bg-gradient-primary" >إضـافة</button>
         </form>
    <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize pe-3">جدول المواد</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7" >المعرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المادة</th>
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
                    $subject_name = htmlspecialchars($item["subject_name"]);
                    $level_name = htmlspecialchars($item["level_name"]);
                    $type_name = htmlspecialchars($item["type_name"]);
                    $book_type_id = htmlspecialchars($item["book_type_id"]);
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
                      <h6 class="mb-0 text-sm"><?php echo $subject_name;?></h6>
                       </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo $level_name;?></h6>
                       </td>
                       <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo $type_name;?></h6>
                       </td>
                      <td class="align-middle text-center">
                      <a href="update_subject.php?id=<?php echo $id; ?>&book_type_id=<?php echo $book_type_id; ?>" class="btn badge-sm bg-gradient-primary"><i class="material-icons-round align-middle" style="font-size: 18px;">edit</i></a>
                            <a href="delete_subject.php?id=<?php echo $id;?>" class="btn badge-sm bg-gradient-danger"> <i class="material-icons-round align-middle" style="font-size: 18px;">delete</i></a>
                      </td>
                    </tr>
                    <?php
                }
                ?>
                  </tbody>
                </table>
                  <?php
                include('../pagination.php');
                  ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script>
        
// Get references to the select elements
const bookTypeSelect = document.getElementById('book_type');
const bookLevelSelect = document.getElementById('book_level');
const subjectSelect = document.getElementById('subject');

// Disable the Book Level and Subject selects by default
bookLevelSelect.disabled = true;
subjectSelect.disabled = true;

// Event listener to handle Book Type selection
bookTypeSelect.addEventListener('change', function () {
const selectedBookType = bookTypeSelect.value;
// If no book type is selected, clear and disable the Book Level and Subject selects
if (selectedBookType === '') {
clearBookLevelAndSubject();
} else {
// Fetch book levels based on the selected book type from the server using Ajax
fetchBookLevels(selectedBookType);
}
});

// Event listener to handle Book Level selection
bookLevelSelect.addEventListener('change', function () {
const selectedBookLevel = bookLevelSelect.value;
// If no book level is selected, disable the Subject select and show appropriate message
if (selectedBookLevel === '') {
clearSubject();
} else {
// Fetch subjects based on the selected book level from the server using Ajax
fetchSubjects(selectedBookLevel);
}
});

// Function to fetch book levels using Ajax
function fetchBookLevels(bookType) {
fetch('../get_book_levels.php?type_id=' + bookType)
.then(response => response.json())
.then(data => {
// Generate the Book Level select options
const bookLevelsOptions = data.map(level => `<option value="${level.id}">${level.level_name}</option>`);
// Display the Book Level select
bookLevelSelect.innerHTML = '<option value="">-- إختر مستوى الكتاب --</option>' + bookLevelsOptions.join('');
// Enable the Book Level select
bookLevelSelect.disabled = false;
// Clear and disable the Subject select
clearSubject();
})
.catch(error => console.error('حدث خطأ:', error));
}

// Function to fetch subjects using Ajax
function fetchSubjects(bookLevel) {
fetch('../get_subjects.php?level_id=' + bookLevel)
.then(response => response.json())
.then(data => {
// Generate the Subject select options
const subjectsOptions = data.map(subject => `<option value="${subject.id}">${subject.subject_name}</option>`);
// Display the Subject select
subjectSelect.innerHTML = '<option value="">-- إختر المادة --</option>' + subjectsOptions.join('');
// Enable the Subject select
subjectSelect.disabled = false;
})
.catch(error => console.error('حدث خطأ:', error));
}

// Function to clear and disable the Subject select
function clearSubject() {
subjectSelect.innerHTML = '<option value="">-- إختر المادة --</option>';
subjectSelect.disabled = true;
}

// Function to clear and disable the Book Level and Subject selects
function clearBookLevelAndSubject() {
bookLevelSelect.innerHTML = '<option value="">-- إختر مستوى الكتاب --</option>';
bookLevelSelect.disabled = true;
clearSubject();
}

      </script>
<?php
 // Close the database connection
 mysqli_close($conn);
include('footer.php');
?>