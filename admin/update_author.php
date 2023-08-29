<?php
session_start();
ob_start(); // Start output buffering
include('secure.php');
include('header.php');
include('../connect.php');
include('../dash_functions.php'); 
$uname = $book_title = $email = $year_of_birth = $phone = $second_phone = $authorAddress = $book_type_id = $book_level_id = $subject_id = $fbLink = $instaLink = $youtubeLink = $tiktokLink ="";
$uname_err = $book_title_err = $email_err = $year_of_birth_err = $phone_err = $second_phone_err = $address_err = $file_err = $book_type_err = $book_level_err = $subject_err = "";

?>
<div class="container-fluid py-4">
    <?php
    if (isset($_SESSION['create_update_success']) && $_SESSION['create_update_success'] === true) {
        // Unset the session variable to avoid displaying the message on page refresh
        unset($_SESSION['create_update_success']);
        // Redirect to the display_authors page with a success message
        header("Location: display_authors.php?create_update_success=1");
        exit;
    }

    if (isset($_SESSION['item_not_found']) && $_SESSION['item_not_found'] === true) {
        // Unset the session variable to avoid displaying the message on page refresh
        unset($_SESSION['item_not_found']);
        // Redirect to the display_authors page with a success message
        header("Location: display_authors.php?item_not_found=1");
        exit;
    }

    // Database connection configuration
    include('../connect.php');

    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $author_type = isset($_GET['type']) ? $_GET['type'] : '';
    // For update an dynamic get book level ....
    $book_type_id = isset($_GET['book_type_id']) ? $_GET['book_type_id'] : '';

    if (!empty($id)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM authors WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $item = mysqli_fetch_assoc($result);
            $uname = htmlspecialchars($item['authorfullname']);
            $book_title = htmlspecialchars($item['book_title']);
            $email = htmlspecialchars($item['email']);
            $year_of_birth = htmlspecialchars($item['year_of_birth']);
            $phone = htmlspecialchars($item['phone']);
            $second_phone = htmlspecialchars($item['second_phone']);
            $authorAddress = htmlspecialchars($item['authorAddress']);
            $notes = htmlspecialchars($item['notes']);
            $author_status = htmlspecialchars($item['author_status']);
        
            $fbLink = htmlspecialchars($item['fbLink']);
            $instaLink = htmlspecialchars($item['instaLink']);
            $youtubeLink = htmlspecialchars($item['youtubeLink']);
            $tiktokLink = htmlspecialchars($item['tiktokLink']);

            $book_type_id = $item['book_type_id'];
            $book_level_id = $item['book_level_id'];
            $subject_id = $item['subject_id'];

            // Fetch student-specific data using the function
            if ($author_type === 'student') {
                $studentData = getStudentData($conn, $id);
                $studentLevel = $studentData['studentLevel'];
                $studentSpecialty = $studentData['studentSpecialty'];
                $baccalaureateRate = $studentData['baccalaureateRate'];
                $baccalaureateYear = $studentData['baccalaureateYear'];
            }elseif ($author_type === 'teacher') {
                $teacherData = getTeacherData($conn, $id);
                $teacherExperience = $teacherData['teacherExperience'];
                $teacherCertificate = $teacherData['teacherCertificate'];
                $teacherRank = $teacherData['teacherRank'];
                $workFoundation = $teacherData['workFoundation'];
            }elseif ($author_type === 'inspector') {
                $inspectorData = getInspectorData($conn, $id);
                $inspectorExperience = $inspectorData['inspectorExperience'];
                $inspectorCertificate = $inspectorData['inspectorCertificate'];
                $inspectorRank = $inspectorData['inspectorRank'];
                $inspectorWorkFoundation = $inspectorData['inspectorWorkFoundation'];
            }elseif ($author_type === 'doctor') {
                $doctorData = getDoctorData($conn, $id);
                $specialty = $doctorData['specialty'];
                $drWorkFoundation = $doctorData['drWorkFoundation'];            
            }elseif ($author_type === 'trainer') {
                $trainerData = getTrainerData($conn, $id);
                $field = $trainerData['field'];
                $trainerExperience = $trainerData['trainerExperience'];

            }elseif ($author_type === 'novelist') {
                $novelistData = getNovelistData($conn, $id);
                $novelistfield = $novelistData['novelistfield'];            
            }
        } else {
            $_SESSION['item_not_found'] = true;
            // Close the statement result
            mysqli_stmt_close($stmt);
            // Redirect to the display_authors page after item not found
            header("Location: display_authors.php");
            exit;
        }

        // Close the statement result
        mysqli_stmt_close($stmt);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['updateData'])) {
           // Get the form data
            $uname = trim($_POST["authorfullname"]);
            $book_title = trim($_POST["book_title"]);
            $email = trim($_POST["email"]);
            $year_of_birth = trim($_POST["year_of_birth"]);
            $phone = trim($_POST["phone"]);
            $second_phone = trim($_POST["second_phone"]);
            $authorAddress = trim($_POST["authorAddress"]);
            $notes = trim($_POST["notes"]);
            $author_status = trim($_POST["author_status"]);
            
            $fbLink = trim($_POST["fbLink"]);
            $instaLink = trim($_POST["instaLink"]);
            $youtubeLink = trim($_POST["youtubeLink"]);
            $tiktokLink = trim($_POST["tiktokLink"]);

            $book_type_id = trim($_POST["book_type_id"]);
            $book_level_id = trim($_POST["book_level_id"]);
            $subject_id = trim($_POST["subject_id"]);
            // Validate username
            if (empty($uname)) {
                $uname_err = "يرجى إدخال الإسم الكامل للمؤلف.";
            } elseif (!preg_match("/^[\p{L}\p{N}_\s]+$/u", $uname)) {
                $uname_err = "إسم المؤلف يجب أن يحتوي على حروف.";
            }
            // Validate username
            if (empty($book_title)) {
                $book_title_err = "يرجى إدخال عنوان الكتاب.";
            } elseif (!preg_match("/^[\p{L}\p{N}_\s]+$/u", $book_title)) {
                $book_title_err = "عنوان الكتاب يجب أن يحتوي على حروف.";
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
                $existingPhoneQuery = "SELECT id, authorfullname FROM authors WHERE (phone = ? OR second_phone = ?) AND id != ?";
                $stmt_existingPhone = mysqli_prepare($conn, $existingPhoneQuery);
                mysqli_stmt_bind_param($stmt_existingPhone, "ssi", $phone, $phone, $id);
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
                    $existingSecondPhoneQuery = "SELECT id, authorfullname FROM authors WHERE (phone = ? OR second_phone = ?) AND id != ?";
                    $stmt_existingSecondPhone = mysqli_prepare($conn, $existingSecondPhoneQuery);
                    mysqli_stmt_bind_param($stmt_existingSecondPhone, "ssi", $second_phone, $second_phone, $id);
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


            // Validate authorAddress
            if (empty($authorAddress)) {
                $address_err = "يرجى إدخال عنوان إقامة المؤلف.";
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
            if (empty($uname_err) && empty($book_title_err) && empty($email_err) && empty($year_of_birth_err) && empty($phone_err) && empty($second_phone_err) && empty($address_err) && empty($file_err)  && empty($book_type_err) && empty($book_level_err) && empty($subject_err)) {
                // Create a database connection
               // include('../connect.php');
                $uploadDirectory = "authors_cv/"; // Set the path to your desired directory
                // Create the directory if it does not exist
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0755, true);
                }
                
                $uploadedFile = "";
               if (!empty($_FILES['uploadedFile']['name'])) {
                // Generate a unique filename
                $uniqueFileName = uniqid() . "_" . basename($_FILES['uploadedFile']['name']);
                $uploadedFile = $uploadDirectory . $uniqueFileName;
                // Get the file type from the uploaded file
                $fileType = $_FILES['uploadedFile']['type'];
                // Move the uploaded file to the destination directory
                move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $uploadedFile);
            }


            // Update the author data including social media links
            $sql_update_author = "UPDATE authors SET authorfullname = ?, book_title = ?, email = ?, year_of_birth = ?, phone = ?, second_phone = ?, authorAddress = ?, fbLink = ?, instaLink = ?, youtubeLink = ?, tiktokLink = ?, userfile = ?, filetype = ?, notes = ?, author_status = ?, book_type_id = ?, book_level_id = ?, subject_id = ? WHERE id = ?";
            $stmt_update_author = mysqli_prepare($conn, $sql_update_author);
            mysqli_stmt_bind_param($stmt_update_author, "sssssssssssssssiiii", $uname, $book_title, $email, $year_of_birth, $phone, $second_phone, $authorAddress, $fbLink, $instaLink, $youtubeLink, $tiktokLink, $uploadedFile, $fileType, $notes, $author_status, $book_type_id, $book_level_id, $subject_id, $id);
            mysqli_stmt_execute($stmt_update_author);

    
            
            if (mysqli_stmt_execute($stmt_update_author)) {
                mysqli_stmt_close($stmt_update_author);
                $author_type = isset($_POST['author_type_for_update']) ? $_POST['author_type_for_update'] : '';

                // Update specific data based on author type
                if ($author_type === 'student') {
                    $studentLevel = trim($_POST["studentLevel"]);
                    $studentSpecialty = trim($_POST["studentSpecialty"]);
                    $baccalaureateRate = trim($_POST["baccalaureateRate"]);
                    $baccalaureateYear = trim($_POST["baccalaureateYear"]);
            
                    // Prepare and execute SQL query to update student-specific data
                    $sql_update_student_data = "UPDATE student_data SET studentLevel = ?, studentSpecialty = ?, baccalaureateRate = ?, baccalaureateYear = ? WHERE author_id = ?";
                    $stmt_update_student_data = mysqli_prepare($conn, $sql_update_student_data);
                    mysqli_stmt_bind_param($stmt_update_student_data, "ssssi", $studentLevel, $studentSpecialty, $baccalaureateRate, $baccalaureateYear, $id);
                    mysqli_stmt_execute($stmt_update_student_data);
                } elseif ($author_type === 'teacher') {
                    $teacherExperience = trim($_POST["teacherExperience"]);
                    $teacherCertificate = trim($_POST["teacherCertificate"]);
                    $teacherRank = trim($_POST["teacherRank"]);
                    $workFoundation = trim($_POST["workFoundation"]);
            
                    // Prepare and execute SQL query to update teacher-specific data
                    $sql_update_teacher_data = "UPDATE teacher_data SET teacherExperience = ?, teacherCertificate = ?, teacherRank = ?, workFoundation = ? WHERE author_id = ?";
                    $stmt_update_teacher_data = mysqli_prepare($conn, $sql_update_teacher_data);
                    mysqli_stmt_bind_param($stmt_update_teacher_data, "ssssi", $teacherExperience, $teacherCertificate, $teacherRank, $workFoundation, $id);
                    mysqli_stmt_execute($stmt_update_teacher_data);
                }
                elseif ($author_type === 'inspector') {
                    $inspectorExperience = trim($_POST["inspectorExperience"]);
                    $inspectorCertificate = trim($_POST["inspectorCertificate"]);
                    $inspectorRank = trim($_POST["inspectorRank"]);
                    $inspectorWorkFoundation = trim($_POST["inspectorWorkFoundation"]);
                
                    // Prepare and execute SQL query to update inspector-specific data
                    $sql_update_inspector_data = "UPDATE inspector_data SET inspectorExperience = ?, inspectorCertificate = ?, inspectorRank = ?, inspectorWorkFoundation = ? WHERE author_id = ?";
                    $stmt_update_inspector_data = mysqli_prepare($conn, $sql_update_inspector_data);
                    mysqli_stmt_bind_param($stmt_update_inspector_data, "ssssi", $inspectorExperience, $inspectorCertificate, $inspectorRank, $inspectorWorkFoundation, $id);
                    mysqli_stmt_execute($stmt_update_inspector_data);
                }
                elseif ($author_type === 'doctor') {
                    $specialty = trim($_POST["specialty"]);
                    $drWorkFoundation = trim($_POST["drWorkFoundation"]);
                
                    // Prepare and execute SQL query to update doctor-specific data
                    $sql_update_doctor_data = "UPDATE doctor_data SET specialty = ?, drWorkFoundation = ? WHERE author_id = ?";
                    $stmt_update_doctor_data = mysqli_prepare($conn, $sql_update_doctor_data);
                    mysqli_stmt_bind_param($stmt_update_doctor_data, "ssi", $specialty, $drWorkFoundation, $id);
                    mysqli_stmt_execute($stmt_update_doctor_data);
                }
                elseif ($author_type === 'trainer') {
                    $field = trim($_POST["field"]);
                    $trainerExperience = trim($_POST["trainerExperience"]);
                
                    // Prepare and execute SQL query to update trainer-specific data
                    $sql_update_trainer_data = "UPDATE trainer_data SET field = ?, trainerExperience = ? WHERE author_id = ?";
                    $stmt_update_trainer_data = mysqli_prepare($conn, $sql_update_trainer_data);
                    mysqli_stmt_bind_param($stmt_update_trainer_data, "ssi", $field, $trainerExperience, $id);
                    mysqli_stmt_execute($stmt_update_trainer_data);
                }
                elseif ($author_type === 'novelist') {
                    $novelistfield = trim($_POST["novelistfield"]);
                
                    // Prepare and execute SQL query to update novelist-specific data
                    $sql_update_novelist_data = "UPDATE novelist_data SET novelistfield = ? WHERE author_id = ?";
                    $stmt_update_novelist_data = mysqli_prepare($conn, $sql_update_novelist_data);
                    mysqli_stmt_bind_param($stmt_update_novelist_data, "si", $novelistfield, $id);
                    mysqli_stmt_execute($stmt_update_novelist_data);
                }
        

        }
                 // Redirect to the display_authors page after successful update
                 $_SESSION['create_update_success'] = true;
                 header("Location: display_authors.php");
                 exit;

        }

        }
            }
    ?>
             <form role="form" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="post">
              <h4 class="mb-3">تحديث مؤلف</h4>
              <div class="border rounded p-4 shadow">
                 <h6 class="border-bottom pb-2 mb-3">تحديث معلومات المؤلف</h6>
                <div class="row">
                    <div class="form-group col-md-6">
                    <input type="hidden" name="author_type_for_update" value="<?php echo htmlspecialchars($author_type); ?>">

                        <label class="form-label">إسم المؤلف :</label>
                        <input type="text" name="authorfullname" class="form-control border pe-2 mb-3 <?php echo (!empty($uname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($uname); ?>" required>
                        <span class="invalid-feedback"><?php echo $uname_err; ?></span>
                    </div>
                    <div class="form-group col-md-6">
                    <label class="form-label">الهاتف :</label>
                    <input type="text" name="phone" class="form-control border pe-2 mb-3 <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($phone); ?>" required>
                    <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                </div>
                </div>

                <div class="row">
                <div class="form-group col-md-6">
                    <label class="form-label">الإيميل :</label>
                    <input type="email" name="email" class="form-control border pe-2 mb-3 <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">سنة الميلاد :</label>
                    <input type="text" name="year_of_birth" class="form-control border pe-2 mb-3 <?php echo (!empty($year_of_birth_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($year_of_birth); ?>" required>
                    <span class="invalid-feedback"><?php echo $year_of_birth_err; ?></span>
                </div>
                </div>

                <div class="row">
                <div class="form-group col-md-6">
                    <label class="form-label">عنوان المؤلف :</label>
                    <input type="text" name="authorAddress" class="form-control border pe-2 mb-3 <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($authorAddress); ?>" required>
                    <span class="invalid-feedback"><?php echo $address_err; ?></span>
                </div>
                <div class="form-group col-md-6">
                <label class="form-label">نوع المؤلف :</label>
                    <input type="text" name="author_type" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($author_type); ?>" disabled>
                </div>
                </div>
           
                 <!-- Specific Inputs for Student -->
                <?php if ($author_type === 'student') {?>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">مستوى الطالب :</label>
                        <input type="text" name="studentLevel" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($studentLevel); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">التخصص :</label>
                        <input type="text" name="studentSpecialty" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($studentSpecialty); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">معدل البكالوريا :</label>
                        <input type="text" name="baccalaureateRate" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($baccalaureateRate); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">سنة البكالوريا :</label>
                        <input type="text" name="baccalaureateYear" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($baccalaureateYear); ?>" required>
                    </div>
                </div>
                  <!-- Specific Inputs for Techer -->
                <?php } elseif ($author_type === 'teacher') {?>
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">الخبرة :</label>
                        <input type="text" name="teacherExperience" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($teacherExperience); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">الشهادة :</label>
                        <input type="text" name="teacherCertificate" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($teacherCertificate); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">الرتبة :</label>
                        <input type="text" name="teacherRank" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($teacherRank); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">مؤسسة العمل :</label>
                        <input type="text" name="workFoundation" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($workFoundation); ?>" required>
                    </div>
                </div>
                  <!-- Specific Inputs for Inspector -->
                <?php  } elseif ($author_type === 'inspector') {?>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">خبرة المفتش :</label>
                        <input type="text" name="inspectorExperience" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($inspectorExperience); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">الشهادة :</label>
                        <input type="text" name="inspectorCertificate" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($inspectorCertificate); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">الرتبة :</label>
                        <input type="text" name="inspectorRank" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($inspectorRank); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">الولاية :</label>
                        <input type="text" name="inspectorWorkFoundation" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($inspectorWorkFoundation); ?>" required>
                    </div>
                </div>
                <!-- Specific Inputs for Doctor -->  
                <?php } elseif ($author_type === 'doctor') {?>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">التخصص:</label>
                        <input type="text" name="specialty" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($specialty); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">مكان العمل:</label>
                        <input type="text" name="drWorkFoundation" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($drWorkFoundation); ?>" required>
                    </div>
                </div>
                <!-- Specific Inputs for trainer -->
                <?php } elseif ($author_type === 'trainer') {?>
                    <div class="row">
                <div class="form-group col-md-6">
                    <label class="form-label">المجال:</label>
                    <input type="text" name="field" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($field); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">الخبرة:</label>
                    <input type="text" name="trainerExperience" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($trainerExperience); ?>" required>
                </div>
            </div>
                <?php } elseif ($author_type === 'novelist') {?>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label">المجال :</label>
                            <input type="text" name="novelistfield" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($novelistfield); ?>" required>
                        </div>
                    </div>
                <?php } ?>

                <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">رقم الهاتف الثاني :</label>
                            <input type="text" name="second_phone" class="form-control border pe-2 mb-3 <?php echo (!empty($second_phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($second_phone); ?>">
                            <span class="invalid-feedback"><?php echo $second_phone_err; ?></span>
                        </div>
                        <div class="col-md-6 mt-4">
                        <div class="input-group input-group-outline mt-2">
                        <select name="author_status" id="author_status" class="form-control" required>
                            <option value="" disabled> -- اختر حالة الكاتب -- </option>
                            <?php
                            // Define an array of status values
                            $statusValues = array("في الانتظار", "قيد الدراسة", "مؤجل", "مقبول", "مرفوض");
                            
                            // Fetch the currently selected status from the database (assuming $author_status contains the status value)
                            $selectedStatus = htmlspecialchars($author_status);
                            
                            // Loop through status values and create options
                            foreach ($statusValues as $author_status) {
                                $selected = ($author_status === $selectedStatus) ? 'selected' : ''; // Check if this option is selected
                                echo '<option value="' . $author_status . '" ' . $selected . '>' . $author_status . '</option>';
                            }
                            ?>
                        </select>
                      </div>
                      </div>
                    </div>
                    
                </div>
                <!-- Updete Book Section-->
                <div class="border rounded p-4 shadow mt-4">
                 <h6 class="border-bottom pb-2 mb-3">تحديث معلومات الكتاب</h6>

                <div class="d-flex">
                <div class="input-group input-group-outline my-3">
              <select name="book_type_id" id="book_type" class="form-control" required>
                <option value="" disabled> -- اختر نوع الكتاب -- </option>
                    <?php
                       // Fetch book types for dropdown
                    $bookTypesResult = mysqli_query($conn, "SELECT * FROM book_types");
                    while ($bookType = mysqli_fetch_assoc($bookTypesResult)) {
                        $type_id = htmlspecialchars($bookType["id"]);
                        $type_name = htmlspecialchars($bookType["type_name"]);
                        $selected = ($type_id == $book_type_id) ? 'selected' : ''; // Check if this option is selected
                        echo '<option value="' . $type_id . '" ' . $selected . '>' . $type_name . '</option>';
                    }
                    ?>
                </select>
                 </div>
                <div class="input-group input-group-outline my-3 me-3">
                    <select name="book_level_id" id="book_level" class="form-control" required>
                    <option value="" disabled selected>-- اختر مستوى الكتاب --</option>
                    <?php
                            // Fetch book levels for dropdown
                    $bookLevelsResult = mysqli_query($conn, "SELECT * FROM book_levels");
                    // Fetch book levels for dropdown
                    while ($bookLevel = mysqli_fetch_assoc($bookLevelsResult)) {
                        $level_id = htmlspecialchars($bookLevel["id"]);
                        $level_name = htmlspecialchars($bookLevel["level_name"]);
                        $selected = ($level_id == $book_level_id) ? 'selected' : '';
                        echo '<option value="' . $level_id . '" ' . $selected . '>' . $level_name . '</option>';
                    }
                    ?>
                 </select>
             </div>

            </div>

            <div class="d-flex">
                    <div class="input-group input-group-outline my-3" style="width: 50%;">
                    <select name="subject_id" id="subject_id" class="form-control" required>
                    <option value="" disabled selected>-- اختر المادة --</option>
                    <?php
                            // Fetch book levels for dropdown
                    $subjectsResult = mysqli_query($conn, "SELECT * FROM subjects");
                    // Fetch book levels for dropdown
                    while ($subject = mysqli_fetch_assoc($subjectsResult)) {
                        $subjectID = htmlspecialchars($subject["id"]);
                        $subject_name = htmlspecialchars($subject["subject_name"]);
                        $selected = ($subjectID == $subject_id) ? 'selected' : '';
                        echo '<option value="' . $subjectID . '" ' . $selected . '>' . $subject_name . '</option>';
                    }
                    ?>
                 </select>
             </div>
        <div class="input-group input-group-outline my-3 me-3" style="width: 50%;">
                <label for="book_title" class="form-label"></label>
                <input type="text" name="book_title" id="book_title" class="form-control border" placeholder="عنوان الكتاب" value="<?php echo htmlspecialchars($book_title); ?>" required>
            </div>
        </div>
        </div>
        <!-- Updete Social Section-->
        <div class="border rounded p-4 shadow">
            <h6 class="border-bottom pb-2 mb-3">تحديث معلومات وسائل التواصل</h6>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">رابط الفيسبوك :</label>
                        <input type="text" name="fbLink" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($fbLink); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">رابط الإنستغرام :</label>
                        <input type="text" name="instaLink" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($instaLink); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">رابط اليوتيوب :</label>
                        <input type="text" name="youtubeLink" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($youtubeLink); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">رابط التيكتوك :</label>
                        <input type="text" name="tiktokLink" class="form-control border pe-2 mb-3" value="<?php echo htmlspecialchars($tiktokLink); ?>">
                    </div>
                </div>

                </div>
                        <!-- Updete Notes Section-->
        <div class="border rounded p-4 shadow">
            <h6 class="border-bottom pb-2 mb-3">تحديث الملاحظات + السيرة الذاتية</h6>
                <div class="row">
                <div class="input-group input-group-outline col-md-6">    
                    <input type="file" class="form-control <?php echo (!empty($file_err)) ? 'is-invalid' : ''; ?>" id="file" name="uploadedFile" />
                      <span class="invalid-feedback"><?php echo $file_err; ?></span>
                    </div>
                <div class="form-group col-md-12 my-3">
                    <label for="notes" class="form-label">تحديث الملاحظات:</label>                   
              <textarea class="form-control border pe-2 mb-3" id="notes" name="notes" rows="4"><?php echo htmlspecialchars($notes); ?></textarea>
          </div>
                
                </div>
                </div>

    <div class="form-group mt-3">
                  <button type="submit" name="updateData" class="btn btn-primary">تحديث</button>
              </div>
          </form>
          
    <hr>
    <a href="display_authors.php" class="btn btn-secondary">العودة إلى قائمة المؤلفين</a>
