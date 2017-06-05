<!DOCTYPE html>
<html>
<head>
	<?php
	if(!isset($_COOKIE['userEmail']))
	{
		header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/signin-page.php');
	}
	?>
    <meta charset="utf-8">
    <style type="text/css">
    	#dropbox
		{
			border:2px dotted #0B85A1;
			width:400px;
			height:100px;
			color:#92AAB0;
			text-align:center;
			vertical-align:middle;
			padding:10px 10px 10px 10px;
			margin-bottom:10px;
			font-size:200%;
		}
    </style>
</head>
<body>
	
	<div id="dropbox">drop file here</div>


	<script src="./js/jquery.js"></script>
	<script type="text/javascript">
		$("#dropbox").on("dragenter",function(e){
			e.stopPropagation();
			e.preventDefault();
			$(this).css("border", "2px solid #0B85A1");
		});
		$("#dropbox").on("drop",function(e){
			e.stopPropagation();
			e.preventDefault();
			$(this).css("border", "2px dotted #0B85A1");
			var file = e.originalEvent.dataTransfer.files[0];
			var extend = file.name.split('.')[1];
			if(extend === "mp4")
			{
				var form = new FormData();
				form.append("userfile",file);

				var request = new XMLHttpRequest();
				request.open("POST", "./util/send.php");
				request.send(form);
				console.log("go!");
			}
			else
			{

			}
		});
		$("#dropbox").on("dragleave",function(e){
			e.stopPropagation();
			e.preventDefault();
			$(this).css("border", "2px dotted #0B85A1");
		});
		$("#dropbox").on("dragover",function(e){
			e.preventDefault();
		});
		$(document).on('drop', function (e) 
		{
			e.stopPropagation();
			e.preventDefault();
		});
	</script>
</body>
</html>
