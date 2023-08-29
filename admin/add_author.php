<?php
include('../connect.php');
// Initialize variables
$uname = $book_title = $email = $year_of_birth = $phone = $second_phone = $address = $book_type = $book_level = $subject = $fbLink = $instaLink = $youtubeLink = $tiktokLink = $communicate_date = $notes ="";
$uname_err = $book_title_err = $email_err = $year_of_birth_err = $phone_err = $second_phone_err = $address_err = $book_type_err = $book_level_err = $subject_err = $register_err = $file_err = $communicate_date_err="";

// Fetch book types from the database
$sql_fetch_book_types = "SELECT id, type_name FROM book_types";
$result_book_types = mysqli_query($conn, $sql_fetch_book_types);

// Fetch book levels from the database
$sql_fetch_book_levels = "SELECT id, level_name, book_type_id FROM book_levels";
$result_book_levels = mysqli_query($conn, $sql_fetch_book_levels);

// Fetch subjects from the database
$sql_fetch_subjects = "SELECT id, subject_name, book_level_id FROM subjects";
$result_subjects = mysqli_query($conn, $sql_fetch_subjects);


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // Get the form data
 $uname = trim($_POST["txt_uname"]);
 $book_title = trim($_POST["book_title"]);
 $email = trim($_POST["txt_email"]);
 $year_of_birth = trim($_POST["txt_year_of_birth"]);
 $phone = trim($_POST["txt_phone"]);
 $second_phone = trim($_POST["second_phone"]);
 $address = trim($_POST["txt_address"]);
 $communicate_date = trim($_POST["communicate_date"]);
 $notes = trim($_POST["notes"]);

 $fbLink = trim($_POST["fbLink"]);
 $instaLink = trim($_POST["instaLink"]);
 $youtubeLink = trim($_POST["youtubeLink"]);
 $tiktokLink = trim($_POST["tiktokLink"]);

 $book_type = $_POST["book_type"];
 $book_level = $_POST["book_level"];
 $subject = $_POST["subject"];
 $author_type = $_POST["author_type"];


  // Validate username
  if (empty($uname)) {
    $uname_err = "يرجى إدخال الإسم الكامل للمؤلف.";
  } elseif (!preg_match("/^[\p{L}\p{N}_\s]+$/u", $uname)) {
    $uname_err = " إسم المؤلف يجب أن يحتوي على حروف.";
  }
   // Validate username
   if (empty($book_title)) {
    $book_title_err = "يرجى إدخال نوع الكتاب.";
  } elseif (!preg_match("/^[\p{L}\p{N}_\s]+$/u", $book_title)) {
    $book_title_err = "نوع الكتاب يجب أن يحتوي على حروف.";
  }

  if (!empty($email)) {
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "يرجى إدخال عنوان إيميل صالح.";
    } 
}

  // Validate year of birth
  if (empty($year_of_birth)) {
    $year_of_birth_err = "يرجى إدخال سنة الميلاد.";
  } elseif (!is_numeric($year_of_birth) || strlen($year_of_birth) !== 4) {
    $year_of_birth_err = "سنة الميلاد يجب أن تحتوي على 4 أرقام.";
  }

  $phonePattern = "/^\+?\d{1,4}?\s?\(?\d{1,4}?\)?[0-9\- ]+$/";

  // Validate primary phone
  if (!empty($phone) && !preg_match($phonePattern, $phone)) {
      $phone_err = "رقم هاتف غير صالح.";
  } else {
      // Check if phone number already exists in the database (in phone or second_phone column)
      $existingPhoneQuery = "SELECT id, authorfullname FROM authors WHERE phone = ? OR second_phone = ?";
      $stmt_existingPhone = mysqli_prepare($conn, $existingPhoneQuery);
      mysqli_stmt_bind_param($stmt_existingPhone, "ss", $phone, $phone);
      mysqli_stmt_execute($stmt_existingPhone);
      mysqli_stmt_store_result($stmt_existingPhone);
      if (mysqli_stmt_num_rows($stmt_existingPhone) > 0) {
          mysqli_stmt_bind_result($stmt_existingPhone, $existingAuthorId, $existingAuthorName);
          mysqli_stmt_fetch($stmt_existingPhone);
          $phone_err = "رقم الهاتف مستخدم بالفعل مع المؤلف: $existingAuthorName (رقم المؤلف: $existingAuthorId)";
      }
      mysqli_stmt_close($stmt_existingPhone);
  }
  
  // Validate secondary phone
  if (!empty($second_phone) && !preg_match($phonePattern, $second_phone)) {
      $second_phone_err = "رقم هاتف ثانوي غير صالح.";
  } else {
      // Check if secondary phone number already exists in the database (in phone or second_phone column)
      if (!empty($second_phone)) {
          $existingSecondPhoneQuery = "SELECT id, authorfullname FROM authors WHERE phone = ? OR second_phone = ?";
          $stmt_existingSecondPhone = mysqli_prepare($conn, $existingSecondPhoneQuery);
          mysqli_stmt_bind_param($stmt_existingSecondPhone, "ss", $second_phone, $second_phone);
          mysqli_stmt_execute($stmt_existingSecondPhone);
          mysqli_stmt_store_result($stmt_existingSecondPhone);
          if (mysqli_stmt_num_rows($stmt_existingSecondPhone) > 0) {
              mysqli_stmt_bind_result($stmt_existingSecondPhone, $existingAuthorId, $existingAuthorName);
              mysqli_stmt_fetch($stmt_existingSecondPhone);
              $second_phone_err = "رقم الهاتف الثانوي مستخدم بالفعل مع المؤلف: $existingAuthorName (رقم المؤلف: $existingAuthorId)";
          }
          mysqli_stmt_close($stmt_existingSecondPhone);
      }
  }
  // Validate address
  if (empty($address)) {
    $address_err = "يرجى إدخال عنوان إقامة المؤلف.";
  }
    // Validate communicate_date
    if (empty($communicate_date)) {
      $communicate_date_err = "يرجى إدخال تاريخ تواصل المؤلف مع دار النشر  عكاشة.";
    }
 // Validate File
    // Check if a file is uploaded
    if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
      // Perform username validation before proceeding with the file upload
          // Check if the file is an image or PDF
          $file = $_FILES['uploadedFile'];
          $allowedTypes = array('image/jpeg', 'image/png', 'image/gif', 'application/pdf','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
          if (!in_array($file['type'], $allowedTypes)) {
          
              $file_err = "نوع غير صحيح، الأنواع المقبولة: JPEG, PNG, GIF, PDF And Docx.";
          }

          // Check file size (max 5MB)
          $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes
          if ($file['size'] > $maxFileSize) {
              $file_err = "يجب أن لا يتجاوز حجم الملف (10 MB).";
          }
  }

