<?php
	require_once("../class/DBManager.php");

	/*
		비디오 파일을 업로드합니다.
	*/

	if(!isset($_COOKIE['userEmail'])) // 로그인 했는지 확인합니다.
	{
		// 안했다면 초기화면으로
		header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
	}
	$userEmail = $_COOKIE['userEmail']; // 유저 이메일 
	$uploaddir = '/var/www/html/MDrive/file/video/'.$userEmail.'/'; // 서버에 업로드 될 로컬주소 
	
	/* 비디오 이름 변수들 */
	$videoName = basename($_FILES['userfile']['name']); 
	$anotherName = basename($_FILES['userfile']['name']);

	if(strtolower(substr(strrchr($videoName,"."),1)) != 'mp4') // mp4 파일인지 확인
		die('fail');

	/*
		DB에서 같은 비디오 이름의 유무를 요청
	*/
	$db_manager = new DB_Manager();
	$stmt = $db_manager->pdo->prepare("	SELECT Video.name
										FROM User join Video on User.user_num = Video.owner_num
										WHERE Video.name = ? and User.email = ?");
	$stmt->execute(array($videoName,$userEmail));
	if($stmt->rowCount() != 0) // 이름이 같을 경우
	{
		for($i = 1; ;++$i)
		{
			/*
				DB에서 같은 비디오 이름의 유무를 요청 
			*/
			$stmt = $db_manager->pdo->prepare("	SELECT Video.name
												FROM User join Video on User.user_num = Video.owner_num
												WHERE Video.name = ? and User.email = ?");
			$stmt->execute(array("(".$i.")".$videoName,$userEmail));
			// 이름이 같은 게 없을 때 까지 (i) 번호를 붙입니다.
			if($stmt->rowCount() == 0) 
			{
				$videoName = "(".$i.")".$videoName;
				$anotherName = "\\(".$i."\\)".$anotherName;
				break;
			}
		}
	}


	$uploadfile = $uploaddir . $videoName; // 최종 업로드 로컬 주소

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
	{
		// 유저를 DB로 확인
		$stmt = $db_manager->pdo->prepare("SELECT user_num FROM User WHERE email = ?");
		$stmt->execute(array($userEmail));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			echo "fail";
		}
		else
		{
			/* 
				유저가 확인 되었다면 DB에 비디오 업로드 추가 요청
			*/

			$anotherfile = $uploaddir . $anotherName;
			// 이미지 썸네일 추출
			exec("ffmpeg -i ".$anotherfile." -ss 00:00:01 -s 700x400 -vframes 1 /var/www/html/MDrive/file/thumbnail/".$userEmail."/".$anotherName.".png");
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