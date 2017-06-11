<?php
	require_once("../class/DBManager.php");
	if(!isset($_COOKIE['userEmail']))
	{
		header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.html');
	}
	$userEmail = $_COOKIE['userEmail'];
	$uploaddir = '/var/www/html/MDrive/file/video/'.$userEmail.'/';
	
	$videoName = basename($_FILES['userfile']['name']);
	if(strtolower(substr(strrchr($videoName,"."),1)) != 'mp4')
		die('fail');
	$db_manager = new DB_Manager();
	$stmt = $db_manager->pdo->prepare("	SELECT Video.name
										FROM User join Video on User.user_num = Video.owner_num
										WHERE Video.name = ? and User.email = ?");
	$stmt->execute(array($videoName,$userEmail));
	if($stmt->rowCount() != 0)
	{
		for($i = 1; ;++$i)
		{
			$stmt = $db_manager->pdo->prepare("	SELECT Video.name
												FROM User join Video on User.user_num = Video.owner_num
												WHERE Video.name = ? and User.email = ?");
			$stmt->execute(array("(".$i.")".$videoName,$userEmail));
			if($stmt->rowCount() == 0)
			{
				$videoName = "(".$i.")".$videoName;
				break;
			}
		}
	}


	$uploadfile = $uploaddir . $videoName;

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
	{
		//user check
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
			$stmt->execute(array($result['user_num'],$videoName));
			echo "success";
		}
	}
	else
	{
		echo "fail";
	}
?>