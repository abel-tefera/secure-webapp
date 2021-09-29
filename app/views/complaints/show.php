<?php require APPROOT . '/views/inc/header.php'; ?>
  <a href="<?php echo URLROOT; ?>" class="btn btn-light mb-3"><i class="fa fa-backward" aria-hidden="true"></i> Back</a>
  <br>
  <h1><?php echo $data['complaint']->title; ?></h1>

  <div class="bg-secondary text-white p-2 mb-3">
    Written by <?php echo $data['user']->name; ?> on <?php echo $data['complaint']->created_at; ?>
  </div>
  <p><?php echo $data['complaint']->body; ?></p>
  <?php if ($data['complaint']->file_name !== null) : ?>
      <a href="<?php echo URLROOT; ?>/complaints/download/<?php echo $data['complaint']->file_name ?>" target="_blank">
        Open File
      </a>
    <?php endif; ?>
  <?php if($data['complaint']->user_id == $_SESSION['user_id']) : ?>
    <hr>
    <a class="btn btn-dark" href="<?php echo URLROOT; ?>/complaints/edit/<?php echo $data['complaint']->id; ?>">Edit</a>

    <form class="pull-right" action="<?php echo URLROOT; ?>/complaints/delete/<?php echo $data['complaint']->id; ?>" method="post">
      <input type="submit" class="btn btn-danger" value="Delete">
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']?>"/>
    </form>
  <?php endif; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>