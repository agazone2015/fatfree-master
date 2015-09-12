<table width="600" cellpadding="5" class="table table-hover table-bordered">
    <thead>
        <tr>
<!--
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Mobile</th>
            <th scope="col">Address</th>
            <th scope="col">Action</th>
-->
            <th scope="col">Name</th>
            <th scope="col">Supplier</th>
            <th scope="col">Price</th>
            <th scope="col">Description</th>
            <th scope="col">Action</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach (($users?:array()) as $user): ?>
            <tr>
                <td><?php echo trim($user['name']); ?></td>
                <td><?php echo trim($user['email']); ?></td>
                <td><?php echo trim($user['mobile']); ?></td>
                <td><?php echo trim($user['address']); ?></td>
                <td><a href="<?php echo $BASE.'/user/update/'. $user['id']; ?>" class="btn btn-primary"><i class="icon-edit icon-white"></i> Edit</a>
                    <a href="<?php echo $BASE.'/user/delete/'. $user['id']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>
                    Delete</a></td>

            </tr>
        <?php endforeach; ?>
    </tbody>

</table>
