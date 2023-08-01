<?php
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";      //유틸 함수

$conn = dbconnect($host,$dbid,$dbpass,$dbname);

$review_id = $_POST['review_id'];
$grade = $_POST['grade'];
$content = $_POST['content'];
$r_id = $_GET['r_id'];

mysqli_query($conn, "set_autocommit=0");
mysqli_query($conn, "set session transaction isolation level serializable");
mysqli_query($conn, "start transaction");

$query = "update review set grade = {$grade}, content = '{$content}' where review_id = {$review_id}";
$ret = mysqli_query($conn, $query);

if(!$ret)
{
	echo mysqli_error($conn);
    msg('Query Error : '.mysqli_error($conn));
    mysqli_query($conn, "rollback");
}
else
{
    s_msg ('성공적으로 수정 되었습니다');
    mysqli_query($conn, "commit");
    echo "<meta http-equiv='refresh' content='0;url=review_view.php?r_id=$r_id'>";
}
?>