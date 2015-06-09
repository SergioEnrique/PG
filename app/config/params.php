<?php
# app/config/params.php

// Si es Openshift
if (getEnv("OPENSHIFT_APP_NAME")!='') {
	$container->setParameter('database_host', getEnv("OPENSHIFT_MYSQL_DB_HOST"));
	$container->setParameter('database_port', getEnv("OPENSHIFT_MYSQL_DB_PORT"));
	$container->setParameter('database_name', getEnv("OPENSHIFT_APP_NAME"));
	$container->setParameter('database_user', getEnv("OPENSHIFT_MYSQL_DB_USERNAME"));
	$container->setParameter('database_password', getEnv("OPENSHIFT_MYSQL_DB_PASSWORD"));

	// Binario para crear pdfs es diferente en local que en servidor
	$container->loadFromExtension('knp_snappy', array(
	    'pdf' => array(
	    	'enabled' => true,
	    	'binary' => '%kernel.root_dir%/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64',
	    	'options' => array(),
	    ),
	));
}
// Si es windows
else if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	// Base de datos
	$container->setParameter('database_host', '127.0.0.1');
	$container->setParameter('database_port', 'null');
	$container->setParameter('database_name', 'partygift');
	$container->setParameter('database_user', 'root');
	$container->setParameter('database_password', 'newly$dbcon');

	// PDF
	$container->loadFromExtension('knp_snappy', array(
	    'pdf' => array(
	    	'enabled' => true,
	    	'binary' => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"',
	    	'options' => array(),
	    ),
	));
}
// Si es Unix
else{
	$container->loadFromExtension('knp_snappy', array(
	    'pdf' => array(
	    	'enabled' => true,
	    	'binary' => '/usr/local/bin/wkhtmltopdf',
	    	'options' => array(),
	    ),
	));
}

?>