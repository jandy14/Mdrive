<?php 
	require_once("../class/DBManager.php");

	/*
		로그인
	*/

	// 유저 이메일과 비밀번호 입력 확인
	if(isset($_POST["user_email"]) && isset($_POST["user_password"])) 
	{
		$userEmail = $_POST["user_email"]; // 입력된 이메일
		$password = $_POST["user_password"]; // 입력된 비밀번호

		// 이메일 형식이 맞는 지 확인
		if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) 
		{
			echo "not valid email format";
		}
		// 비밀번호 최소 4자 확인
		else if(strlen($password) < 4)
		{
			echo "bad password";
		}
		else 
		{
			try
			{
				/*
					입력된 이메일의 비밀번호 DB에 요청
					입력된 비밀번호와 매칭된다면 이메일을 쿠키에 저장
					안된다면 쿠키를 초기화 
				*/
				$db_manager = new DB_Manager();
				$stmt = $db_manager->pdo->prepare("SELECT email,password FROM User WHERE email = ?");
				$stmt->execute(array($userEmail));
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if(password_verify($password,$result["password"]))
				{
					setcookie('userEmail',$userEmail,time()+86400, '/');
					echo "success";
					//echo "<script>
					//				window.location.replace(\"http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/main-page.php\");
					//			</script>";
					header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');

				}
				else
				{
					setcookie('userEmail','',time()-1, '/');
					echo "signin fail";
					//echo "<script>
					//				window.location.replace(\"http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/signin-page.php\");
					//			</script>";
					header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
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
