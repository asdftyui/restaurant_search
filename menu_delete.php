<?php
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";      //유틸 함수

$conn = dbconnect($host,$dbid,$dbpass,$dbname);

$m_id = $_GET['m_id'];
$r_id = $_GET['r_id'];

mysqli_query($conn, "set_autocommit=0");
mysqli_query($conn, "set session transaction isolation level serializable");
mysqli_query($conn, "start transaction");

$ret = mysqli_query($conn, "delete from menu where m_id = $m_id");

if(!$ret)
{
    msg('Query Error : '.mysqli_error($conn));
    mysqli_query($conn, "rollback");
}
else
{
    s_msg ('성공적으로 삭제 되었습니다');
    mysqli_query($conn, "commit");
    echo "<meta http-equiv='refresh' content='0;url=menu_view.php?r_id=$r_id'>";
}
?>