<!DOCTYPE html>
<html>
<head>
	<title>
		videotest
	</title>
	<?php
		require_once("./class/DBManager.php");
		if(!isset($_COOKIE['userEmail']))
		{
			header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.html');
		}
		if(isset($_GET['']))
		$db_manager = new DB_Manager();
		$stmt = $db_manager->pdo->prepare("SELECT user_num FROM User WHERE email = ?");
		$stmt->execute(array($_COOKIE['userEmail']));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = $db_manager->pdo->prepare("SELECT video_num,name,caption_num FROM Video WHERE owner_num = ? and video_num = ?");
		$stmt->execute(array($user["user_num"],));

	?>
	<style type="text/css">
		body {
			background-color: #000000;
			margin: 0px;
			overflow:hidden;/*hide scroll*/
		}
		video::-webkit-media-controls-fullscreen-button {
			display: none;
		}
	</style>
</head>
<body>
	<video id = "vt" width="400" height="100" controls>
		<source src="../LostRoom1.mp4" type="video/mp4">
		what the?
	</video>
	<!-- jQuery -->
	<script src="./js/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#vt").attr({"width":$(window).width(),"height":$(window).height()});
		});
		$(window).resize(function(){
			$("#vt").attr({"width":$(window).width(),"height":$(window).height()});
		});
	</script>
</body>
</html>