// If there are no errors, proceed with registration
if (empty($uname_err) && empty($book_title_err) && empty($email_err) && empty($year_of_birth_err) && empty($phone_err) && empty($second_phone_err) && empty($address_err) && empty($book_type_err) && empty($book_level_err) && empty($subject_err) && empty($file_err) && empty($communicate_date_err)) {
    // Create a database connection

    include('../connect.php');
    session_start();
    $user_id = $_SESSION['id'];
    $inserted_by = $_SESSION['username'];

    $uploadDirectory = "authors_cv/"; // Set the path to your desired directory
    // Create the directory if it does not exist
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }
    $uploadedFile = '';
   if (!empty($_FILES['uploadedFile']['name'])) {
    // Generate a unique filename
    $uniqueFileName = uniqid() . "_" . basename($_FILES['uploadedFile']['name']);
    $uploadedFile = $uploadDirectory . $uniqueFileName;
    // Get the file type from the uploaded file
    $fileType = $_FILES['uploadedFile']['type'];
    // Move the uploaded file to the destination directory
    move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $uploadedFile);
}
    // Insert the new user record into the database inserted_by_username
    $sql_insert_user = "INSERT INTO authors (authorfullname, book_title, email, year_of_birth, phone, second_phone, authorAddress, author_type, created_at, inserted_by_username, communicate_date, fbLink, instaLink, youtubeLink, tiktokLink, userfile, filetype, notes, inserted_by_user_id, book_type_id, book_level_id, subject_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
    mysqli_stmt_bind_param($stmt_insert_user, "sssssssssssssssssiiii", $uname, $book_title, $email, $year_of_birth, $phone, $second_phone, $address, $author_type, $inserted_by, $communicate_date, $fbLink, $instaLink, $youtubeLink, $tiktokLink, $uploadedFile, $fileType, $notes, $user_id, $book_type, $book_level, $subject);
    
    mysqli_stmt_execute($stmt_insert_user);

        // Retrieve the user ID of the inserted user
        $author_id = mysqli_insert_id($conn);
        
        // Insert student-specific or teacher-specific data based on user role
        if ($author_type === 'student') {
          $studentLevel = trim($_POST["studentLevel"]);
          $studentSpecialty = trim($_POST["studentSpecialty"]);
          $baccalaureateRate = trim($_POST["baccalaureateRate"]);
          $baccalaureateYear = trim($_POST["baccalaureateYear"]);
          // Insert the student data into the student_data table
          $sql_insert_student_data = "INSERT INTO student_data (author_id, studentLevel, studentSpecialty, baccalaureateRate, baccalaureateYear) VALUES (?, ?, ?, ?, ?)";
          $stmt_insert_student_data = mysqli_prepare($conn, $sql_insert_student_data);
          mysqli_stmt_bind_param($stmt_insert_student_data, "issss", $author_id, $studentLevel, $studentSpecialty, $baccalaureateRate, $baccalaureateYear);
          mysqli_stmt_execute($stmt_insert_student_data);
        } elseif ($author_type === 'teacher') {
          $teacherExperience = trim($_POST["teacherExperience"]);
          $teacherCertificate = trim($_POST["teacherCertificate"]);
          $teacherRank = trim($_POST["teacherRank"]);
          $workFoundation = trim($_POST["workFoundation"]);
          // Insert the student data into the student_data table
          $sql_insert_teacher_data = "INSERT INTO teacher_data (author_id, teacherExperience, teacherCertificate, teacherRank, workFoundation) VALUES (?, ?, ?, ?, ?)";
          $stmt_insert_teacher_data = mysqli_prepare($conn, $sql_insert_teacher_data);
          mysqli_stmt_bind_param($stmt_insert_teacher_data, "issss", $author_id, $teacherExperience, $teacherCertificate, $teacherRank, $workFoundation);
          mysqli_stmt_execute($stmt_insert_teacher_data);
        }
        elseif ($author_type === 'inspector') {
          // Insert inspector-specific data
          $inspectorExperience = trim($_POST["inspectorExperience"]);
          $inspectorCertificate = trim($_POST["inspectorCertificate"]);
          $inspectorRank = trim($_POST["inspectorRank"]);
          $inspectorWorkFoundation = trim($_POST["inspectorWorkFoundation"]);
      
          $sql_insert_inspector_data = "INSERT INTO inspector_data (author_id, inspectorExperience, inspectorCertificate, inspectorRank, inspectorWorkFoundation) VALUES (?, ?, ?, ?, ?)";
          $stmt_insert_inspector_data = mysqli_prepare($conn, $sql_insert_inspector_data);
          mysqli_stmt_bind_param($stmt_insert_inspector_data, "issss", $author_id, $inspectorExperience, $inspectorCertificate, $inspectorRank, $inspectorWorkFoundation);
          mysqli_stmt_execute($stmt_insert_inspector_data);
      }
      elseif ($author_type === 'doctor') {
        // Insert doctor-specific data
        $specialty = trim($_POST["specialty"]);
        $drWorkFoundation = trim($_POST["drWorkFoundation"]);
    
        $sql_insert_doctor_data = "INSERT INTO doctor_data (author_id, specialty, drWorkFoundation) VALUES (?, ?, ?)";
        $stmt_insert_doctor_data = mysqli_prepare($conn, $sql_insert_doctor_data);
        mysqli_stmt_bind_param($stmt_insert_doctor_data, "iss", $author_id, $specialty, $drWorkFoundation);
        mysqli_stmt_execute($stmt_insert_doctor_data);
    } elseif ($author_type === 'trainer') {
        // Insert trainer-specific data
        $field = trim($_POST["field"]);
        $trainerExperience = trim($_POST["trainerExperience"]);
    
        $sql_insert_trainer_data = "INSERT INTO trainer_data (author_id, field, trainerExperience) VALUES (?, ?, ?)";
        $stmt_insert_trainer_data = mysqli_prepare($conn, $sql_insert_trainer_data);
        mysqli_stmt_bind_param($stmt_insert_trainer_data, "iss", $author_id, $field, $trainerExperience);
        mysqli_stmt_execute($stmt_insert_trainer_data);
    } elseif ($author_type === 'novelist') {
        // Insert novelist-specific data
        $novelistfield = trim($_POST["novelistfield"]);
    
        $sql_insert_novelist_data = "INSERT INTO novelist_data (author_id, novelistfield) VALUES (?, ?)";
        $stmt_insert_novelist_data = mysqli_prepare($conn, $sql_insert_novelist_data);
        mysqli_stmt_bind_param($stmt_insert_novelist_data, "is", $author_id, $novelistfield);
        mysqli_stmt_execute($stmt_insert_novelist_data);
    }
    // Store the success message in a session variable
    $_SESSION['register_success_msg'] = "تم إضافة المؤلف بنجاح.";
    // Registration successful, redirect to login page or dashboard
    header("Location: add_author.php");
    exit();
    mysqli_stmt_close($stmt_insert_user);
    // Close the connection
    mysqli_close($conn);
  }
}
session_start();
include('secure.php');
$register_success_msg = isset($_SESSION['register_success_msg']) ? $_SESSION['register_success_msg'] : "";
include('header.php');
?>
    <div class="container-fluid py-4">
          <!-- Display the flash message if it exists -->
