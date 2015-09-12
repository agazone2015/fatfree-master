<div class="navbar">
    <ul class="nav">
        <li <?php if ($page_nav == 1): ?>class="active"<?php endif; ?>>
            <a href="<?php echo $BASE.'/admin/dashboard'; ?>"><i class="fa fa-home"></i> Cafes</a>
        </li>
        <li <?php if ($page_nav == 2): ?>class="active"<?php endif; ?>>
            <a href="<?php echo $BASE.'/admin/categories'; ?>"><i class="fa fa-list"></i> Categories</a>
        </li>
        <li <?php if ($page_nav == 2): ?>class="active"<?php endif; ?>>
            <a href="<?php echo $BASE.'/admin/items'; ?>"><i class="fa fa-th-large"></i> Items</a>
        </li>
    </ul>
</div>
