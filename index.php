<?php

$f3=require('lib/base.php');

$f3->config('app/config.ini');
$f3->config('app/routes.ini');

$db=new DB\SQL
(
	$f3->get('db_type').':host='.$f3->get('db_host').';port='.$f3->get('db_port').';dbname='.$f3->get('db_name'),
	$f3->get('db_login'),
	$f3->get('db_password')
);

$f3->run();
?>