<?php

// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config/config.ini');
$f3->config('config/routes.ini');
//$f3->route('GET /admin', function ($f3) {
//   echo "???";
//});
//$f3->route('GET|POST /admin/login', function ($f3) {
//    if ($f3->exists('GET.name')) {
//        echo $f3->get('GET.name');
//    }
//});
$f3->run();


//$f3->set('ONERROR', function ($f3) {
//   echo \Template::instance()->render('error.html');
//});

//$f3->set('DEBUG',1);
//if ((float)PCRE_VERSION<7.9)
//	trigger_error('PCRE version is out of date');
//
//// Load configuration
//$f3->config('config.ini');
//
//$f3->route('GET /', function () {
//    echo "this is demo";
//});
//$f3->run();
//$f3->route('GET /order/@name', 'Order');
//
//$f3->route('GET /userref',
//	function($f3) {
//		$f3->set('content','userref.htm');
//		echo View::instance()->render('layout.htm');
//	}
//);
//
//$f3->route('GET /hi',
//	function($f3) {
//		$f3->set('content','welcome.htm');
//		echo View::instance()->render('layout.htm');
//	}
//);
//
//
//function Index($f3) {
//    echo "Hah this is good";
//}
//
//function Order($f3) {
//    echo "How do we get here? {$f3->get('PARAMS.name')}";
//}
