<table class="table table-sm table-bordered table-hover">
    <thead>
    <tr>
        <th class="text-center">#</th>
        <th>Tên Nhân Viên</th>
        <th>Tên Đăng Nhập</th>
        <th>Nhóm</th>
    </tr>
    </thead>
    <tbody>
    <?php if( isset($users) && !empty($users) ): foreach ($users AS $u):
        $checked = in_array($u->uid,$selected) ? "checked" : NULL;
        ?>
        <tr>
            <th class="text-center"><input name="<?php echo $name?>[]" value="<?php echo $u->uid?>" type="checkbox" <?php echo $checked; ?> ></th>
            <td><?php echo $u->name;?></td>
            <td scope="row"><?php echo $u->username;?></td>
            <td><?php echo $u->groupname;?></td>
        </tr>
    <?php endforeach; ?>
    <?php else:?>
        <tr><td colspan="4" class="text-center">Không có nhân viên nào</td></tr>
    <?php endif;?>
    </tbody>
</table>