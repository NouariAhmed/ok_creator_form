<?php
session_start();
include('secure.php');
include('../connect.php');
include('../dash_functions.php'); 
$table = "authors";

$itemsPerPage = 10; // Number of items per page

$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;

// Get the total number of items in the database
$sql = "SELECT COUNT(*) AS total_items FROM $table";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalItems = $row['total_items'];

$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = max(1, min($currentPage, $totalPages));

$startIndex = ($currentPage - 1) * $itemsPerPage;

// Retrieve items for the current page
$result = mysqli_query($conn, "SELECT * FROM $table LIMIT $startIndex, $itemsPerPage");
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
// Get the selected category from the query parameter
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$selectedBookType = isset($_GET['bookType']) ? $_GET['bookType'] : 'all';
$selectedBookLevel = isset($_GET['bookLevel']) ? $_GET['bookLevel'] : 'all';
$selectedSubject = isset($_GET['subject']) ? $_GET['subject'] : 'all';

// Construct the SQL query based on selected filters
$sql = "SELECT COUNT(*) AS total_filtered_items FROM $table WHERE 1 = 1"; // Initial SQL with a dummy condition

$bindTypes = ''; // String to store parameter types
$bindValues = []; // Array to store parameter values

if ($selectedCategory !== 'all') {
  $sql .= " AND author_type = ?";
  $bindTypes .= 's'; // Assuming author_type is a string
  $bindValues[] = &$selectedCategory;
}

if ($selectedBookType !== 'all') {
  $sql .= " AND book_type_id = ?";
  $bindTypes .= 'i'; // Assuming book_type_id is an integer
  $bindValues[] = &$selectedBookType;
}

if ($selectedBookLevel !== 'all') {
  $sql .= " AND book_level_id = ?";
  $bindTypes .= 'i'; // Assuming book_level_id is an integer
  $bindValues[] = &$selectedBookLevel;
}

if ($selectedSubject !== 'all') {
$sql .= " AND subject_id = ?";
$bindTypes .= 'i'; // Assuming subject_id is an integer
$bindValues[] = &$selectedSubject;
}
// ... (previous code)

$countStmt = mysqli_prepare($conn, $sql);

// Bind parameters for the count query prepared statement
if (!empty($bindValues)) {
  $bindParams = array_merge([$bindTypes], $bindValues);
  $countStmt->bind_param(...$bindParams);
}

// Execute the count query
mysqli_stmt_execute($countStmt);

$countResult = mysqli_stmt_get_result($countStmt);
$countRow = mysqli_fetch_assoc($countResult);
$totalFilteredItems = $countRow['total_filtered_items'];

// Calculate Total Pages
$totalPages = ceil($totalFilteredItems / $itemsPerPage);

// Construct the main SQL query for pagination
$sql = "SELECT * FROM $table WHERE 1 = 1"; // Initial SQL with a dummy condition

if ($selectedCategory !== 'all') {
  $sql .= " AND author_type = ?";
}

if ($selectedBookType !== 'all') {
  $sql .= " AND book_type_id = ?";
}

if ($selectedBookLevel !== 'all') {
  $sql .= " AND book_level_id = ?";
}
if ($selectedSubject !== 'all') {
$sql .= " AND subject_id = ?";
}
$sql .= " ORDER BY communicate_date DESC";
$sql .= " LIMIT $startIndex, $itemsPerPage";

$stmt = mysqli_prepare($conn, $sql);

// Bind parameters for the query prepared statement
if (!empty($bindValues)) {
  $bindParams = array_merge([$bindTypes], $bindValues);
  $stmt->bind_param(...$bindParams);
}
// Execute the query
mysqli_stmt_execute($stmt);

// Get the result set
$result = mysqli_stmt_get_result($stmt);

