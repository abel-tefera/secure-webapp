<?php require APPROOT . '/views/inc/header.php'; ?>
<h1>Select new security questions and answers. </h1>
<h5>These questions will be used to verify your identity and recover your account in case of multiple failed login attempts.</h5>
<?php console_log($data);?>
<form action="<?php echo URLROOT; ?>/users/settings" method="post">
    <div class="form-group">
        <label>Security Question 1:<sup>*</sup></label>
        <select name="sec1-select" id="sec1-select">
            <?php foreach ($data['questions'] as $question) : ?>
                <option value="<?php echo $question->id ?>" <?php if($question->id == $data['sec1-select']): echo 'selected' ?><?php endif;?>><?php echo $question->question; ?></option>
            <?php endforeach ?>
        </select>
        <input type="text" name="sec1" class="form-control form-control-lg <?php echo (!empty($data['sec1_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['sec1']; ?>">
        <span class="invalid-feedback"><?php echo $data['sec1_err']; ?></span>
    </div>
    <div class="form-group">
        <label>Security Question 2:<sup>*</sup></label>
        <select name="sec2-select" id="sec2-select">
            <?php foreach ($data['questions'] as $question) : ?>
                <option value="<?php echo $question->id ?>" <?php if($question->id == $data['sec2-select']): echo 'selected' ?><?php endif;?>><?php echo $question->question; ?></option>
            <?php endforeach ?>
        </select>
        <input type="text" name="sec2" class="form-control form-control-lg <?php echo (!empty($data['sec2_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['sec2']; ?>">
        <span class="invalid-feedback"><?php echo $data['sec2_err']; ?></span>
    </div>
    <div class="form-group">
        <label>Security Question 3:<sup>*</sup></label>
        <select name="sec3-select" id="sec3-select">
            <?php foreach ($data['questions'] as $question) : ?>
                <option value="<?php echo $question->id ?>" <?php if($question->id == $data['sec3-select']): echo 'selected' ?><?php endif;?>><?php echo $question->question; ?></option>
            <?php endforeach ?>
        </select>
        <input type="text" name="sec3" class="form-control form-control-lg <?php echo (!empty($data['sec3_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['sec3']; ?>">
        <span class="invalid-feedback"><?php echo $data['sec3_err']; ?></span>
    </div>
    <input type="submit" class="btn btn-success" value="Submit">

</form>
<?php require APPROOT . '/views/inc/footer.php'; ?>