<?php
include "config.php";    //�����ͺ��̽� ���� ��������
include "util.php";      //��ƿ �Լ�

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
    s_msg ('���������� ���� �Ǿ����ϴ�');
    mysqli_query($conn, "commit");
    echo "<meta http-equiv='refresh' content='0;url=menu_view.php?r_id=$r_id'>";
}
?>