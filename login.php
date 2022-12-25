<?php
include __DIR__ . '/components/header.php';

// Check if user is already logged in
if (isAuthenticated()) {
  header("Location: index.php");
}

// Verify email
if (isset($_GET['verify'])) {
  $token = mysqli_real_escape_string($conn, $_GET['verify']);

  // Check user token
  $sql = "SELECT user_id FROM `tokens` WHERE token = '$token'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $user_id = $user_data['user_id'];
    // Token is valid
    // Verify user from database
    $sql = "UPDATE `users` SET verified = '1' WHERE `id` = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
      $alert['success'] = 'User is verified successfully. Please login to your account.';
    } else {
      $alert['danger'] = 'Error: ' . mysqli_error($conn);
    }
  } else {
    // Token is invalid
    $alert['error'] = 'Token is invalid.';
  }
}

// Login user
if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  // Removing other alerts
  unset($_GET['error']);
  unset($_GET['success']);


  // Check if all fields are filled
  if (empty($email) || empty($password)) {
    $alert['error'] = 'All fields must not be empty.';
  } else {
    // SQL query to check if email is exists or not
    $sql = "SELECT `id`, `password` FROM `users` WHERE `email` = '$email' AND `verified` = '1'";
    $result = mysqli_query($conn, $sql);
    // Check if email is already exists
    if (mysqli_num_rows($result) > 0) {
      $user_data = mysqli_fetch_assoc($result);
      // Check password
      $verify_password = password_verify($password, $user_data['password']);
      if ($verify_password) {
        $login_time = time() + (60 * 60 * 24); // 24 hours
        if (isset($_POST['remember-me'])) {
          $login_time = time() + (60 * 60 * 24 * 365); // 365 days (year)
        }
        // Set cookie
        setcookie('token', $user_data['password'], $login_time, '/');
        // Redirect to welcome page
        header('Location: index.php');
      } else {
        $alert['error'] = 'Login credentials are invalid.';
      }
    } else {
      $alert['error'] = 'Login credentials are invalid.';
    }
  }
}

?>

<div class="wrapper authentication-form">
  <form action="" method="POST">
    <h1>Welcome back</h1>

    <?php include __DIR__ . '/components/alert.php'; ?>

    <div class="input-box">
      <label for="email" class="input-label">Email Address</label>
      <input type="email" class="input" id="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
    </div>
    <div class="input-box">
      <label for="password" class="input-label">Password</label>
      <input type="password" class="input" id="password" name="password">
      <a href="forgot-password.php" style="font-size: 14px; color: black;">Forgot Password?</a>
    </div>
    <div class="remember-me input-box">
      <input type="checkbox" id="remember-me" name="remember-me">
      <label for="remember-me">Remember Me</label>
    </div>
    <button type="submit" class="btn input-box">Login</button>
    <p style="text-align: center; margin-top: 1rem">
      Don't have an account yet?
      <a href="register.php">Register</a>
    </p>
  </form>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>