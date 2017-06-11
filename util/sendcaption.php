<?php
	require_once("../class/DBManager.php");
	if(!isset($_COOKIE['userEmail']))
	{
		header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.html');
	}
	$userEmail = $_COOKIE['userEmail'];
	$uploaddir = '/var/www/html/MDrive/file/caption/'.$userEmail.'/';
	
	$captionName = basename($_FILES['captionfile']['name']);
	if(strtolower(substr(strrchr($captionName,"."),1)) != 'vtt')
	{
		die('typefail');
	}
	$video_num = $_POST['video_num'];
	$db_manager = new DB_Manager();
	$stmt = $db_manager->pdo->prepare("	SELECT Caption.name
										FROM User join Caption on User.user_num = Caption.owner_num
										WHERE Caption.name = ? and User.email = ?");
	$stmt->execute(array($captionName,$userEmail));
	if($stmt->rowCount() != 0)
	{
		for($i = 1; ;++$i)
		{
			$stmt = $db_manager->pdo->prepare("	SELECT Caption.name
												FROM User join Caption on User.user_num = Caption.owner_num
												WHERE Caption.name = ? and User.email = ?");
			$stmt->execute(array("(".$i.")".$captionName,$userEmail));
			if($stmt->rowCount() == 0)
			{
				$captionName = "(".$i.")".$captionName;
				break;
			}
		}
	}


	$uploadfile = $uploaddir . $captionName;

	if (move_uploaded_file($_FILES['captionfile']['tmp_name'], $uploadfile))
	{
		//check video and owner
		$stmt = $db_manager->pdo->prepare("SELECT User.user_num
											FROM User join Video on User.user_num = Video.owner_num 
											WHERE User.email = ? and Video.video_num = ?");
		$stmt->execute(array($userEmail,$video_num));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			echo "dbfail";
		}
		else
		{
			$stmt = $db_manager->pdo->prepare("INSERT into Caption (video_num,owner_num,name,up_date) VALUES(?,?,?,now())");
			$stmt->execute(array($video_num,$result['user_num'],$captionName));
			echo "success";
		}
	}
	else
	{
		echo "movefail";
	}
?>