<?php

require 'connection.php';
require 'status_code.php';
require 'utils.php';

$db_config = [
	'dsn' => "mysql:host=127.0.0.1;port=3306;dbname=secreto",
	"user" => "root",
	"password" => "root"
];

$db = new Database($db_config);

