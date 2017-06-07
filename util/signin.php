<?php 
	require_once("../class/DBManager.php");

	if(isset($_POST["user_email"]) && isset($_POST["user_password"]))
	{
		$userEmail = $_POST["user_email"];
		$password = $_POST["user_password"];

		if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			echo "not valid email format";
		}
		else if(strlen($password) < 4)
		{
			echo "bad password";
		}
		else 
		{
			try
			{
				$db_manager = new DB_Manager();
				$stmt = $db_manager->pdo->prepare("SELECT email,password FROM User WHERE email = ?");
				$stmt->execute(array($userEmail));
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if(password_verify($password,$result["password"]))
				{
					setcookie('userEmail',$userEmail,time()+86400, '/');
					echo "success";
					echo "<script>
									window.location.replace(\"http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/main-page.php\");
								</script>";

				}
				else
				{
					setcookie('userEmail','',time()-1, '/');
					echo "signin fail";
					echo "<script>
									window.location.replace(\"http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/signin-page.php\");
								</script>";
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
		echo "signin fail";
	}
?>
