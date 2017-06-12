<?php
/*
	DB를 관리하는 클래스
*/

class DB_Manager
{
	private $db_host; // DB 주소
	private $db_user; // DB 관리자 아이디
	private $db_password; // DB 관리자 비밀번호
	private $db_dbname; // DB 이름
	public $pdo; // PDO 클래스

	// 생성자
	function __construct($pFilePath = '/var/www/html/MDrive/data/account.dat')
	{
		$settingFile = $pFilePath; // DB 셋팅 파일 로컬 주소

		/*
			DB 셋팅 파일이 존재한다면
			셋팅 파일을 이용해 DB를 초기화합니다.
		*/

		if(file_exists($settingFile))
		{
			$fp = fopen($settingFile, 'r');

			$buffer = fread($fp, filesize($settingFile));
			fclose($fp);
			$setting = ['host', 'user', 'password', 'dbname'];
			$list = strtok($buffer, ';');

			for($i = 0; $i < count($setting); $i++)
			{
				$setting[$i] = $list;
				$list = strtok(';');
			}

			$this->db_host = $setting[0];
			$this->db_user = $setting[1];
			$this->db_password = $setting[2];
			$this->db_dbname = $setting[3];
		}
		else
		{
			echo 'file error';
		}

		try
		{
			$this->pdo = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_dbname.';charset=utf8', $this->db_user, $this->db_password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
				echo $e->getMessage();
		}
	}
	// DB 정보를 리턴하는 함수
	function DBInfo()
	{
		return $this->db_host.'<br>'.$this->db_user.'<br>'.$this->db_password.'<br>'.$this->db_dbname.'<br>';
	}
}
?>
