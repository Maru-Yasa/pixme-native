<?php
session_start();
require "./core/init.php";


$message = '';

if (isset($_POST['submit'])){

	if ($_POST['submit'] === "Register") {
		$newUser = [
			'username' => $_POST['username'],
			'password' => $_POST['password']
		];
		$isValid = $db->validateRegister($newUser['username'],$newUser['password']);
		if ($isValid) {
			$user = $db->makeUser($newUser);
			if($user === true){
				$message = '<label class="my-label-success">success making new user, try to login</label>';
			}else{
				$message = '<label class="my-label-danger">'. $user .'</label>';
			}
		}else{
			$message = '<label class="my-label-danger">please dont use spcaes and special character except ( _ )</label>';
		}


	}

	elseif ($_POST['submit'] === "Login") {
		$username = $_POST['username'] ?? '';
		$password = $_POST['password'] ?? '';


		if ($db->login($username,$password) === true) {
			$_SESSION['user'] = $username;
			$_SESSION['isAuthenticated'] = true;
			redirect('profile.php');
		}else{
			$message = '<label class="my-label-danger">username or password not valid</label>';
		}


	}
}


if (isset($_POST['to_profile'])) {
	redirect('profile.php');
}

elseif (isset($_POST['to_page'])) {
	redirect('page.php?u='.$_SESSION['username']);
}



?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="./public/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./public/css/main.css">
	<title>pixme | Home</title>

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


			<?php if (!$_SESSION['isAuthenticated']) { ?>
			<div class="col-6 mt-5 my-border my-shadow">
				<h1 class="text-center mb-3 mt-2">Login / Register</h1>
				<form method="POST" action="./">
					  <div class="mb-3">
					    <label for="username" class="form-label">Username</label>
					    <input type="text" name="username" class="form-control my-border-sub" id="username" aria-describedby="usernameHelp">
					  </div>
					  <div class="mb-3">
					    <label for="exampleInputPassword1" class="form-label">Password</label>
					    <input type="password" name="password" class="form-control my-border-sub" id="exampleInputPassword1">
						<?= $message ?>
					  </div>
					  <div class="mb-3">
					  	<input type="submit" name="submit" class="my-button-black" value="Login">
					    <input type="submit" name="submit" class="my-button-white" value="Register">
					  </div>				
				</form>
			</div>
			<?php } else { ?>

				<div class="col-7 mx-5 my-border my-shadow p-5" style="margin-top:90px;">
					<h1 class="text-center mb-3">You've alredy loggedin</h1>
					<form method="POST" class="d-flex justify-content-center">
						<input type="submit" name="to_profile" class="my-button-green mx-1" value="Goto my profile">
						<input type="submit" name="to_page" class="my-button-yellow mx-1" value="Goto my page">
					</form>
				</div>
			<?php } ?>

			<div class="col-9 mb-5 p-5 my-border my-shadow blue" id="about" style="margin-top: 250px;">
				<div class="col-12 mb-5" >
					<h1 >About</h1>
					<p class="" id="">	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
					tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
					quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
					consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
					cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
					proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div>

				<div class="col-12 " style="">
					<h1>About Developer</h1>
					<p class="">	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
					tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
					quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
					consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
					cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
					proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</div>
				
			</div>

		</div>
	</div>


	<div class="container-fluid" style="margin-top: 150px;">
		<h5 class="text-center">made with ❤ by maru</h5>
	</div>

</body>
</html>
