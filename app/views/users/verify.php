<?php require APPROOT . '/views/inc/header.php'; ?>
<h1>Answer the Security Questions</h1>
<?php flash("verify_fail"); ?>
<form action="<?php echo URLROOT; ?>/users/verify" method="post">
    <?php $count = 0; ?>
    <?php foreach ($data["qIds"] as $questionId) : ?>
        <?php $count += 1; ?>
        <?php foreach ($data["questions"] as $question) : ?>
            <?php if ($question->id == $questionId) : ?>
                <div class="form-group">
                    <label><?php echo $question->question ?><sup>*</sup></label>
                    <input type="text" name="<?php echo 'sec' . $count ?>" class="form-control form-control-lg " value="<?php echo $data['sec' . $count]; ?>">
                </div>
                <?php break; ?>
            <?php endif ?>
        <?php endforeach ?>

    <?php endforeach ?>
    <input type="submit" class="btn btn-success" value="Submit">

</form>
<?php require APPROOT . '/views/inc/footer.php'; ?>