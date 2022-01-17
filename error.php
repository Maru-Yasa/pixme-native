<?php
require './core/status_code.php';


$code = $_GET['status_code'] ?? 404;
header($STATUS_CODE[$code]);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="./public/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./public/css/main.css">
	<title>Pixme | <?= $code ?></title>

</head>
<body>

	<div class="container">
		<div class="row justify-content-center">

			<div class="col-5 mt-5 my-border my-shadow">
				<div class="p-5">
					<h1 class="text-center"><?= $code ?> | <?= $STATUS_CODE[$code] ?></h1>
					<h2 class="text-center"></h2>
				</div>
			</div>

		</div>
	</div>

</body>
</html>