// Fetch the items for the current page
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
       
        <h4 class="mb-3">إضافة مؤلف</h4>
          <div class="input-group input-group-outline my-3">
          <a href="add_author.php" class="btn btn-secondary">إضـافة</a>
              </div>
                
          <form role="form">
          <h5 class="mb-3">فلترة</h5>
        <div class="input-group input-group-outline my-3">
          <select class="form-control" id="category" name="category">
          <option value="all" <?php echo $selectedCategory === 'all' ? 'selected' : ''; ?>>-- جميع أنواع المؤلفين --</option>
          <option value="student" <?php echo $selectedCategory === 'student' ? 'selected' : ''; ?>>طالب</option>
          <option value="teacher" <?php echo $selectedCategory === 'teacher' ? 'selected' : ''; ?>>أستاذ</option>
          <option value="inspector" <?php echo $selectedCategory === 'inspector' ? 'selected' : ''; ?>>مفتش</option>
          <option value="doctor" <?php echo $selectedCategory === 'doctor' ? 'selected' : ''; ?>>طبيب</option>
          <option value="trainer" <?php echo $selectedCategory === 'trainer' ? 'selected' : ''; ?>>مدرب</option>
          <option value="novelist" <?php echo $selectedCategory === 'novelist' ? 'selected' : ''; ?>>روائي</option> 
          </select>
          </div>

          <div class="input-group input-group-outline my-3">
          <select class="form-control" id="bookType" name="bookType">
          <option value="all" <?php echo $selectedBookType === 'all' ? 'selected' : ''; ?>>-- جميع أنواع الكتب -- </option>
          <!-- Fetch and display book types dynamically from the database -->
          <?php
          $bookTypesQuery = "SELECT * FROM book_types";
          $bookTypesResult = mysqli_query($conn, $bookTypesQuery);
          while ($bookTypeRow = mysqli_fetch_assoc($bookTypesResult)) {
              $isSelected = $selectedBookType == $bookTypeRow['id'] ? 'selected' : '';
              echo '<option value="' . $bookTypeRow['id'] . '" ' . $isSelected . '>' . $bookTypeRow['type_name'] . '</option>';
          }
          ?>
      </select>
      </div>
      <div class="input-group input-group-outline my-3">
      <select class="form-control" name="bookLevel" id="bookLevel">
          <option value="all" <?php echo ($selectedBookLevel === 'all') ? 'selected' : ''; ?>>-- جميع المستويات --</option>
          <!-- Fetch and populate the book levels from the database -->
          <?php
          $bookLevels = mysqli_query($conn, "SELECT * FROM book_levels");
          while ($level = mysqli_fetch_assoc($bookLevels)) {
              echo '<option value="' . $level['id'] . '" ' . ($selectedBookLevel === $level['id'] ? 'selected' : '') . '>' . $level['level_name'] . '</option>';
          }
          ?>
      </select>
      </div>

      <div class="input-group input-group-outline my-3">
      <select class="form-control" id="subject" name="subject">
          <option value="all" <?php echo $selectedSubject === 'all' ? 'selected' : ''; ?>>-- جميع المواد --</option>
          <!-- Fetch and display subjects dynamically from the database -->
          <?php
          $subjectsQuery = "SELECT * FROM subjects";
          $subjectsResult = mysqli_query($conn, $subjectsQuery);
          while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
              $isSelected = $selectedSubject == $subjectRow['id'] ? 'selected' : '';
              echo '<option value="' . $subjectRow['id'] . '" ' . $isSelected . '>' . $subjectRow['subject_name'] . '</option>';
          }
          ?>
      </select>
       </div>

          <button type="submit"  class="btn bg-gradient-primary" >فلترة</button> 
        </form>
    <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize pe-3">جدول المؤلفين</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 table-hover">
                  <thead>
                    <tr>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 ">المعرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المؤلف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">عنوان الكتاب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">النوع والمستوى</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المادة</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">العنوان</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">من طرف</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">ملاحظات</th>
                        <!-- Teacher-specific columns -->
                      <?php if ($selectedCategory === 'teacher') { ?>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الشهادة</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الخبرة</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الرتبة والمؤسسة</th>
                       <!-- Student-specific columns -->
                      <?php } elseif ($selectedCategory === 'student') { ?>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">مستوى الطالب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">تخصص الطالب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المعدل والسنة</th>
                      <?php } elseif ($selectedCategory === 'inspector') { ?>
                      <!-- Inspector-specific columns -->
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الشهادة</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الخبرة</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الرتبة والولاية</th>
                      <?php } elseif ($selectedCategory === 'doctor') { ?>
                      <!-- Doctor-specific columns -->
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">تخصص الطبيب</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">مكان العمل</th>
                      <?php } elseif ($selectedCategory === 'trainer') { ?>
                      <!-- Trainer-specific columns -->
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المجال</th>
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">الخبرة</th>
                      <?php } elseif ($selectedCategory === 'novelist') { ?>
                      <!-- Novelist-specific columns -->
                      <th class="text-secondary text-lg font-weight-bolder opacity-7 pe-2">المجال</th>
                      <?php } ?>
                      <th class="text-center text-secondary text-lg font-weight-bolder opacity-7">الإجراءات</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                foreach ($items as $item) {
                ?>
                    <tr>
                    <td class="align-middle text-sm">
                    <h6 class="mb-0 text-sm pe-3"><?php echo htmlspecialchars($item["communicate_date"]);?></h6>
                    <p class="text-xs text-secondary mb-0 pe-3"><?php echo htmlspecialchars($item["id"]);?>#</p>
                      </td>
                      <td>
                         <!--
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="../assets/img/team-2.jpg" class="avatar avatar-sm ms-3 border-radius-lg" alt="user1">
                          </div> -->
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["authorfullname"]);?></h6>
                            <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["phone"]);?></p>
                            <p class="text-xs text-primary mb-0"><?php echo htmlspecialchars($item["author_type"]);?></p>
                            <!-- </div> -->
                          </div>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm">
                      <?php
    $bookTitle = htmlspecialchars($item["book_title"]);
    $titelWords = explode(' ', $bookTitle);
    
    $titelWordGroups = array_chunk($titelWords, 4);
    foreach ($titelWordGroups as $titleGroup) {
        echo implode(' ', $titleGroup) . "<br>";
    }
    ?>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo getBookLevelName($conn, $item['book_level_id']); ?></h6>
                      <p class="text-xs text-secondary mb-0"><?php echo getBookTypeName($conn, $item['book_type_id']); ?></p>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo getSubjectName($conn, $item['subject_id']); ?></h6>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php
    $address = htmlspecialchars($item["authorAddress"]);
    $words = explode(' ', $address);
    
    $wordGroups = array_chunk($words, 4);
    foreach ($wordGroups as $group) {
        echo implode(' ', $group) . "<br>";
    }
    ?></h6>
                      <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["email"]);?></p>
                  
                      <!-- Social Media Icons -->
                      <div class="ms-auto">
                          <?php if (!empty($item["fbLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["fbLink"]); ?>" target="_blank">
                              <i class="fab fa-facebook"></i>
                            </a>
                          <?php } ?>
                          <?php if (!empty($item["instaLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["instaLink"]); ?>" target="_blank">
                              <i class="fab fa-instagram"></i>
                            </a>
                          <?php } ?>
                          <?php if (!empty($item["youtubeLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["youtubeLink"]); ?>" target="_blank">
                              <i class="fab fa-youtube"></i>
                            </a>
                          <?php } ?>
                          <?php if (!empty($item["tiktokLink"])) { ?>
                            <a href="<?php echo htmlspecialchars($item["tiktokLink"]); ?>" target="_blank">
                              <i class="fab fa-tiktok"></i>
                            </a>
                          <?php } ?>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                      <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($item["inserted_by_username"]);?></h6>
                      <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($item["created_at"]);?></p>
                      </td>
                      <td class="align-middle text-sm">
    <h6 class="mb-0 text-sm">
        <?php if (!empty($item['notes'])): ?>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="populateModal('<?php echo $item['notes']; ?>')">
                <i class="fas fa-comment-alt align-middle" style="font-size: 18px;"></i>
            </button>
        <?php endif; ?>
    </h6>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ملاحظات خاصة بالمؤلف: <?php echo $item['authorfullname']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalContent"></div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">غلق</button>
                </div>
            </div>
        </div>
    </div>
</td>


                                <?php if ($selectedCategory === 'teacher') { ?>
                                    <!-- Display teacher-specific columns -->
                                    <?php $teacherData = getTeacherData($conn, $item['id']); ?>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $teacherData['teacherCertificate']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $teacherData['teacherExperience']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $teacherData['teacherRank']; ?></h6>
                                        <p class="text-xs text-secondary mb-0"><?php echo $teacherData['workFoundation']; ?></p>
                                    </td>
                                <?php } elseif ($selectedCategory === 'student') { ?>
                                    <!-- Display student-specific columns -->
                                    <?php $studentData = getStudentData($conn, $item['id']); ?>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $studentData['studentLevel']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $studentData['studentSpecialty']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $studentData['baccalaureateRate']; ?></h6>
                                        <p class="text-xs text-secondary mb-0"><?php echo $studentData['baccalaureateYear']; ?></p>
                                    </td>
                                  <?php } elseif ($selectedCategory === 'inspector') { ?>
                                    <!-- Display inspector-specific columns -->
                                    <?php $inspectorData = getInspectorData($conn, $item['id']); ?>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $inspectorData['inspectorCertificate']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $inspectorData['inspectorExperience']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $inspectorData['inspectorRank']; ?></h6>
                                        <p class="text-xs text-secondary mb-0"><?php echo $inspectorData['inspectorWorkFoundation']; ?></p>
                                    </td>
                                <?php } elseif ($selectedCategory === 'doctor') { ?>
                                    <!-- Display doctor-specific columns -->
                                    <?php $doctorData = getDoctorData($conn, $item['id']); ?>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $doctorData['specialty']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $doctorData['drWorkFoundation']; ?></h6>
                                    </td>
                                <?php } elseif ($selectedCategory === 'trainer') { ?>
                                    <!-- Display trainer-specific columns -->
                                    <?php $trainerData = getTrainerData($conn, $item['id']); ?>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $trainerData['field']; ?></h6>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $trainerData['trainerExperience']; ?></h6>
                                    </td>
                                <?php } elseif ($selectedCategory === 'novelist') { ?>
                                    <!-- Display novelist-specific columns -->
                                    <?php $novelistData = getNovelistData($conn, $item['id']); ?>
                                    <td class="align-middle text-sm">
                                        <h6 class="mb-0 text-sm"><?php echo $novelistData['novelistfield']; ?></h6>
                                    </td>
                                    


                                <?php } ?>
                      <td class="align-middle text-center">
                            <?php if (!empty($item["userfile"])): ?>
                                <a href="<?php echo htmlspecialchars($item["userfile"]); ?>" class="btn badge-sm bg-gradient-secondary" target="_blank">
                                    <i class="fas fa-file-pdf align-middle" style="font-size: 18px;"></i></a>
                            <?php endif; ?>
                            <a href="update_author.php?id=<?php echo htmlspecialchars($item["id"]);?>&type=<?php echo htmlspecialchars($item["author_type"]);?>&book_type_id=<?php echo htmlspecialchars($item["book_type_id"]); ?>" class="btn badge-sm bg-gradient-primary"> <i class="material-icons-round align-middle" style="font-size: 18px;">edit</i></a>
                            <a href="delete_author.php?id=<?php echo htmlspecialchars($item["id"]);?>&type=<?php echo htmlspecialchars($item["author_type"]);?>" class="btn badge-sm bg-gradient-danger"> <i class="material-icons-round align-middle" style="font-size: 18px;">delete</i></a>
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
<?php
 mysqli_close($conn);
include('footer.php');
?>

          