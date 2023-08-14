<?php
session_start();

// Check if the user is logged in and their role is "admin"
if (isset($_SESSION['id']) && $_SESSION['role'] === "admin") {
    // The user is an admin, continue with dashboard content

  // Pagination
  include('connect.php');
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


   include('dash_functions.php'); 


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

// ... (remaining code)

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
$sql .= " LIMIT $startIndex, $itemsPerPage";

// ... (previous code)


$stmt = mysqli_prepare($conn, $sql);

// Bind parameters for the query prepared statement
if (!empty($bindValues)) {
    $bindParams = array_merge([$bindTypes], $bindValues);
    $stmt->bind_param(...$bindParams);
}

// Execute the query
mysqli_stmt_execute($stmt);

// ... (remaining code)


// Get the result set
$result = mysqli_stmt_get_result($stmt);

// Fetch the items for the current page
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);


} 

else {
    // The user is not an admin or not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" dir="rtl"> <!-- Add the dir attribute here -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
</head>

<body class="main-bg">
  <!-- Header Section -->
  <header class="bg-primary py-3">
  <div class="container">
    <div class="row">
      <div class="col text-center">
        <h1>مكتبة عكاشة أكثر من مجرد دار نشر</h1>
      </div>
    </div>
    <div class="row mt-2 text-center">
      <div class="col">
        <div class="card-group">
          <div class="card bg-info">
            <div class="card-body">
              <h5 class="card-title">جميع أنواع الكتب</h5>
              <p class="card-text">استكشف جميع أنواع الكتب  .</p>
              <a href="book_types/display_book_types.php" class="btn btn-light btn-lg">تصفح</a>
            </div>
          </div>
          <div class="card bg-success">
            <div class="card-body">
              <h5 class="card-title">المستويات</h5>
              <p class="card-text">تصفح المستويات المختلفة للكتب.</p>
              <a href="book_levels/display_book_levels.php" class="btn btn-light btn-lg">تصفح</a>
            </div>
          </div>
          <div class="card bg-warning">
            <div class="card-body">
              <h5 class="card-title">المواد</h5>
              <p class="card-text">استكشف مختلف المواد والموضوعات المتاحة.</p>
              <a href="subjects/display_subjects.php" class="btn btn-light btn-lg">تصفح</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </header>

  <!-- Dashboard Content -->
  <div class="container mt-4">
    <div class="row justify-content-center mt-5">
      <div class="col-lg-12">
        <div class="card shadow">
          <div class="card-title text-center border-bottom">
            <h2 class="p-3">مرحبًا بك يا مشرف <?php echo $_SESSION['username']; ?>!</h2>
          </div>
          <div class="card-body">
            <h4>قائمة الكتّاب</h4>
            <form class="mb-4">
          <label for="category" class="form-label">اختر الفئة:</label>
          <select class="form-select" id="category" name="category">
          <option value="all" <?php echo $selectedCategory === 'all' ? 'selected' : ''; ?>>الكل</option>
          <option value="student" <?php echo $selectedCategory === 'student' ? 'selected' : ''; ?>>طالب</option>
          <option value="teacher" <?php echo $selectedCategory === 'teacher' ? 'selected' : ''; ?>>أستاذ</option>
          <option value="inspector" <?php echo $selectedCategory === 'inspector' ? 'selected' : ''; ?>>مفتش</option>
          <option value="doctor" <?php echo $selectedCategory === 'doctor' ? 'selected' : ''; ?>>طبيب</option>
          <option value="trainer" <?php echo $selectedCategory === 'trainer' ? 'selected' : ''; ?>>مدرب</option>
          <option value="novelist" <?php echo $selectedCategory === 'novelist' ? 'selected' : ''; ?>>روائي</option> 
          </select>
          <label for="bookType" class="form-label">اختر نوع الكتاب:</label>
          <select class="form-select" id="bookType" name="bookType">
          <option value="all" <?php echo $selectedBookType === 'all' ? 'selected' : ''; ?>>الكل</option>
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
      <label for="bookLevel" class="form-label">Book Level:</label>
      <select class="form-select" name="bookLevel" id="bookLevel">
          <option value="all" <?php echo ($selectedBookLevel === 'all') ? 'selected' : ''; ?>>All Levels</option>
          <!-- Fetch and populate the book levels from the database -->
          <?php
          $bookLevels = mysqli_query($conn, "SELECT * FROM book_levels");
          while ($level = mysqli_fetch_assoc($bookLevels)) {
              echo '<option value="' . $level['id'] . '" ' . ($selectedBookLevel === $level['id'] ? 'selected' : '') . '>' . $level['level_name'] . '</option>';
          }
          ?>
      </select>
      <label for="subject" class="form-label">اختر المادة:</label>
      <select class="form-select" id="subject" name="subject">
          <option value="all" <?php echo $selectedSubject === 'all' ? 'selected' : ''; ?>>الكل</option>
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

          <button type="submit" class="btn btn-primary mt-2">تصفية</button>
          
        </form>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>المعرّف</th>
                  <th>الاسم الكامل</th>
                  <th>البريد الإلكتروني</th>
                  <th>سنة الميلاد</th>
                  <th>الهاتف</th>
                  <th>العنوان</th>
                  <th>النوع</th>
                  <th>نوع الكتاب</th> <!-- Add this column -->
                <th>مستوى الكتاب</th> <!-- Add this column -->
                <th>المادة</th> <!-- Add this column -->
                  <!-- Add more columns here as needed -->
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $row) { ?>
                  <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['authorfullname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['year_of_birth']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['authorAddress']; ?></td>
                    <td><?php echo $row['author_type']; ?></td>
                    <td><?php echo getBookTypeName($conn, $row['book_type_id']); ?></td> <!-- Display book_type -->
                    <td><?php echo getBookLevelName($conn, $row['book_level_id']); ?></td> <!-- Display book_level -->
                    <td><?php echo getSubjectName($conn, $row['subject_id']); ?></td> <!-- Display subject -->
                        <!-- Display student-specific or teacher-specific data -->
                     <?php if ($row['author_type'] === 'student') { ?>       
                        <?php $studentData = getStudentData($conn, $row['id']); ?>
                        <td><?php echo $studentData['studentLevel']; ?></td>
                        <td><?php echo $studentData['studentSpecialty']; ?></td>
                        <td><?php echo $studentData['baccalaureateRate']; ?></td>
                        <td><?php echo $studentData['baccalaureateYear']; ?></td>
                        <!-- Add more columns for student data here -->
                    <?php } elseif ($row['author_type'] === 'teacher') { ?>
                        <?php $teacherData = getTeacherData($conn, $row['id']); ?>
                        <td><?php echo $teacherData['teacherExperience']; ?></td>
                        <td><?php echo $teacherData['teacherCertificate']; ?></td>
                        <td><?php echo $teacherData['teacherRank']; ?></td>
                        <td><?php echo $teacherData['workFoundation']; ?></td>
                        <!-- Add more columns for teacher data here -->
                    <?php } elseif ($row['author_type'] === 'inspector') { ?>
                        <?php $inspectorData = getInspectorData($conn, $row['id']); ?>
                        <td><?php echo $inspectorData['inspectorExperience']; ?></td>
                        <td><?php echo $inspectorData['InspectorCertificate']; ?></td>
                        <td><?php echo $inspectorData['inspectorRank']; ?></td>
                        <td><?php echo $inspectorData['inspectorWorkFoundation']; ?></td>
                        <!-- Add more columns for inspector data here -->
                    <?php } elseif ($row['author_type'] === 'doctor') { ?>
                        <?php $doctorData = getDoctorData($conn, $row['id']); ?>
                        <td><?php echo $doctorData['specialty']; ?></td>
                        <td><?php echo $doctorData['drWorkFoundation']; ?></td>
                        <!-- Add more columns for doctor data here -->
                    <?php } elseif ($row['author_type'] === 'trainer') { ?>
                        <?php $trainerData = getTrainerData($conn, $row['id']); ?>
                        <td><?php echo $trainerData['field']; ?></td>
                        <td><?php echo $trainerData['trainerExperience']; ?></td>
                        <!-- Add more columns for trainer data here -->
                    <?php } elseif ($row['author_type'] === 'novelist') { ?>
                        <?php $novelistData = getNovelistData($conn, $row['id']); ?>
                        <td><?php echo $novelistData['novelistfield']; ?></td>
                        <!-- Add more columns for novelist data here -->
                    <?php } else { ?>
                        <td colspan="4">N/A</td>
                        <!-- Add more columns for other author types if needed -->
                    <?php } ?>
                    <!-- Add more columns here as needed -->
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div id="pagination-controls" class="text-center mt-4">
        <?php
         include('pagination.php');
           // Close the database connection
  mysqli_close($conn);
          ?>
        </div>
          </div>
          <div class="card-footer text-center">
            <a href="logout.php" class="btn btn-danger">تسجيل الخروج</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>