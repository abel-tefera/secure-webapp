<?php require APPROOT . '/views/inc/header.php'; ?>
<h1>Accounts Management</h1>
<?php flash('accounts_msg'); ?>
<table class="table">
    <thead>
        <tr>
            <th scope="col">
                Name
            </th>
            <th scope="col">
                Email
            </th>
            <!-- <th scope="col">
                Status
            </th> -->
            <th scope="col">
                
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row) : ?>
            <?php if ($row->id !== $_SESSION['user_id']) : ?>
                <tr>
                    <td>
                        <?php echo $row->name; ?>
                    </td>
                    <td>
                        <?php echo $row->email; ?>
                    </td>
                    <td>
                        <form action="<?php echo URLROOT; ?>/pages/setStatus/<?php echo $row->id ?>" method="post">
                            <?php if ($row->status == 1) : ?>
                                <button type="submit" class="btn btn-danger">BLOCK</button>
                            <?php elseif ($row->status == 0) : ?>
                                <button type="submit" class="btn btn-success">ALLOW</button>
                            <?php endif ?>
                        </form>
                    </td>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
    </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>