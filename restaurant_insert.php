<?php
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";      //유틸 함수

$conn = dbconnect($host,$dbid,$dbpass,$dbname);

$r_name = $_POST['r_name'];
$phone_number = $_POST['phone_number'];
$city_d = $_POST['city_d'];
$s_g_g = $_POST['s_g_g'];
$d_e_m = $_POST['d_e_m'];
$detailed_ad = $_POST['detailed_ad'];
$t_name = $_POST['t_name'];

mysqli_query($conn, "set_autocommit=0");
mysqli_query($conn, "set session transaction isolation level read uncommitted");
mysqli_query($conn, "start transaction");

$query1 = "select a_id from location where city_d = '$city_d' and s_g_g = '$s_g_g' and d_e_m = '$d_e_m'";
$res1 = mysqli_query($conn, $query1);

if(!$res1)
{
	mysqli_query($conn, "rollback");
	s_msg('Query Error : '.mysqli_error($conn));
}

while($row = mysqli_fetch_array($res1)){
	$a_id = $row[a_id];
}

$query2 = "select t_id from restaurant_type where t_name = '$t_name'";
$res2 = mysqli_query($conn, $query2);

if(!$res2)
{
	mysqli_query($conn, "rollback");
	s_msg('Query Error : '.mysqli_error($conn));
}
else mysqli_query($conn, "commit");

while($row = mysqli_fetch_array($res2)){
	$t_id = $row[t_id];
}

mysqli_query($conn, "set_autocommit=0");
mysqli_query($conn, "set session transaction isolation level serializable");
mysqli_query($conn, "start transaction");

$query = "insert into restaurant (r_name, phone_number, a_id, detailed_ad, t_id) values ('$r_name', '$phone_number', $a_id, '$detailed_ad', $t_id)";
$ret = mysqli_query($conn, $query);

if(!$ret)
{
	echo mysqli_error($conn);
	mysqli_query($conn, "rollback");
    msg('Query Error : '.mysqli_error($conn));
}
else
{
	$last_id = mysqli_insert_id($conn);
	mysqli_query($conn, "commit");
    s_msg ('성공적으로 입력 되었습니다');
    echo "<meta http-equiv='refresh' content='0;url=restaurant_confirm.php?r_id=$last_id'>";
}
?>