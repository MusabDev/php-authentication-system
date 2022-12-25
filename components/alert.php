<?php
if (isset($_GET['success'])) {
  $alert['success'] = $_GET['success'];
}
if (isset($_GET['error'])) {
  $alert['error'] = $_GET['error'];
}
?>

<?php if (isset($alert['success'])) : ?>
  <div class="alert success">
    <?= $alert['success'] ?>
  </div>
<?php endif; ?>
<?php if (isset($alert['error'])) : ?>
  <div class="alert error">
    <?= $alert['error'] ?>
  </div>
<?php endif; ?>