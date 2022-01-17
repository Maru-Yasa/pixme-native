<?php 
session_start();
require './core/init.php';

if (!$_SESSION['isAuthenticated']) {
	redirect('index.php');
}

$user = $db->getUserByUsername($_SESSION['username']);
$page = $db->getPage($user['id']);
$message = '';
$message_pages = '';

// echo "<pre>";
// var_dump($page['about']);
// echo "</pre>";

if (isset($_POST['cancel'])) {
	redirect('profile.php');
}

elseif (isset($_POST['edit_desc'])) {
	$pageId = $page['id'];
	$desc = $_POST['desc'];
	
	if ($db->updatePage($pageId,$desc)) {
		redirect('profile.php');		
	}else{
		$message_pages = '<label class="my-label-danger">somethings wrong, i can feel it</label>';
	}
}

elseif (isset($_POST['edit'])) {
	$id = $user['id'];
	$username = $_POST['username'];
	if($_POST['password'] === ''){
		$_POST['password'] = $user['password'];
	}
	$password = $_POST['password'];

	$newUser = $db->updateUser($id,$username,$password);
	if (is_bool($newUser) and $newUser === false) {
		// code...
	}elseif(is_string($newUser)){
		$message = '<label class="my-label-danger">'. $newUser .'</label>';
	}elseif(is_bool($newUser) and $newUser === true){
		redirect('profile.php');
	}

}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="./public/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./public/css/main.css">
	<title>Pixme | Edit profile</title>

</head>
<body>

	<div class="container">
		<div class="row justify-content-center">

			<div class="col-6 mb-5 mt-5 p-3 my-border my-shadow">
				<h1 class="text-center">Edit Profile</h1>
					<form method="POST" class="" action="">
						  <div class="mb-3">
						    <label for="username" class="form-label">ID</label>
						    <input type="text" name="id" value="<?= $user['id'] ?>" class="form-control my-border-sub" id="username" disabled>
						  </div>
						  <div class="mb-3">
						    <label for="username" class="form-label">Username</label>
						    <input type="text" name="username" value="<?= $user['username'] ?>" class="form-control my-border-sub" id="username" aria-describedby="usernameHelp">
						  </div>
						  <div class="mb-3">
						    <label for="exampleInputPassword1" class="form-label">Password</label>
						    <input type="password" name="password" class="form-control my-border-sub" id="exampleInputPassword1">
						    <div id="emailHelp" class="form-text">keep this blank if you're not gonna change your password</div>
							<?= $message ?>
						  </div>
						  <div class="mb-3">
						  	<input type="submit" name="edit" class="my-button-green" value="Edit">
						    <input type="submit" name="cancel" class="my-button-pink" value="Cancel">
						  </div>				
					</form>
			</div>


			<div class="col-6 mx-2 mb-5 mt-5 p-3 my-border my-shadow">
				<h1 class="text-center">Edit Page's desc</h1>
					<form method="POST" class="" action="">
						  <div class="mb-3">
						    <label for="username" class="form-label">Describe about you :</label>
						    <textarea class="form-control my-border-sub" name="desc"> <?= $page['about'] ?> </textarea>
						    <?= $message_pages ?>
						  </div>
						  <div class="mb-3">
						  	<input type="submit" name="edit_desc" class="my-button-green" value="Edit">
						    <input type="submit" name="cancel" class="my-button-pink" value="Cancel">
						  </div>				
					</form>
			</div>
	</div>


	<div class="container-fluid" style="margin-top: 150px;">
		<h5 class="text-center">made with ❤ by maru</h5>
	</div>
</body>
</html>