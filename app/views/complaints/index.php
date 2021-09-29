<?php require APPROOT . '/views/inc/header.php'; ?>
<?php flash('complaint_message'); ?>
<div class="row mb-3">
  <div class="col-md-6">
    <h1>Complaints</h1>
  </div>
  <div class="col-md-6">
    <?php if ($_SESSION['user_role'] != 1) : ?>
      <a class="btn btn-primary pull-right" href="<?php echo URLROOT; ?>/complaints/add"><i class="fa fa-pencil" aria-hidden="true"></i> Add Complaint</a>
    <?php endif ?>
  </div>
</div>
<?php foreach ($data['complaints'] as $complaint) : ?>
  <div class="card card-body mb-3">
    <h4 class="card-title"><?php echo $complaint->title; ?></h4>
    <div class="bg-light p-2 mb-3">
      Written by <?php echo $complaint->name; ?> on <?php echo $complaint->created_at; ?>
    </div>
    <p class="card-text"><?php echo $complaint->body; ?></p>
    <?php if ($complaint->file_name !== null) : ?>
      <a href="<?php echo URLROOT; ?>/complaints/download/<?php echo $complaint->file_name ?>"  target="_blank">
        Open File
      </a>
    <?php endif; ?>
    <?php if ($complaint->userId == $_SESSION['user_id']) : ?>
      <a class="btn btn-dark" href="<?php echo URLROOT; ?>/complaints/show/<?php echo $complaint->complaintId; ?>">More</a>
    <?php endif ?>
  </div>
<?php endforeach; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>