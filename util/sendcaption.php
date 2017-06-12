<?php
	require_once("../class/DBManager.php");

	/*
		서버에 자막파일을 업로드 합니다.
	*/

	if(!isset($_COOKIE['userEmail'])) // 로그인 했는지 확인합니다.
	{
		// 안했다면 초기화면으로
		header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
	}
	$userEmail = $_COOKIE['userEmail']; // 유저 이메일
	$uploaddir = '/var/www/html/MDrive/file/caption/'.$userEmail.'/'; // 서버에 업로드 될 로컬주소 
	
	$captionName = basename($_FILES['captionfile']['name']); // 자막 파일 이름
	if(strtolower(substr(strrchr($captionName,"."),1)) != 'vtt') // vtt 파일인지 확인
	{
		die('typefail');
	}

	/*
		DB에서 비디오의 같은 이름의 자막 유무를 요청
	*/

	$video_num = $_POST['video_num']; // 비디오 넘버
	$db_manager = new DB_Manager();
	$stmt = $db_manager->pdo->prepare("	SELECT Caption.name
										FROM User join Caption on User.user_num = Caption.owner_num
										WHERE Caption.name = ? and User.email = ?");
	$stmt->execute(array($captionName,$userEmail));
	if($stmt->rowCount() != 0) // 이미 자막이 있다면
	{
		for($i = 1; ;++$i)
		{
			/*
				DB에서 비디오의 같은 이름의 자막 유무를 요청
			*/
			$stmt = $db_manager->pdo->prepare("	SELECT Caption.name
												FROM User join Caption on User.user_num = Caption.owner_num
												WHERE Caption.name = ? and User.email = ?");
			$stmt->execute(array("(".$i.")".$captionName,$userEmail));

			// 이름이 같은 게 없을 때 까지 (i) 번호를 붙입니다.
			if($stmt->rowCount() == 0)
			{
				$captionName = "(".$i.")".$captionName;
				break;
			}
		}
	}


	$uploadfile = $uploaddir . $captionName; // 최종 업로드 로컬 주소

	if (move_uploaded_file($_FILES['captionfile']['tmp_name'], $uploadfile))
	{
		/*
			현재 유저와 비디오 주인이 동일한지 확인 요청
		*/
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
			/*
				유저가 확인 되었다면 DB에 자막 업로드 추가 요청
			*/

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