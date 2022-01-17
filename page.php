<?php 
session_start();
require './core/init.php';



if ($_GET['u']) {

	if (is_bool($db->getUserByUsername($_GET['u']))) {
		redirect('error.php?status_code=404');
	}

	$username = $_GET['u'];
	$comments = $db->getAllComments($username);
	$sub_comments = $db->getAllSubComments($username);
	$user = $db->getUserByUsername($username);
	$page = $db->getPage($user['id']);

	$pageId = $page['id'];
	$visitorIp = $_SERVER['REMOTE_ADDR'];
	$db->addPageView($visitorIp,$pageId);


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


	if (isset($_POST['send_comment'])) {
		$comment_text = $_POST['comment_text'];
		$db->newComment($username,$comment_text);
		redirect('page.php?u='.$username);
	}


}else{
	redirect('error.php?status_code=404');
}




 ?>

 <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="./public/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./public/css/main.css">
	<title>Pixme | <?= $username ?></title>

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
	        <a class="nav-link" href="index.php">Register</a>
	      </div>
	    </div>
	  </div>
	</nav>

	<div class="container">
		<div class="row justify-content-center">

			<div class="col-9 mt-5 p-3 my-border my-shadow">
				<h1 class="text-center"><?= $username ?></h1>
				<div class="col-12 d-flex justify-content-center">	
					<p class="text-center mt-3"><?= $page['about'] ?></p>
				</div>
			</div>

			<div class="col-9 mt-5 mb-5 p-3 my-border my-shadow">
				<h4>Send comment to <?= $username ?></h4>
				<form method="POST" action="">
					<textarea type="text" name="comment_text" class="form-control my-border-sub" placeholder="type comment here"></textarea>
					<input type="submit" class="my-button-green mt-2" name="send_comment" value="Send">
				</form>
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
				</div>

			<?php } ?>

		</div>

	</div>

	<div class="container-fluid" style="margin-top: 150px;">
		<h5 class="text-center">made with ❤ by maru</h5>
	</div>

</body>
</html>

