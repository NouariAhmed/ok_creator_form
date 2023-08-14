<?php
// Initialize variables
$uname = $email = $year_of_birth = $phone = $address = $book_type = $book_level = $subject = "";
$uname_err = $email_err = $year_of_birth_err = $phone_err = $address_err = $book_type_err = $book_level_err = $subject_err = $register_err = "";
include('connect.php');

// Fetch book types from the database
$sql_fetch_book_types = "SELECT id, type_name FROM book_types";
$result_book_types = mysqli_query($conn, $sql_fetch_book_types);

// Fetch book levels from the database
$sql_fetch_book_levels = "SELECT id, level_name, book_type_id FROM book_levels";
$result_book_levels = mysqli_query($conn, $sql_fetch_book_levels);

// Fetch subjects from the database
$sql_fetch_subjects = "SELECT id, subject_name, book_level_id FROM subjects";
$result_subjects = mysqli_query($conn, $sql_fetch_subjects);

// Close the connection (since we don't need it anymore for the form rendering)
mysqli_close($conn);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // Get the form data
 $uname = trim($_POST["txt_uname"]);
 $email = trim($_POST["txt_email"]);
 $year_of_birth = trim($_POST["txt_year_of_birth"]);
 $phone = trim($_POST["txt_phone"]);
 $address = trim($_POST["txt_address"]);
 $book_type = $_POST["book_type"];
 $book_level = $_POST["book_level"];
 $subject = $_POST["subject"];
 $author_type = $_POST["author_type"];

  // Validate username
  if (empty($uname)) {
    $uname_err = "Please enter a full author name.";
  } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $uname)) {
    $uname_err = "author name can only contain letters, numbers, and underscores.";
  }

  // Validate email
  if (empty($email)) {
    $email_err = "Please enter an email address.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format.";
  }

  // Validate year of birth
  if (empty($year_of_birth)) {
    $year_of_birth_err = "Please enter a year of birth.";
  } elseif (!is_numeric($year_of_birth) || strlen($year_of_birth) !== 4) {
    $year_of_birth_err = "Year of birth must be a 4-digit number.";
  }

  // Validate phone
  if (empty($phone)) {
    $phone_err = "Please enter author phone number.";
  } elseif (!preg_match("/^\+?\d{1,4}?\s?\(?\d{1,4}?\)?[0-9\- ]+$/", $phone)) {
    $phone_err = "Invalid phone number format.";
  }

  // Validate address
  if (empty($address)) {
    $address_err = "Please enter author address.";
  }

  // If there are no errors, proceed with registration
  if (empty($uname_err) && empty($email_err) && empty($year_of_birth_err) && empty($phone_err) && empty($address_err) && empty($book_type_err) && empty($book_level_err) && empty($subject_err)) {
    // Create a database connection
    include('connect.php');

    // Insert the new user record into the database
    $sql_insert_user = "INSERT INTO authors (authorfullname, email, year_of_birth, phone, authorAddress, author_type, book_type_id, book_level_id, subject_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
    mysqli_stmt_bind_param($stmt_insert_user, "ssssssiii", $uname, $email, $year_of_birth, $phone, $address, $author_type, $book_type, $book_level, $subject);
    mysqli_stmt_execute($stmt_insert_user);
    // Registration successful, show success message

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
          $inspectorCertificate = trim($_POST["InspectorCertificate"]);
          $inspectorRank = trim($_POST["inspectorRank"]);
          $inspectorWorkFoundation = trim($_POST["inspectorWorkFoundation"]);
      
          $sql_insert_inspector_data = "INSERT INTO inspector_data (author_id, inspectorExperience, InspectorCertificate, inspectorRank, inspectorWorkFoundation) VALUES (?, ?, ?, ?, ?)";
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
    session_start();
    $_SESSION['register_success_msg'] = "Author Registration successful.";
    // Registration successful, redirect to login page or dashboard
    header("Location: okForm.php");
    exit();

    // Close the connection
    mysqli_close($conn);
  }
}
session_start();
$register_success_msg = isset($_SESSION['register_success_msg']) ? $_SESSION['register_success_msg'] : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registration Form</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" />

  <link rel="stylesheet" media="screen" href="https://fontlibrary.org/face/droid-arabic-kufi" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <link rel="stylesheet" href="style.css">
</head>

