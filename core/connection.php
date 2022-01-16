<?php

// if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
// 	header("Location: error.php?status_code=403");
// 	exit();
// }

class Connection{
	public PDO $pdo;

	public function __construct($dbconfig = []){
		$dbDsn = $dbconfig['dsn'] ?? '';
		$username = $dbconfig['user'] ?? '';
        $password = $dbconfig['password'] ?? '';
        try{
        	$this->pdo = new PDO($dbDsn, $username, $password);
        	$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
        	echo 'Connection failed: ' . $e->getMessage();
        	exit();
        }

	}

    public function prepare($sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

}

class Database extends Connection{

	public function execute($mysql){
		$statement = $this->prepare($mysql);
		$statement->execute();
	}

	public function getAll($mysql)
	{
		$statement = $this->prepare($mysql);
		$statement->execute();		
		return $statement->fetchAll(PDO::FETCH_COLUMN);
	}

/*
====================================================================
	Users methods
====================================================================
*/

	public function makeUser($user)
	{
		$username = $user['username'] ?? "";
		$password = $user['password'] ?? "";
		$password = password_hash($password, PASSWORD_DEFAULT);
		try {
			$mysql = "INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES (NULL, '$username', '$password', current_timestamp());";
			$statement = $this->prepare($mysql);
			$statement->execute();
			$this->makePage($this->getUserId($username));
			return true;
		} catch (Exception $e) {
			if ($e->errorInfo[1] == 1062) {
				return "username alredy exist";
			}else{
				return false;
			}
			
		}
	}

	public function login($username,$password)
	{
		$query = "SELECT * FROM `users` WHERE `username` = :username";
		$statement = $this->prepare($query);		
		$statement->execute(
			array(
				'username' => $username,
			)
		);
		$user = $statement->fetch(PDO::FETCH_ASSOC);
		$count = $statement->rowCount();
		if ($count > 0 and password_verify($password, $user['password'])) {
			$_SESSION['username'] = $username;
			return true;
		}else{
			return false;
		}
	}

	public function validateRegister($username,$password)
	{
		// [^\w\_] : username regex
		$usernameRegexValidator = '/[\'\/~`\!@#\$%\^&\*\(\)-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';
		$usernameValidator = preg_match($usernameRegexValidator, $username);

		if ($usernameValidator === 1) {
		 	return false;
		}else{
			return true;
		} 
	}

	public function getUserById($id)
	{
		$query = "SELECT * FROM users WHERE id = :id";
		$statement = $this->prepare($query);
		$statement->execute(array(
			'id' => $id
		));		
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getUserByUsername($username)
	{
		$query = "SELECT * FROM users WHERE username = :username";
		$statement = $this->prepare($query);
		$statement->execute(array(
			'username' => $username
		));		
		return $statement->fetch(PDO::FETCH_ASSOC);

	}

	public function updateUser($id,$username,$password)
	{

 		try {
			$query = "UPDATE `users`   
	   		SET `username` = :username,
	       		`password` = :password 
	 		WHERE `id` = :id";

	 		$statement = $this->prepare($query);
	 		$statement->execute(array(
	 			'id' => $id, 
	 			'username' => $username,
	 			'password' => password_hash($password,PASSWORD_DEFAULT)
	 		));
	 		$_SESSION['username'] = $username;
 			return true;
 		} catch (Exception $e) {
 			if ($e->errorInfo[1] == 1062) {
				return "username alredy exist";
			}else{
				return false;
			}
			
 		}
	}


	public function getUserId($username)
	{
		$id = $this->getUserByUsername($username);
		return $id['id'];
	}

	public function getUsernameById($id)
	{
		$username = $this->getUserById($id);
		return $username['username'];
	}

	public function logout()
	{
		session_destroy();
		$_SESSION['isAuthenticated'] = false;
	}


/*
====================================================================
	Page methods
====================================================================
*/


	public function makePage($userId)
	{
		$query = "INSERT INTO `pages` (`id`, `user`, `visitor`, `comments_count`, `about`) VALUES 
		(NULL, :userId, '0', '0', NULL);";
		$statement = $this->prepare($query);
		$statement->execute(array(
			'userId' => $userId
		));
	}


	public function getPage($userId)
	{
		$query = "SELECT * FROM pages WHERE user = :userId";
		$statement = $this->prepare($query);
		$statement->execute(array(
			'userId' => $userId
		));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}


	public function updatePage($id,$about)
	{

 		try {
			$query = " UPDATE `pages`   
	   		SET `about` = :about
	 		WHERE id = :id ";

	 		$statement = $this->prepare($query);
	 		$statement->execute(array(
	 			'about' => $about,
	 			'id' => $id
	 		));
 			return true;
 		} catch (Exception $e) {
 			throw $e;
 			return false;	
 		}
	}

/*
====================================================================
	Comment methods
====================================================================
*/

	public function getAllComments($username)
	{
		$id = $this->getUserId($username);

		$query = "SELECT * FROM comments WHERE user = :id";
		$statement = $this->prepare($query);
		$statement->execute(array(
			'id' => $id
		));
		$comments = $statement->fetchAll(PDO::FETCH_ASSOC);
		return array_reverse($comments);

	}



	public function newComment($user,$comment)
	{
		$userId = $this->getUserId($user);
		$query = "INSERT INTO `comments` (`id`, `user`, `comment`, `created_at`) VALUES (NULL, :userId, :comment, current_timestamp());";
		$statement = $this->prepare($query);
		$statement->execute(array(
			"userId" => $userId,
			"comment" => $comment
		));

	}


/*
====================================================================
	Sub Comment methods
====================================================================
*/

	public function newSubComment($commentId,$userId,$reply_text)
	{
		$query = "INSERT INTO `sub_comments` (`id`, `user`, `parent_comment`, `comment`, `created_at`) 
		VALUES (NULL, :userId, :commentId, :reply_text, current_timestamp() );";
		$statement = $this->prepare($query);
		$statement->execute(array(
			"userId" => $userId,
			"commentId" => $commentId,
			"reply_text" => $reply_text
		));
	}


	public function getAllSubComments($username)
	{	
		$id = $this->getUserId($username);

		$query = "SELECT * FROM sub_comments WHERE user = :id";
		$statement = $this->prepare($query);
		$statement->execute(array(
			'id' => $id
		));
		$comments = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $comments;
	}


}
