<head>
	<title>signin-page</title>
	<meta charset="utf-8" />
</head>
<body>
	<div>
		<form id="signin-form" method="POST" action="./util/signin.php">
			<input type="email" id="user_email" name="user_email" placeholder="이메일">
			<input type="password" id="user_password" name="user_password" placeholder="비밀번호">
			<button type="submit" id="signin_submit">signin</button>
		</form>
	</div>
	<div>
		<form id="signup-form" method="POST" action="./util/signup.php">
			<input type="email" id="user_email_reg" name="user_email" placeholder="이메일">
			<input type="password" id="user_password_reg" name="user_password" placeholder="비밀번호">
			<input type="text" id="user_name_reg" name="user_name" placeholder="이름">
			<button type="submit" id="signup_submit">signup</button>
		</form>
	</div>
</body>