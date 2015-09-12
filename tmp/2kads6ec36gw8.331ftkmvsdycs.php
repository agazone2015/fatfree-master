<?php if ($isSpecialOn): ?>
    <aside id="specialMenu">
        <strong id="specialHeader">Chef Special Today</strong>
        <ul id="specialContent">
            <?php foreach (($specials?:array()) as $special): ?>
                <li>
                    <a href="" class="itemName"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo $special['itemName']; ?></a>
                    <span class="itemDesc"><?php echo $special['description']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
<?php endif; ?>
<?php if ($isPromotionOn): ?>
    <section id="promotion">
        <strong class="promotionHeader"><i class="fa fa-star-o"></i> Promotion</strong>
        <p class="promotionContent">
            Save up to $10 with your order. Go to our homepage at http://gllow.com.au and order online now. Remember to apply this code: ABCDEFG
        </p>
    </section>
<?php endif; ?>
