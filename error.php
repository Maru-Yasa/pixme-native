<?php
require './core/status_code.php';


$code = $_GET['status_code'] ?? 404;
header($STATUS_CODE[$code]);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="./public/css/main.css">
	<title>Secreto clone | <?= $code ?></title>

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
