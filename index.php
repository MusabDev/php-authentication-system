<?php
include __DIR__ . '/components/header.php';

// Check if user is already logged in
if (!isAuthenticated()) {
  header("Location: login.php");
}

// Logout
if (isset($_POST['logout'])) {
  // Remove token from cookie
  unset($_COOKIE['token']);
  setcookie('token', '', '-1', '/');
  header("Location: login.php");
}

// Getting user data
$user_data = getLoggedInUser();

?>

Welcome <?= $user_data['first_name'] . ' ' . $user_data['last_name'] ?>


<div class="wrapper">
  <form action="" method="POST">
    <button type="submit" name="logout" class="btn" style="background-color: red">Logout</button>
  </form>
</div>


<?php include __DIR__ . '/components/footer.php'; ?>