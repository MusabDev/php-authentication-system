<?php
include __DIR__ . '/components/header.php';

// Check if user is already logged in
if (isAuthenticated()) {
  header("Location: index.php");
}

// Check if reset password is clicked
if (isset($_POST['email'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);

  // Check if user exists
  // SQL query to check if email is already exists or not
  $sql = "SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `email` = '$email'";
  $result = mysqli_query($conn, $sql);
  // Check if email is already exists
  if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    // Generate verfication link
    $token = md5(rand() . time());
    $verification_link = BASE_URL . '/reset-password.php?token=' . $token;
    mysqli_query($conn, "INSERT INTO `tokens` (`user_id`, `token`) VALUES ('{$user_data['id']}', '$token')");
    // Sending confirmation email
    $subject = "Reset your password";
    $body = "
      <h4>Hi $user_data[first_name] $user_data[last_name],</h4>
      <p>Forgot your password? Don't worry, you can reset your password by clicking the below link.</p>
      <a href='$verification_link'>$verification_link</a>
    ";
    sendEmail($email, $subject, $body);
    $alert['success'] = 'We\'ve sent instructions to your email - ' . $email;
    // Empty all post fields
    $_POST['email'] = '';
  } else {
    $alert['error'] = 'User is not exits. Please register your account.';
  }
}

?>

<div class="wrapper authentication-form">
  <form action="" method="POST">
    <h1>Forgot password</h1>

    <?php include __DIR__ . '/components/alert.php'; ?>

    <div class="input-box">
      <label for="email" class="input-label">Email Address</label>
      <input type="email" class="input" id="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
    </div>
    <button type="submit" class="btn input-box">Reset Password</button>
    <p style="text-align: center; margin-top: 1rem">
      Remember password?
      <a href="login.php">Login</a>
    </p>
  </form>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>