<?php 
	require_once("../class/DBManager.php");

	if(isset($_POST["user_email"]) && isset($_POST["user_password"]) && isset($_POST["user_name"]))
	{
		$userEmail = $_POST["user_email"];
		$password = $_POST["user_password"];
		$username = $_POST["user_name"];

		if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			echo "not valid email format";
		}
		else if(strlen($password) < 4)
		{
			echo "bad password";
		}
		else if(strlen($username) < 4)
		{
			echo "bad name";
		}
		else 
		{
			try
			{
				$db_manager = new DB_Manager();
				$stmt = $db_manager->pdo->prepare("SELECT email FROM User WHERE email = ?");
				$stmt->execute(array($userEmail));
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result["email"] == $userEmail)
				{
					echo "this email already exists.";
				}
				else
				{
					$password = password_hash($password, PASSWORD_DEFAULT);
					$stmt = $db_manager->pdo->prepare("INSERT into User (email,password,name,reg_date) VALUES(?,?,?,now())");
					$stmt->execute(array($userEmail,$password,$username));

					mkdir("/var/www/html/MDrive/file/video/".$userEmail);
					mkdir("/var/www/html/MDrive/file/caption/".$userEmail);

					echo "success";
				}
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
		}
	}
	else
	{
		echo "signup fail";
	}
	header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.html');
?>
