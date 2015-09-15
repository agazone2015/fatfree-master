<?php if ($isSpecialOn): ?>
    <aside id="specialMenu">
        <strong id="specialHeader">Chef Special Today</strong>
        <div id="specialContentWrapper">
            <ul id="specialContent">
                <?php foreach (($specials?:array()) as $special): ?>
                    <li>
                        <a href="" class="itemName"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo $special['itemName']; ?></a>
                        <span class="itemDesc"><?php echo $special['description']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>
<?php endif; ?>
<?php if ($isPromotionOn): ?>
    <section id="promotion">
        <strong class="promotionHeader"><i class="fa fa-star-o"></i> Promotion</strong>
        <p class="promotionContent">
            <?php echo $promotionText; ?>

        </p>
    </section>
<?php endif; ?>
<script>
    window.onload = function () {
        $('#specialContentWrapper').perfectScrollbar();
    }
</script>