<body class="main-bg d-flex justify-content-center align-items-center">
  <!-- Registration Form -->
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-lg-4 col-md-6 col-sm-6 align-self-center">
        <div class="card shadow">
          <div class="card-title text-center border-bottom">
            <h2 class="p-3">Register</h2>
          </div>
          <div class="card-body">
            <form method="post" action="">
                                         <!-- Display the flash message if it exists -->
                                         <?php if (isset($_SESSION['register_success_msg'])) { ?>
                            <div class="alert alert-success mt-3" role="alert">
                                <?php echo $_SESSION['register_success_msg']; ?>
                            </div>
                            <?php unset($_SESSION['register_success_msg']); }  ?>
              <div class="mb-4">
                <label for="username" class="form-label">Fullname</label>
                <input type="text" class="form-control <?php echo (!empty($uname_err)) ? 'is-invalid' : ''; ?>"
                  id="username" name="txt_uname" value="<?php echo $uname; ?>" />
                <span class="invalid-feedback"><?php echo $uname_err; ?></span>
              </div>
              <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                  id="email" name="txt_email" value="<?php echo $email; ?>" />
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
              </div>
              <div class="mb-4">
                <label for="year_of_birth" class="form-label">Year of Birth (YYYY)</label>
                <input type="text" class="form-control <?php echo (!empty($year_of_birth_err)) ? 'is-invalid' : ''; ?>"
                  id="year_of_birth" name="txt_year_of_birth" value="<?php echo $year_of_birth; ?>" />
                <span class="invalid-feedback"><?php echo $year_of_birth_err; ?></span>
              </div>
              <div class="mb-4">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" id="phone"
                  name="txt_phone" value="<?php echo $phone; ?>" />
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
              </div>
              <div class="mb-4">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" id="address"
                  name="txt_address" value="<?php echo $address; ?>" />
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
              </div>
                          <div class="mb-4">
                  <label for="book_type" class="form-label">Book Type</label>
                  <select class="form-select" id="book_type" name="book_type" required>
                    <option value="">Select Book Type</option>
                    <?php while ($row = mysqli_fetch_assoc($result_book_types)) { ?>
                      <option value="<?php echo $row['id']; ?>"><?php echo $row['type_name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="mb-4">
                  <label for="book_level" class="form-label">Book Level</label>
                  <select class="form-select" id="book_level" name="book_level" required>
                    <option value="">Select Book Level</option>
                    <?php while ($row = mysqli_fetch_assoc($result_book_levels)) { ?>
                      <option value="<?php echo $row['id']; ?>" data-book-type-id="<?php echo $row['book_type_id']; ?>">
                        <?php echo $row['level_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="mb-4">
                  <label for="subject" class="form-label">Subject</label>
                  <select class="form-select" id="subject" name="subject" required>
                    <option value="">Select Subject</option>
                    <?php while ($row = mysqli_fetch_assoc($result_subjects)) { ?>
                      <option value="<?php echo $row['id']; ?>" data-book-level-id="<?php echo $row['book_level_id']; ?>">
                        <?php echo $row['subject_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                  <div class="mb-4">
                <label for="author_type" class="form-label">User Role</label>
                <select class="form-select" id="author_type" name="author_type" required>
                  <option value="" disabled selected>Select User Role</option>
                  <option value="student">Student</option>
                  <option value="teacher">Teacher</option>
                  <option value="inspector">inspector</option>
                  <option value="doctor">doctor</option>
                  <option value="trainer">trainer</option>
                  <option value="novelist">novelist</option>
                </select>
              </div>
              <!-- Student Specific Inputs -->
                   <div class="mb-4" id="studentInputs" style="display: none;">
                  <label for="studentLevel" class="form-label">Student Level</label>
                  <input type="text" class="form-control" id="studentLevel" name="studentLevel">
                  <label for="studentSpecialty" class="form-label">Student Specialty</label>
                  <input type="text" class="form-control" id="studentSpecialty" name="studentSpecialty">
                  <label for="baccalaureateRate" class="form-label">Baccalaureate Rate</label>
                  <input type="text" class="form-control" id="baccalaureateRate" name="baccalaureateRate">
                  <label for="baccalaureateYear" class="form-label">Baccalaureate Year</label>
                  <input type="text" class="form-control" id="baccalaureateYear" name="baccalaureateYear">
                </div>
                 <!-- Teacher Specific Inputs -->
                <div class="mb-4" id="teacherInputs" style="display: none;">
                  <label for="teacherExperience" class="form-label">Teacher Experience</label>
                  <input type="text" class="form-control" id="teacherExperience" name="teacherExperience">
                  <label for="teacherCertificate" class="form-label">Teacher Certificate</label>
                  <input type="text" class="form-control" id="teacherCertificate" name="teacherCertificate">
                  <label for="teacherRank" class="form-label">Teacher Rank</label>
                  <input type="text" class="form-control" id="teacherRank" name="teacherRank">
                  <label for="workFoundation" class="form-label">Work Experience</label>
                  <input type="text" class="form-control" id="workFoundation" name="workFoundation">
                </div>
                 <!-- Inspector Specific Inputs -->
                <div class="mb-4" id="inspectorInputs" style="display: none;">
                  <label for="inspectorExperience" class="form-label">Inspector Experience</label>
                  <input type="text" class="form-control" id="inspectorExperience" name="inspectorExperience">
                  <label for="InspectorCertificate" class="form-label">Inspector Certificate</label>
                  <input type="text" class="form-control" id="InspectorCertificate" name="InspectorCertificate">
                  <label for="inspectorRank" class="form-label">Inspector Rank</label>
                  <input type="text" class="form-control" id="inspectorRank" name="inspectorRank">
                  <label for="inspectorWorkFoundation" class="form-label">Inspector Work Experience</label>
                  <input type="text" class="form-control" id="inspectorWorkFoundation" name="inspectorWorkFoundation">
                </div>
                  <!-- Doctor Specific Inputs -->
                <div class="mb-4" id="doctorInputs" style="display: none;">
                  <label for="specialty" class="form-label">specialty</label>
                  <input type="text" class="form-control" id="specialty" name="specialty">
                  <label for="drWorkFoundation" class="form-label">Dr Work Foundation</label>
                  <input type="text" class="form-control" id="drWorkFoundation" name="drWorkFoundation">
                </div>
                   <!-- trainer Specific Inputs -->
                <div class="mb-4" id="trainerInputs" style="display: none;">
                  <label for="field" class="form-label">The field</label>
                  <input type="text" class="form-control" id="field" name="field">
                  <label for="trainerExperience" class="form-label">trainer Experience</label>
                  <input type="text" class="form-control" id="trainerExperience" name="trainerExperience">
                </div>
                    <!-- novelist Specific Inputs -->
                <div class="mb-4" id="novelistInputs" style="display: none;">
                  <label for="novelistfield" class="form-label">The field</label>
                  <input type="text" class="form-control" id="novelistfield" name="novelistfield">
                </div>
              <div class="d-grid">
                <button type="submit" class="btn text-light main-bg" name="but_submit">Register</button>
              </div>
              <?php if (!empty($register_err)) { ?>
                <div class="alert alert-danger mt-3" role="alert">
                  <?php echo $register_err; ?>
                </div>
              <?php } ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- JavaScript code using Ajax to handle dynamic updates based on Book Type and Book Level selections -->
<!-- JavaScript code using Ajax to handle dynamic updates based on Book Type and Book Level selections -->
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
    fetch('get_book_levels.php?type_id=' + bookType)
      .then(response => response.json())
      .then(data => {
        // Generate the Book Level select options
        const bookLevelsOptions = data.map(level => `<option value="${level.id}">${level.level_name}</option>`);
        // Display the Book Level select
        bookLevelSelect.innerHTML = '<option value="">Select Book Level</option>' + bookLevelsOptions.join('');
        // Enable the Book Level select
        bookLevelSelect.disabled = false;
        // Clear and disable the Subject select
        clearSubject();
      })
      .catch(error => console.error('Error fetching book levels:', error));
  }

  // Function to fetch subjects using Ajax
  function fetchSubjects(bookLevel) {
    fetch('get_subjects.php?level_id=' + bookLevel)
      .then(response => response.json())
      .then(data => {
        // Generate the Subject select options
        const subjectsOptions = data.map(subject => `<option value="${subject.id}">${subject.subject_name}</option>`);
        // Display the Subject select
        subjectSelect.innerHTML = '<option value="">Select Subject</option>' + subjectsOptions.join('');
        // Enable the Subject select
        subjectSelect.disabled = false;
      })
      .catch(error => console.error('Error fetching subjects:', error));
  }

  // Function to clear and disable the Subject select
  function clearSubject() {
    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
    subjectSelect.disabled = true;
  }

  // Function to clear and disable the Book Level and Subject selects
  function clearBookLevelAndSubject() {
    bookLevelSelect.innerHTML = '<option value="">Select Book Level</option>';
    bookLevelSelect.disabled = true;
    clearSubject();
  }
  // Function to display special input for authors types 

  document.getElementById('author_type').addEventListener('change', function() {
  const studentInputs = document.getElementById('studentInputs');
  const teacherInputs = document.getElementById('teacherInputs');
  const inspectorInputs = document.getElementById('inspectorInputs');
  const doctorInputs = document.getElementById('doctorInputs');
  const trainerInputs = document.getElementById('trainerInputs');
  const novelistInputs = document.getElementById('novelistInputs');

  studentInputs.style.display = 'none';
  teacherInputs.style.display = 'none';
  inspectorInputs.style.display = 'none';
  doctorInputs.style.display = 'none';
  trainerInputs.style.display = 'none';
  novelistInputs.style.display = 'none';

  const selectedValue = this.value;
  if (selectedValue === 'student') {
    studentInputs.style.display = 'block';
  } else if (selectedValue === 'teacher') {
    teacherInputs.style.display = 'block';
  } else if (selectedValue === 'inspector') {
    inspectorInputs.style.display = 'block';
  } else if (selectedValue === 'doctor') {
    doctorInputs.style.display = 'block';
  } else if (selectedValue === 'trainer') {
    trainerInputs.style.display = 'block';
  } else if (selectedValue === 'novelist') {
    novelistInputs.style.display = 'block';
  }
});

</script>



</body>
</html>
