<?php

include __DIR__ . '/components/header.php';

// Check if user is already logged in
if (isAuthenticated()) {
  header("Location: index.php");
}

// Check if register button is clicked
if (isset($_POST['first-name']) && isset($_POST['last-name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm-password'])) {
  $first_name = mysqli_real_escape_string($conn, $_POST['first-name']);
  $last_name = mysqli_real_escape_string($conn, $_POST['last-name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);

  // Checking if any fields are black
  if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
    $alert['error'] = 'All fields must not be empty.';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $alert['error'] = 'Email format is invalid.';
  } else if ($password !== $confirm_password) {
    $alert['error'] = 'Password doesn\'t match.';
  } else {
    // SQL query to check if email is already exists or not
    $sql = "SELECT `id` FROM `users` WHERE `email` = '$email'";
    $result = mysqli_query($conn, $sql);
    // Check if email is already exists
    if (mysqli_num_rows($result) > 0) {
      $alert['error'] = 'Email is already exists. Please reset your password or try with a different email.';
    } else {
      // Encrypt password
      $hash_password = password_hash($password, PASSWORD_DEFAULT);
      // Register user
      $sql = "INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`) VALUES ('$first_name', '$last_name', '$email', '$hash_password')";
      $result = mysqli_query($conn, $sql);
      // Check if user is registered successfully
      if ($result) {
        // Fetching user information
        $sql = "SELECT `id` FROM `users` WHERE `email` = '$email'";
        $result = mysqli_query($conn, $sql);
        $user_data = mysqli_fetch_assoc($result);

        // Generate verfication link
        $token = md5(rand() . time());
        $verification_link = BASE_URL . '/login.php?verify=' . $token;
        mysqli_query($conn, "INSERT INTO `tokens` (`user_id`, `token`) VALUES ('{$user_data['id']}', '$token')");
        // Sending confirmation email
        $subject = "Email verification";
        $body = "
          <h4>Hi $first_name $last_name,</h4>
          <p>Your account has been created successfully. Please verify your email by clicking below link.</p>
          <a href='$verification_link'>$verification_link</a>
        ";
        sendEmail($email, $subject, $body);
        $alert['success'] = 'We\'ve sent a verification link to your email - ' . $email;
        // Empty all post fields
        $_POST['first-name'] = '';
        $_POST['last-name'] = '';
        $_POST['email'] = '';
      } else {
        $alert['error'] = 'Error: ' . mysqli_error($conn);
      }
    }
  }
}

?>

<div class="wrapper authentication-form">
  <form action="" method="POST">
    <h1>Join us</h1>

    <?php include __DIR__ . '/components/alert.php'; ?>

    <div class="input-box">
      <label for="first-name" class="input-label">First Name</label>
      <input type="text" class="input" id="first-name" name="first-name" value="<?= isset($_POST['first-name']) ? $_POST['first-name'] : '' ?>">
    </div>
    <div class="input-box">
      <label for="last-name" class="input-label">Last Name</label>
      <input type="text" class="input" id="last-name" name="last-name" value="<?= isset($_POST['last-name']) ? $_POST['last-name'] : '' ?>">
    </div>
    <div class="input-box">
      <label for="email" class="input-label">Email Address</label>
      <input type="email" class="input" id="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>">
    </div>
    <div class="input-box">
      <label for="password" class="input-label">Password</label>
      <input type="password" class="input" id="password" name="password">
    </div>
    <div class="input-box">
      <label for="confirm-password" class="input-label">Confirm Password</label>
      <input type="password" class="input" id="confirm-password" name="confirm-password">
    </div>
    <button type="submit" class="btn input-box">Register</button>
    <p style="text-align: center; margin-top: 1rem">
      Already have an account?
      <a href="login.php">Login</a>
    </p>
  </form>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>