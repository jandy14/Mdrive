<?php
	/*
		로그아웃
	*/

	setcookie('userEmail','',time()-1, '/'); // 쿠키 초기화
	header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.php');
?>