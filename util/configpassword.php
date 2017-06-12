<?php
	
	require_once("../class/DBManager.php");

	if(isset($_POST["user_password"]) && isset($_POST["new_password"]))
	{
		$userEmail = $_COOKIE['userEmail'];
		$userpassword = $_POST["user_password"];
		$password = $_POST["new_password"];

		$password = password_hash($password, PASSWORD_DEFAULT);

		$db_manager = new DB_Manager();
		$stmt = $db_manager->pdo->prepare("SELECT user_num,password FROM User WHERE email = ?");
		$stmt->execute(array($userEmail));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if($stmt->rowCount() == 1 && password_verify($userpassword,$user["password"]))
		{
			$stmt = $db_manager->pdo->prepare("UPDATE User set password = ? WHERE user_num = ?");
			$stmt->execute(array($password,$user['user_num']));

			echo "success";
		}
	}

	header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');
?>