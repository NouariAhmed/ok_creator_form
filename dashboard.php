<?php
session_start();

// Check if the user is logged in and their role is "admin"
if (isset($_SESSION['id']) && $_SESSION['role'] === "admin") {
    // The user is an admin, continue with dashboard content

    // Include your database connection code here
    include('connect.php');

    // Fetch authors data from the database
    $sql_fetch_authors = "SELECT * FROM authors";
    $result_authors = mysqli_query($conn, $sql_fetch_authors);
            // Function to get the book type name by its ID
        function getBookTypeName($conn, $bookTypeId) {
          $sql = "SELECT type_name FROM book_types WHERE id = ?";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "i", $bookTypeId);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $typeName);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);
          return $typeName;
        }

        // Function to get the book level name by its ID
        function getBookLevelName($conn, $bookLevelId) {
          $sql = "SELECT level_name FROM book_levels WHERE id = ?";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "i", $bookLevelId);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $levelName);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);
          return $levelName;
        }

        // Function to get the subject name by its ID
        function getSubjectName($conn, $subjectId) {
          $sql = "SELECT subject_name FROM subjects WHERE id = ?";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "i", $subjectId);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $subjectName);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);
          return $subjectName;
        }
        // Function to get student data by user ID
        function getStudentData($conn, $userId) {
        $sql = "SELECT studentLevel, studentSpecialty, baccalaureateRate, baccalaureateYear FROM student_data WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $studentLevel, $studentSpecialty, $baccalaureateRate, $baccalaureateYear);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return [
            'studentLevel' => $studentLevel,
            'studentSpecialty' => $studentSpecialty,
            'baccalaureateRate' => $baccalaureateRate,
            'baccalaureateYear' => $baccalaureateYear
        ];
        }

        // Function to get teacher data by user ID
        function getTeacherData($conn, $userId) {
        $sql = "SELECT teacherExperience, teacherCertificate, teacherRank, workFoundation FROM teacher_data WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $teacherExperience, $teacherCertificate, $teacherRank, $workFoundation);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return [
            'teacherExperience' => $teacherExperience,
            'teacherCertificate' => $teacherCertificate,
            'teacherRank' => $teacherRank,
            'workFoundation' => $workFoundation
        ];
        }
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
              <p class="card-text">استكشف جميع أنواع الكتب.</p>
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
                <?php while ($row = mysqli_fetch_assoc($result_authors)) { ?>
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
                    <?php } else { ?>
                        <td colspan="4">N/A</td>
                        <!-- Add more columns for other author types if needed -->
                    <?php } ?>
                    <!-- Add more columns here as needed -->
                  </tr>
                <?php } ?>
              </tbody>
            </table>
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