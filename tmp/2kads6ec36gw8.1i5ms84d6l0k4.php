<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="Gllow Taiwanese" />
    <base href="<?php echo $BASE.'/'; ?>" />
    <link href='https://fonts.googleapis.com/css?family=Lato:400,900italic,900,700italic,400italic,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,400italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="ui/css/font-awesome.min.css">
    <link rel="stylesheet" href="ui/css/colorbox.css">
    <title><?php echo $site; ?></title>
    <!-- Bootstrap -->
    <!--        <link href="../../ui/css/bootstrap.min.css" rel="stylesheet" media="screen">-->
    <?php if ($isRichmond || $isMalvern || $isHome): ?>
        <link rel="stylesheet" href="ui/css/perfect-scrollbar.min.css">
    <?php endif; ?>
    <link rel="stylesheet" href="ui/css/style.css">
</head>

<body>
    <div id="container">
        <div id="background" class="<?php echo $isBlurred; ?>"></div>
        <div id="logo"><img src="ui/img/Gllow_logo5.png"/></div>
        <nav id="navbar">
            <ul>
                <li><a class="<?php echo $isHome; ?>" href="">Home</a></li>
                <li><a class="<?php echo $isRichmond; ?>" href="Richmond/menu">Richmond Menu</a></li>
                <li><a class="<?php echo $isMalvern; ?>" href="Malvern/menu">Malvern Menu</a></li>
                <li><a class="<?php echo $isCafes; ?>" href="cafes">Find Us</a></li>
            </ul>
        </nav>
        <section id="socialMedia">
            <ul>
                <li class="socialMedia"><a href="" target="_blank" title="Find us on Facebook"><img src="ui/img/media/facebook.png"/></a></li>
                <li class="socialMedia"><a href="" target="_blank" title="Find us on Twitter"><img src="ui/img/media/twitter.png"/></a></li>
                <li class="socialMedia"><a href="" target="_blank" title="Find us on Instagram"><img src="ui/img/media/instagram.png"/></a></li>
            </ul>
        </section>