<?php if (isset($_SESSION['register_success_msg'])) { ?>
    <div class="progress-container">
        <div class="progress-bar" id="myProgressBar">
            <div class="progress-text">يتم إدخال المؤلف</div>
        </div>
    </div>
    <div class="alert alert-success mt-3 text-white" role="alert" id="successMessage" style="display: none;">
        <?php echo $_SESSION['register_success_msg']; ?>
    </div>
    <style>
        .progress-container {
            height: 30px;
            background-color: #f5f5f5;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0;
            background-color: #4caf50;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            position: relative;
        }

        .progress-text {
            position: absolute;
        }
    </style>
    <script>
        var progressBar = document.getElementById("myProgressBar");
        var progressText = document.querySelector(".progress-text");
        var successMessage = document.getElementById("successMessage");

        // Simulate progress
        var progress = 0;
        var interval = setInterval(function () {
            progress += 10;
            progressBar.style.width = progress + "%";
            progressText.textContent = "يتم إدخال المؤلف " + progress + "%";
            if (progress >= 100) {
                clearInterval(interval);
                progressBar.style.display = "none";
                progressText.style.display = "none";
                successMessage.style.display = "block";
            }
        }, 250);
    </script>
<?php unset($_SESSION['register_success_msg']); }  ?>


        <form role="form" action="" method="post" enctype="multipart/form-data">
            <h4 class="mb-3">إضافة مؤلف</h4>

            <div class="border rounded p-4 shadow">
    <h6 class="border-bottom pb-2 mb-3">معلومات المؤلف</h6>
           <div class="d-flex">
           <div class="input-group input-group-outline m-3">
                <select class="form-control" id="author_type" name="author_type" required>
                  <option value="" disabled selected>-- نوع المؤلف --</option>
                  <option value="student">طالب</option>
                  <option value="teacher">أستاذ</option>
                  <option value="inspector">مفتش</option>
                  <option value="doctor">طبيب</option>
                  <option value="trainer">مدرب</option>
                  <option value="novelist">روائي</option>
                </select>
              </div>
              <div class="input-group input-group-outline my-3">
              <?php if (empty($uname)): ?>
                <label for="username" class="form-label">اسم المؤلف</label>
              <?php endif; ?>
              <input type="text" class="form-control <?php echo (!empty($uname_err)) ? 'is-invalid' : ''; ?>"
                id="username" name="txt_uname" value="<?php echo $uname; ?>" required
                <?php if (!empty($uname)) echo 'placeholder="اسم المؤلف"'; ?> />
              <span class="invalid-feedback"><?php echo $uname_err; ?></span>
            </div>

              </div>
              <div class="d-flex">
              <div class="input-group input-group-outline m-3">
              <?php if (empty($phone)): ?>
                <label for="phone" class="form-label">الهاتف</label>
              <?php endif; ?>
              <input type="text" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                id="phone" name="txt_phone" value="<?php echo $phone; ?>" required
                <?php if (!empty($phone)) echo 'placeholder="الهاتف"'; ?> />
              <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>

            <div class="input-group input-group-outline my-3">
              <?php if (empty($second_phone)): ?>
                <label for="second_phone" class="form-label">الهاتف الثاني</label>
              <?php endif; ?>
              <input type="text" class="form-control <?php echo (!empty($second_phone_err)) ? 'is-invalid' : ''; ?>"
                id="second_phone" name="second_phone" value="<?php echo $second_phone; ?>"
                <?php if (!empty($second_phone)) echo 'placeholder="الهاتف الثاني"'; ?> />
              <span class="invalid-feedback"><?php echo $second_phone_err; ?></span>
            </div>

              </div>

              <div class="d-flex">
              <div class="input-group input-group-outline m-3">
              <?php if (empty($address)): ?>
                <label for="address" class="form-label">العنوان</label>
              <?php endif; ?>
              <input type="text" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"
                id="address" name="txt_address" value="<?php echo $address; ?>" required
                <?php if (!empty($address)) echo 'placeholder="العنوان"'; ?> />
              <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>

              <div class="input-group input-group-outline my-3">
              <?php if (empty($year_of_birth)): ?>
                <label for="year_of_birth" class="form-label">سنة الميلاد (YYYY)</label>
              <?php endif; ?>
              <input type="text" class="form-control <?php echo (!empty($year_of_birth_err)) ? 'is-invalid' : ''; ?>"
                id="year_of_birth" name="txt_year_of_birth"
                value="<?php echo $year_of_birth; ?>" required
                <?php if (!empty($year_of_birth)) echo 'placeholder="سنة الميلاد (YYYY)"'; ?> />
              <span class="invalid-feedback"><?php echo $year_of_birth_err; ?></span>
            </div>
            
              </div>

              <div class="d-flex">
                <div class="col-md-6 ps-3">
                  <div class="input-group input-group-outline m-3">
                  <?php if (empty($email)): ?>
                    <label for="email" class="form-label">الإيميل</label>
                  <?php endif; ?>
                  <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                    id="email" name="txt_email" value="<?php echo $email; ?>"
                    <?php if (!empty($email)) echo 'placeholder="الإيميل"'; ?> />
                  <span class="invalid-feedback"><?php echo $email_err; ?></span>
                  </div>
                </div>
              </div>
            
               <!-- Student Specific Inputs -->
            <div class="d-flex">
            <div class="input-group input-group-outline m-3" id="studentInputs" style="display: none;">
          
                <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                    <label for="studentLevel" class="form-label">المستوى</label>
                    <input type="text" class="form-control" id="studentLevel" name="studentLevel" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="input-group input-group-outline me-3">
                    <label for="studentSpecialty" class="form-label">التخصص</label>
                    <input type="text" class="form-control" id="studentSpecialty" name="studentSpecialty" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                    <label for="baccalaureateRate" class="form-label">معدل البكالوريا</label>
                    <input type="text" class="form-control" id="baccalaureateRate" name="baccalaureateRate" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="input-group input-group-outline me-3">
                    <label for="baccalaureateYear" class="form-label">سنة البكالوريا</label>
                    <input type="text" class="form-control" id="baccalaureateYear" name="baccalaureateYear" required>
                </div>
                </div>
            </div>
            </div>

            <!-- Teacher Specific Inputs -->
            <div class="d-flex">
            <div class="input-group input-group-outline m-3" id="teacherInputs" style="display: none;">
                <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                    <label for="teacherExperience" class="form-label">الخبرة</label>
                    <input type="text" class="form-control" id="teacherExperience" name="teacherExperience" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="input-group input-group-outline me-3">
                    <label for="teacherCertificate" class="form-label">الشهادة</label>
                    <input type="text" class="form-control" id="teacherCertificate" name="teacherCertificate" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="input-group input-group-outline mb-3">
                    <label for="teacherRank" class="form-label">الرتبة</label>
                    <input type="text" class="form-control" id="teacherRank" name="teacherRank" required>
                </div>
                </div>
                <div class="col-md-6">
                <div class="input-group input-group-outline me-3">
                    <label for="workFoundation" class="form-label">مؤسسة العمل</label>
                    <input type="text" class="form-control" id="workFoundation" name="workFoundation" required>
                </div>
                </div>
            </div>
            </div>

                <!-- Inspector Specific Inputs -->
                <div class="d-flex">
                <div class="input-group input-group-outline m-3" id="inspectorInputs" style="display: none;">
                    <div class="col-md-6">
                    <div class="input-group input-group-outline mb-3">
                        <label for="inspectorExperience" class="form-label">الخبرة</label>
                        <input type="text" class="form-control" id="inspectorExperience" name="inspectorExperience" required>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="input-group input-group-outline me-3">
                        <label for="inspectorCertificate" class="form-label">الشهادة</label>
                        <input type="text" class="form-control" id="inspectorCertificate" name="inspectorCertificate" required>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="input-group input-group-outline mb-3">
                        <label for="inspectorRank" class="form-label">الرتبة</label>
                        <input type="text" class="form-control" id="inspectorRank" name="inspectorRank" required>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="input-group input-group-outline me-3">
                        <label for="inspectorWorkFoundation" class="form-label">الولاية</label>
                        <input type="text" class="form-control" id="inspectorWorkFoundation" name="inspectorWorkFoundation" required>
                    </div>
                    </div>
                </div>
                </div>

               <!-- Doctor Specific Inputs -->
                <div class="d-flex">
                <div class="input-group input-group-outline m-3" id="doctorInputs" style="display: none;">
                    <div class="col-md-6">
                    <div class="input-group input-group-outline mb-3">
                        <label for="specialty" class="form-label">التخصص</label>
                        <input type="text" class="form-control" id="specialty" name="specialty" required>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="input-group input-group-outline me-3">
                        <label for="drWorkFoundation" class="form-label">مكان العمل</label>
                        <input type="text" class="form-control" id="drWorkFoundation" name="drWorkFoundation" required>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Trainer Specific Inputs -->
                <div class="d-flex">
                <div class="input-group input-group-outline m-3" id="trainerInputs" style="display: none;">
                    <div class="col-md-6">
                    <div class="input-group input-group-outline mb-3">
                        <label for="field" class="form-label">المجال</label>
                        <input type="text" class="form-control" id="field" name="field" required>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="input-group input-group-outline me-3">
                        <label for="trainerExperience" class="form-label">الخبرة</label>
                        <input type="text" class="form-control" id="trainerExperience" name="trainerExperience" required>
                    </div>
                    </div>
                </div>
                </div>
                <div class="d-flex">
                <div class="col-md-6 ps-3">
                    <!-- novelist Specific Inputs -->
                <div class="input-group input-group-outline m-3" id="novelistInputs" style="display: none;">
                  <label for="novelistfield" class="form-label">المجال</label>
                  <input type="text" class="form-control" id="novelistfield" name="novelistfield" required>
                </div>
                </div>
                </div>

                </div>
            <!-- Author Info End-->
             
              
                <!-- Book Info Section-->
              <div class="border rounded p-4 my-4 shadow">
          <h6 class="border-bottom pb-2 mb-3">معلومات الكتاب</h6>
              <div class="d-flex">
                <div class="input-group input-group-outline m-3">
                <?php if (empty($book_title)): ?>
                  <label for="book_title" class="form-label">عنوان الكتاب</label>
                <?php endif; ?>
                <input type="text" class="form-control <?php echo (!empty($book_title_err)) ? 'is-invalid' : ''; ?>"
                  id="book_title" name="book_title" value="<?php echo $book_title; ?>" required/>
                <span class="invalid-feedback"><?php echo $book_title_err; ?></span>
              </div>

                <div class="input-group input-group-outline my-3">
                  <select class="form-control" id="book_type" name="book_type" required>
                    <option value="" disabled selected>-- نوع الكتاب --</option>
                    <?php while ($row = mysqli_fetch_assoc($result_book_types)) { ?>
                      <option value="<?php echo $row['id']; ?>"><?php echo $row['type_name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                </div>

                <div class="d-flex">
                <div class="input-group input-group-outline m-3">
                  <select class="form-control" id="book_level" name="book_level" required>
                    <option value="" disabled selected>-- مستوى الكتاب --</option>
                    <?php while ($row = mysqli_fetch_assoc($result_book_levels)) { ?>
                      <option value="<?php echo $row['id']; ?>" data-book-type-id="<?php echo $row['book_type_id']; ?>">
                        <?php echo $row['level_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
               
                <div class="input-group input-group-outline my-3">
                  <select class="form-control" id="subject" name="subject" required>
                    <option value="">-- المادة --</option>
                    <?php while ($row = mysqli_fetch_assoc($result_subjects)) { ?>
                      <option value="<?php echo $row['id']; ?>" data-book-level-id="<?php echo $row['book_level_id']; ?>">
                        <?php echo $row['subject_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              
              <div class="form-group col-md-6 ps-3">
                        <label class="form-label me-4">تاريخ التواصل :</label>
                        <input type="date" name="communicate_date" class="form-control border pe-2 mb-3 me-3 <?php echo (!empty($communicate_date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($communicate_date); ?>" required>
                        <span class="invalid-feedback"><?php echo $communicate_date_err; ?></span>
                    </div>
              </div>
              
              <!-- Social Section-->
              <div class="border rounded p-4 shadow">
                <h6 class="border-bottom pb-2 mb-3">معلومات وسائل التواصل</h6>
                    <div class="d-flex">
                      <div class="input-group input-group-outline m-3">
                      <?php if (empty($fbLink)): ?>
                      <label for="fbLink" class="form-label">رابط الفيسبوك</label>
                    <?php endif; ?>
                    <input type="text" class="form-control" id="fbLink" name="fbLink" value="<?php echo $fbLink; ?>"
                      <?php if (!empty($fbLink)) echo 'placeholder="رابط الفيسبوك"'; ?> />
                    </div>
                    <div class="input-group input-group-outline my-3">
                    <?php if (empty($instaLink)): ?>
                    <label for="instaLink" class="form-label">رابط الإنستغرام</label>
                  <?php endif; ?>
                  <input type="text" class="form-control" id="instaLink" name="instaLink" value="<?php echo $instaLink; ?>"
                    <?php if (!empty($instaLink)) echo 'placeholder="رابط الإنستغرام"'; ?> />
                    </div>
                    </div>

                    <div class="d-flex">
                    <div class="input-group input-group-outline m-3">
                    <?php if (empty($youtubeLink)): ?>
                    <label for="youtubeLink" class="form-label">رابط اليوتيوب</label>
                  <?php endif; ?>
                  <input type="text" class="form-control" id="youtubeLink" name="youtubeLink" value="<?php echo $youtubeLink; ?>"
                    <?php if (!empty($youtubeLink)) echo 'placeholder="رابط اليوتيوب"'; ?> />
                    </div>
                    <div class="input-group input-group-outline my-3">
                    <?php if (empty($tiktokLink)): ?>
                    <label for="tiktokLink" class="form-label">رابط التيكتوك</label>
                  <?php endif; ?>
                  <input type="text" class="form-control" id="tiktokLink" name="tiktokLink" value="<?php echo $tiktokLink; ?>"
                    <?php if (!empty($tiktokLink)) echo 'placeholder="رابط التيكتوك"'; ?> />
                    </div>
                    </div> 

              </div>
            <!-- File Section-->
            <div class="border rounded p-4 shadow">
               <h6 class="border-bottom pb-2 mb-3">السيرة الذاتية + ملاحظات</h6>
                  <div class="input-group input-group-outline m-3 ps-3">
                    <input type="file" class="form-control <?php echo (!empty($file_err)) ? 'is-invalid' : ''; ?>" id="file" name="uploadedFile" />
                      <span class="invalid-feedback"><?php echo $file_err; ?></span>
              </div>
              <div class="input-group input-group-outline m-3 ps-3">
                  <label for="notes" class="form-label">ملاحظات</label>
              <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo $notes; ?></textarea>
          </div>

            </div>    
                <button type="submit" name="but_submit" class="btn bg-gradient-primary" >إضـافة</button>
                <?php if (!empty($register_err)) { ?>
                <div class="alert alert-danger mt-3" role="alert">
                  <?php echo $register_err; ?>
                </div>
              <?php } ?>
        </form>
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
      .catch(error => console.error('Error fetching subjects:', error));
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
  // Function to display special input for authors types 

  document.getElementById('author_type').addEventListener('change', function() {
  const roleInputs = {
    student: document.getElementById('studentInputs'),
    teacher: document.getElementById('teacherInputs'),
    inspector: document.getElementById('inspectorInputs'),
    doctor: document.getElementById('doctorInputs'),
    trainer: document.getElementById('trainerInputs'),
    novelist: document.getElementById('novelistInputs')
    // Add other role inputs here
  };

  const selectedRole = this.value;
  for (const role in roleInputs) {
    if (role === selectedRole) {
      roleInputs[role].style.display = '';
    } else {
      roleInputs[role].style.display = 'none';
    }
  }
});

</script>

<?php
include('footer.php');
?>

          