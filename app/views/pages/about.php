<?php require APPROOT . '/views/inc/header.php'; ?>
<h1>About</h1>
<p><?php echo $data['title']; ?></p>
<?php foreach ($data['features'] as $feature) : ?>
  <ul>
    <li><?php echo $feature ?></li>
  </ul>
<?php endforeach; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>