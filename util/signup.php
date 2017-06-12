<?php 
	require_once("../class/DBManager.php");

	/*
		회원가입
	*/

	if(isset($_POST["user_email"]) && isset($_POST["user_password"]) && isset($_POST["user_name"]))
	{
		$userEmail = $_POST["user_email"];
		$password = $_POST["user_password"];
		$username = $_POST["user_name"];

		// 이메일 형식 확인
		if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			echo "not valid email format";
		}
		// 비밀번호 최소 4자 확인
		else if(strlen($password) < 4)
		{
			echo "bad password";
		}
		// 유저 이름 최소 4자 확인
		else if(strlen($username) < 4)
		{
			echo "bad name";
		}
		else 
		{
			try
			{	
				/*
					입력된 이메일이 이미 DB에 존재하는 지 확인 요청
				*/
				$db_manager = new DB_Manager();
				$stmt = $db_manager->pdo->prepare("SELECT email FROM User WHERE email = ?");
				$stmt->execute(array($userEmail));
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if($result["email"] == $userEmail) // 이미 이메일이 DB에 존재
				{
					echo "this email already exists.";
				}
				else
				{
					/*
						존재하지 않는 다면 
						패스워드를 해싱해서 DB에 유저정보(이메일, 비밀번호, 유저 이름, 가입날짜) 추가 요청
					*/
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
	header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
?>
