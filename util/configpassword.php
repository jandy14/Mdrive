<?php
	
	require_once("../class/DBManager.php");

	/*  
		비밀번호를 수정합니다.
	*/

	// 기존 비밀번호와 새 비밀번호 입력확인 
	if(isset($_POST["user_password"]) && isset($_POST["new_password"]))
	{
		$userEmail = $_COOKIE['userEmail'];
		$userpassword = $_POST["user_password"];
		$password = $_POST["new_password"];

		$password = password_hash($password, PASSWORD_DEFAULT);

		/*
			DB에서 기존 비밀번호 확인 요청 
		*/
		$db_manager = new DB_Manager();
		$stmt = $db_manager->pdo->prepare("SELECT user_num,password FROM User WHERE email = ?");
		$stmt->execute(array($userEmail));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if($stmt->rowCount() == 1 && password_verify($userpassword,$user["password"])) // 기존 비밀번호가 확인되면 변경합니다. 
		{
			$stmt = $db_manager->pdo->prepare("UPDATE User set password = ? WHERE user_num = ?");
			$stmt->execute(array($password,$user['user_num']));

			echo "success";
		}
	}

	header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');
?>