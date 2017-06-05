<?php
	require_once("../class/DBManager.php");
	if(!isset($_COOKIE['userEmail']))
	{
		header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/signin-page.php');
	}
	$userEmail = $_COOKIE['userEmail'];
	$uploaddir = '/var/www/html/MDrive/file/video/'.$userEmail.'/';
	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

	echo '<pre>';
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
	{
		$db_manager = new DB_Manager();
		$stmt = $db_manager->pdo->prepare("SELECT user_num FROM User WHERE email = ?");
		$stmt->execute(array($userEmail));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			echo "fail";
		}
		else
		{
			$stmt = $db_manager->pdo->prepare("INSERT into Video (owner_num,name,up_date) VALUES(?,?,now())");
			$stmt->execute(array($result['user_num'],$_FILES['userfile']['name']));
			echo "success";
		}
	}
	else
	{
		echo "fail";
	}
?>