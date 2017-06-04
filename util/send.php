<?php
// 4.1.0 이전의 PHP에서는, $_FILES 대신에 $HTTP_POST_FILES를
// 사용해야 합니다.

$uploaddir = '/var/www/uploads/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
//ini_set('upload_max_filesize', '400M');
//ini_set('post_max_size', '400M');
// echo $uploadfile;
// echo $_FILES['userfile']['tmp_name'];
echo '<br>'.($_FILES['userfile']['error']).'<br>';
//echo ini_get('upload-max-filesize').'<br />'.ini_get('post-max-size'),'<br />';
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
} else {
    print "파일 업로드 공격의 가능성이 있습니다!\n";
}

echo '자세한 디버깅 정보입니다:';
print_r($_FILES);

print "</pre>";

?>