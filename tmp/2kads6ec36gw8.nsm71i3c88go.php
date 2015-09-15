<!DOCTYPE html>
<html lang="en">
<head>
<!--    <base href="<?php echo $BASE.'/'.$UI; ?>" />-->
    <base href="<?php echo $BASE.'/'; ?>" />
    <meta charset="utf-8">
    <title><?php echo $site; ?></title>
    <!-- Bootstrap -->
<!--    <link href="ui/css/bootstrap.min.css" rel="stylesheet" media="screen">-->
    <link href="ui/css/w2ui-1.4.3.min.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Lato:400,900italic,900,700italic,400italic,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="ui/css/font-awesome.min.css">
    <link rel="stylesheet" href="ui/css/colorbox.css">
    <link rel="stylesheet" href="ui/css/jquery-ui.min.css">
    <link rel="stylesheet" href="ui/css/jquery-ui.structure.min.css">
    <link rel="stylesheet" href="ui/css/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="ui/css/admin/admin-theme.css">
<!--    <link rel="stylesheet" href="ui/css/admin/admin.css">-->
</head>

<body>
    <div id="container">
        <?php echo $this->render($view,$this->mime,get_defined_vars()); ?>
    </div>
    <!-- jquery mobile / jquery desktop checking -->
    <?php if ($isMobile): ?>

            <script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>

        <?php else: ?>
            <script src="http://code.jquery.com/jquery.js"></script>

    <?php endif; ?>
    <script src="ui/js/jquery.colorbox-min.js"></script>
<!--    <script src="ui/js/bootstrap.min.js"></script>-->
    <script src="ui/js/w2ui-1.4.3.min.js"></script>
    <script type="text/javascript">
        /*
        * important: this for getting base url of application for ajax
        */
        window.base = document.getElementsByTagName('base')[0].getAttribute('href');
    </script>
    <script src="ui/js/jquery-ui.min.js"></script>
    <script src="ui/js/admin/admin.js"></script>
</body>

</html>
