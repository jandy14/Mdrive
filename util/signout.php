<?php
	setcookie('userEmail','',time()-1, '/');
	header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.html');
?>