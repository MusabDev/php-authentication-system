<?php
include __DIR__ . '/components/header.php';

// Check if user is already logged in
if (isAuthenticated()) {
  header("Location: index.php");
}

// Fetching token from url
if (isset($_GET['token'])) {
  $token = mysqli_real_escape_string($conn, $_GET['token']);

  // Check user token
  $sql = "SELECT user_id FROM `tokens` WHERE token = '$token'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $user_id = $user_data['user_id'];
  } else {
    // Token is invalid
    header("Location: forgot-password.php");
  }
} else {
  header("Location: forgot-password.php");
}

// Check if reset password is clicked
if (isset($_POST['new-password']) && isset($_POST['confirm-new-password'])) {
  $new_password = mysqli_real_escape_string($conn, $_POST['new-password']);
  $confirm_new_password = mysqli_real_escape_string($conn, $_POST['confirm-new-password']);

  // Check if the password is matches
  if ($new_password === $confirm_new_password) {
    $hash_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $sql = "UPDATE `users` SET `password` = '$hash_password' WHERE `id` = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
      header("Location: login.php?success=Password successfully reseted.");
    } else {
      $alert['error'] = 'Error: ' . mysqli_error($conn);
    }
  } else {
    $alert['error'] = 'Password doesn\'t match.';
  }
}

?>

<div class="wrapper authentication-form">
  <form action="" method="POST">
    <h1>Reset password</h1>

    <?php include __DIR__ . '/components/alert.php'; ?>

    <div class="input-box">
      <label for="new-password" class="input-label">New Password</label>
      <input type="password" class="input" id="new-password" name="new-password">
    </div>
    <div class="input-box">
      <label for="confirm-new-password" class="input-label">Confirm New Password</label>
      <input type="password" class="input" id="confirm-new-password" name="confirm-new-password">
    </div>
    <button type="submit" class="btn input-box">Reset Password</button>
    <p style="text-align: center; margin-top: 1rem">
      Remember password?
      <a href="login.php">Login</a>
    </p>
  </form>
</div>

<?php include __DIR__ . '/components/footer.php'; ?>