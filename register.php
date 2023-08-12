
<?php
// Initialize variables
$uname = $email = $pwd = $confirm_pwd = "";
$uname_err = $email_err = $pwd_err = $confirm_pwd_err = $register_err = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the form data
  $uname = trim($_POST["txt_uname"]);
  $email = trim($_POST["txt_email"]);
  $pwd = trim($_POST["txt_pwd"]);
  $confirm_pwd = trim($_POST["txt_confirm_pwd"]);

  // Validate username
  if (empty($uname)) {
    $uname_err = "Please enter a username.";
  }elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $uname)) {
    $uname_err = "Username can only contain letters, numbers, and underscores.";
}

  // Validate email
  if (empty($email)) {
    $email_err = "Please enter an email address.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format.";
  }

  // Validate password
  if (empty($pwd)) {
    $pwd_err = "Please enter a password.";
  } elseif (strlen($pwd) < 6) {
    $pwd_err = "Password must be at least 6 characters.";
  }

  // Validate confirm password
  if (empty($confirm_pwd)) {
    $confirm_pwd_err = "Please confirm the password.";
  } elseif ($pwd !== $confirm_pwd) {
    $confirm_pwd_err = "Passwords do not match.";
  }

// If there are no errors, proceed with registration
if (empty($uname_err) && empty($email_err) && empty($pwd_err)&& empty($confirm_pwd_err)) {
    // Create a database connection
    include('connect.php');

    // Check if the email already exists in the database
    $sql_check_email = "SELECT id FROM users WHERE email = ?";
    $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);

    // Check if the username already exists in the database
    $sql_check_username = "SELECT id FROM users WHERE username = ?";
    $stmt_check_username = mysqli_prepare($conn, $sql_check_username);
    mysqli_stmt_bind_param($stmt_check_username, "s", $uname);
    mysqli_stmt_execute($stmt_check_username);
    mysqli_stmt_store_result($stmt_check_username);

    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        // Email already exists, show error message
        $email_err = "This email is already taken. Please use a different email.";
    } elseif (mysqli_stmt_num_rows($stmt_check_username) > 0) {
        // Username already exists, show error message
        $uname_err = "This username is already taken. Please choose a different username.";
    } else {
        // Insert the new user record into the database
        $sql_insert_user = "INSERT INTO users (username, email, pass) VALUES (?, ?, ?)";
        $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
        // Hash the password before storing it in the database
        $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt_insert_user, "sss", $uname, $email, $hashed_pwd);
        mysqli_stmt_execute($stmt_insert_user);
        // Registration successful, show success message
        $register_success_msg = "Registration successful. You can now log in.";

        // Store the success message in a session variable
        session_start();
        $_SESSION['register_success_msg'] = $register_success_msg;
        // Registration successful, redirect to login page or dashboard
        header("Location: login.php");
        exit();
    }

    // Close the connection
    mysqli_close($conn);
}
}
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
              <div class="mb-4">
                <label for="username" class="form-label">Username</label>
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
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control <?php echo (!empty($pwd_err)) ? 'is-invalid' : ''; ?>"
                  id="password" name="txt_pwd" />
                <span class="invalid-feedback"><?php echo $pwd_err; ?></span>
              </div>
              <div class="mb-4">
                <label for="confirm_pwd" class="form-label">Confirm Password</label>
                <input type="password" class="form-control <?php echo (!empty($confirm_pwd_err)) ? 'is-invalid' : ''; ?>" id="confirm_pwd"
                  name="txt_confirm_pwd" />
                <span class="invalid-feedback"><?php echo $confirm_pwd_err; ?></span>
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
     <!-- Sign In Link -->
        <div class="row justify-content-center mt-3">
            <div class="col-lg-4 col-md-6 col-sm-6 text-center">
                <p class="mb-0">Already a member? <a href="login.php" class="text-decoration-none">Sign In</a></p>
            </div>
        </div>
  </div>

</body>

</html>