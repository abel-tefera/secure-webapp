<?php require APPROOT . '/views/inc/header.php'; ?>
<a href="<?php echo URLROOT; ?>" class="btn btn-light"><i class="fa fa-backward" aria-hidden="true"></i> Back</a>
<div class="card card-body bg-light mt-5">
  <h2>Add Complaint</h2>
  <p>Create a complaint with this form</p>
  <form id="addComplaintForm" action="<?php echo URLROOT; ?>/complaints/add" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>Title:<sup>*</sup></label>
      <input id="title" type="text" name="title" class="form-control form-control-lg <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>" placeholder="Add a title...">
      <span class="invalid-feedback"><?php echo $data['title_err']; ?></span>
    </div>
    <div class="form-group">
      <label>Body:<sup>*</sup></label>
      <textarea id="complaintBody" name="body" class="form-control form-control-lg <?php echo (!empty($data['body_err'])) ? 'is-invalid' : ''; ?>" placeholder="Add some text..." value="<?php echo $data['body']; ?>"><?php echo $data['body']; ?></textarea>
      <span class="invalid-feedback"><?php echo $data['body_err']; ?></span>
    </div>
    <div class="form-group">
      <label for="imageUpload">File Upload:</label>
      <input type="file" name="fileToUpload" id="fileToUpload">
      <span class="invalid-feedback" style="display: block;"><?php echo $data['upload_err']; ?></span>
    </div>
    <input type="submit" class="btn btn-success" value="Submit">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']?>"/>

  </form>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>