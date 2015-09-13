    </div>
    <?php if ($isMobile): ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
        <?php else: ?><script src="http://code.jquery.com/jquery.js"></script>
    <?php endif; ?>

<!--    <script src="../../ui/js/bootstrap.min.js"></script>-->
    <?php if ($isRichmond || $isMalvern || $isHome): ?>
        <script type="text/javascript" src="ui/js/perfect-scrollbar.jquery.min.js"></script>
    <?php endif; ?>
    <script src="ui/js/jquery.colorbox-min.js"></script>
    <?php if ($isRichmond || $isMalvern): ?>
        <script src="ui/js/gllow.js"></script>
    <?php endif; ?>
    </body>
</html>
