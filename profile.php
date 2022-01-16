<?php
session_start();
require './core/init.php';

if ($_SESSION['isAuthenticated'] === false) {
	header('Location: index.php');
	exit();
}

$USER = [
	'username' => $_SESSION['username']
];

$user = $db->getUserByUsername($_SESSION['username']);
$page = $db->getPage($user['id']);

$comments = $db->getAllComments($_SESSION['username']);
$sub_comments = $db->getAllSubComments($_SESSION['username']);

for ($i=0; $i < count($comments); $i++) { 
	$items = [];
	$comment_id = $comments[$i]['id'];
	for ($j=0; $j < count($sub_comments); $j++) { 
		$sub_comment_id = $sub_comments[$j]['parent_comment'];
		if ($comment_id === $sub_comment_id) {
			$items[] = [
				'id' => $sub_comment_id,
				'user' => $db->getUsernameById($sub_comments[$j]['user']),
				'comment' => $sub_comments[$j]['comment']
			]; 
			$comments[$i]['sub_comments'] = $items;
		}
	}
}

// echo "<pre>";
// var_dump($comments);
// echo "</pre>";

if (isset($_POST['logout'])) {
	session_destroy();
	$db->logout();
	redirect("index.php");
}
if (isset($_POST['edit'])) {
	redirect('editprofile.php');
}

if (isset($_POST['page'])) {
	redirect('page.php?u='.$_SESSION['username']);
}

if (isset($_POST['reply'])) {

	$commentId = $_POST['commentId'];
	$reply = $_POST['reply_text'];

	$db->newSubComment(
		$commentId,
		$db->getUserId($_SESSION['username']),
		$reply
	);
	redirect('profile.php');

}


?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="./public/css/main.css">
	<title>Secreto clone</title>

</head>
<body>

	<nav class="navbar navbar-expand mb-5 navbar-light my-border-nav bg-light">
	  <div class="container-fluid">
	    <a class="navbar-brand" href="#">Pixme</a>
	    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
	      <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
	      <div class="navbar-nav">
	        <a class="nav-link" href="index.php">Home</a>
	        <a class="nav-link" href="index.php#about">About</a>
	      </div>
	    </div>
	  </div>
	</nav>

	<div class="container">
		<div class="row justify-content-center">

			<div class="col-9 mt-5 mb-5 p-3 my-border my-shadow">
				<h1 class="text-center">Hello <?= $_SESSION['username'] ?></h1>
				<h5 class="text-center"> <?= count($comments) ?> Comments | 0 viewers</h5>
				<p class="text-center"><?= $page['about'] ?></p>
				<div class="mb-2">
					<form method="POST" class="d-flex justify-content-center" action="">
						<input type="submit" name="page" value="Goto my page" class="my-button-green m-2">
						<input type="submit" name="edit" value="Edit profile" class="my-button-yellow m-2">
						<input type="submit" name="logout" value="Logout" class="my-button-pink m-2">
					</form>
				</div>
			</div>


			<?php foreach ($comments as $comment) { ?>

				<div class="col-9 mt-5 p-3 my-border my-shadow">
					<h4 class="text-left">Anonymous</h4>
					<p><?= $comment['comment'] ?></p>
					<span class="text-right text-muted"> <?= $comment['created_at'] ?> </span>
					<hr>

					<?php foreach ($comment['sub_comments'] as $sub) { ?>
					<p class="mx-3 my-0"> <span class="fw-bold"> <?= $sub['user'] ?> </span> : <?= $sub['comment'] ?></p>					
					<?php } ?>
					<form method="POST" action="" class="mt-3 row g-2">
						<div class="col-sm-8">
							<input type="text" name="reply_text" class="form-control my-border-sub" placeholder="type reply here">
							<input type="text" name="commentId" value="<?= $comment['id'] ?>" hidden>
						</div>
						<div class="col-sm">
							<input type="submit" class="my-button-black" name="reply" value="Send">							
						</div>
					</form>
				</div>

			<?php } ?>

		</div>

	</div>

	<div class="container-fluid" style="margin-top: 150px;">
		<h5 class="text-center">made with ❤ by maru</h5>
	</div>

</body>
</html>