</div>
<script>
   // Function to populate book levels dropdown based on selected book type
function populateBookLevels(bookTypeId, selectedLevelId) {
    var bookLevelSelect = document.getElementById('book_level');
    var subjectSelect = document.getElementById('subject_id');

    bookLevelSelect.innerHTML = '';
    subjectSelect.innerHTML = '';

    // Create default placeholder options
    var bookLevelPlaceholder = document.createElement('option');
    bookLevelPlaceholder.value = '';
    bookLevelPlaceholder.textContent = '-- اختر مستوى الكتاب --';
    bookLevelPlaceholder.disabled = true;
    bookLevelPlaceholder.selected = true;
    bookLevelSelect.appendChild(bookLevelPlaceholder);

    var subjectPlaceholder = document.createElement('option');
    subjectPlaceholder.value = '';
    subjectPlaceholder.textContent = '-- اختر المادة --';
    subjectPlaceholder.disabled = true;
    subjectPlaceholder.selected = true;
    subjectSelect.appendChild(subjectPlaceholder);

    // Fetch and populate book levels based on selected book type
    if (bookTypeId !== '') {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_book_levels_for_subject.php?book_type_id=' + bookTypeId, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                // Parse the response and create option elements
                var bookLevels = JSON.parse(xhr.responseText);
                bookLevels.forEach(function (bookLevel) {
                    var option = document.createElement('option');
                    option.value = bookLevel.id;
                    option.textContent = bookLevel.level_name;
                    if (bookLevel.id == selectedLevelId) {
                        option.selected = true; // Select the appropriate level
                    }
                    bookLevelSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    }
}

    // Add an event listener to the book_type dropdown
    document.getElementById('book_type').addEventListener('change', function () {
        var bookTypeId = this.value;
        populateBookLevels(bookTypeId, <?php echo json_encode($book_level_id); ?>);
    });

    // On page load, populate book levels based on the initial selected book type
    populateBookLevels(<?php echo json_encode($book_type_id); ?>, <?php echo json_encode($book_level_id); ?>);

    // Function to populate subjects dropdown based on selected book level
    function populateSubjects(bookLevelId, selectedSubjectId) {
        var subjectSelect = document.getElementById('subject_id');
        subjectSelect.innerHTML = '';

        // Create a default placeholder option
        var placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = '-- اختر المادة --';
        placeholderOption.disabled = true;
        placeholderOption.selected = true;
        subjectSelect.appendChild(placeholderOption);

        // Fetch and populate subjects based on selected book level
        if (bookLevelId !== '') {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_subjects_for_level.php?book_level_id=' + bookLevelId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Parse the response and create option elements
                    var subjects = JSON.parse(xhr.responseText);
                    subjects.forEach(function (subject) {
                        var option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.subject_name;
                        if (subject.id == selectedSubjectId) {
                            option.selected = true; // Select the appropriate subject
                        }
                        subjectSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    }

    // Add an event listener to the book_level dropdown
    document.getElementById('book_level').addEventListener('change', function () {
        var bookLevelId = this.value;
        populateSubjects(bookLevelId, <?php echo json_encode($subject_id); ?>);
    });

    // On page load, populate subjects based on the initial selected book level
    populateSubjects(<?php echo json_encode($book_level_id); ?>, <?php echo json_encode($subject_id); ?>);
</script>
<?php
// Close the database connection
mysqli_close($conn);
include('footer.php');
ob_end_flush();
?>
