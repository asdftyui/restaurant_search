<?
include "header.php";
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";     //유틸 함수
$conn = dbconnect($host, $dbid, $dbpass, $dbname);

$mainCategory = array();

mysqli_query($conn, "set_autocommit=0");
mysqli_query($conn, "set session transaction isolation level read uncommitted");
mysqli_query($conn, "start transaction");

$query = "select distinct city_d from location";
$res = mysqli_query($conn, $query);

if(!$res)
{
	mysqli_query($conn, "rollback");
	s_msg('Query Error : '.mysqli_error($conn));
}

while($row = mysqli_fetch_array($res)){
	array_push($mainCategory, $row[city_d]);
}

$subCategory = array();
foreach($mainCategory as $m) {
	$query = "select distinct s_g_g from location where city_d = '{$m}'";
	$res = mysqli_query($conn, $query);
	
	if(!$res)
	{
		mysqli_query($conn, "rollback");
		s_msg('Query Error : '.mysqli_error($conn));
	}
	
	$arr = array();
	while($row = mysqli_fetch_array($res)){
		array_push($arr, $row[s_g_g]);
	}
	$subCategory += [$m => $arr];
}

$thirdCategory = array();
foreach($subCategory as $main => $sub) {
	$arr1 = array();
	foreach($sub as $id => $s_g_g) {
		$query = "select distinct d_e_m from location where city_d = '{$main}' and s_g_g = '{$s_g_g}'";
		$res = mysqli_query($conn, $query);
		
		if(!$res)
		{
			mysqli_query($conn, "rollback");
			s_msg('Query Error : '.mysqli_error($conn));
		}
		
		$arr = array();
		while($row = mysqli_fetch_array($res)){
			array_push($arr, $row[d_e_m]);
		}
		
		$arr1 += [$s_g_g => $arr];
	}
	$thirdCategory += [$main => $arr1];
}

$type = array();
$query = "select distinct t_name from restaurant_type";
$res = mysqli_query($conn, $query);

if(!$res)
{
	mysqli_query($conn, "rollback");
	s_msg('Query Error : '.mysqli_error($conn));
}
else mysqli_query($conn, "commit");

while($row = mysqli_fetch_array($res)){
	array_push($type, $row[t_name]);
}
?>
    <div class="container_search">
        <form name="restaurant_search_form" action="restaurant_search.php" method="post">
        	<h3>맛집 검색</h3>
        	<br>
      
            <label for="city_d">특별시/특별자치시/특별자치도/광역시/도</label>
            <select name="city_d" id="city_d" onchange="city_dChange()">
                <option value="-1">모든 지역</option>
                <?
                	foreach($mainCategory as $value) {
                		echo "<option value='{$value}'>{$value}</option>";
                	}
                ?>
            </select>
            
            <label for="s_g_g">시/군/구</label>
            <select name="s_g_g" id="s_g_g" onchange="s_g_gChange()">
            	<option value="-1">모든 지역</option>
            </select>
            
            <label for="d_e_m">동/읍/면</label>
            <select name="d_e_m" id="d_e_m">
            	<option value="-1">모든 지역</option>
            </select>
            
            <label for="t_name">맛집 종류</label>
            <select name="t_name" id="t_name">
                <option value="-1">모든 종류</option>
                <?
                	foreach($type as $value) {
                		echo "<option value='{$value}'>{$value}</option>";
                	}
                ?>
            </select>
            
            <input type="text" name="search_keyword" placeholder="키워드 검색">
            
            <input type="submit" name="search" value="검색">

            <script>
            	function city_dChange() {
            		var sel = document.getElementById("city_d").value;
            		
            		var s_g_g = document.querySelector('#s_g_g');
            		s_g_g.options.length = 1;
            		
            		var d_e_m = document.querySelector('#d_e_m');
            		d_e_m.options.length = 1;
            		
            		if (sel == -1) return;
            		else {
            			var subCategory = <?php echo json_encode($subCategory);?>;
            			
            			for(var i = 0; i < subCategory[sel].length; i++) {
            				var option = document.createElement('option');
            				option.innerText = subCategory[sel][i];
            				option.value = subCategory[sel][i];
            				s_g_g.append(option);
            			}
            		}
            	}
            </script>
            
            <script>
            	function s_g_gChange() {
            		var sel1 = document.getElementById("city_d").value;
            		var sel2 = document.getElementById("s_g_g").value;
            		
            		var d_e_m = document.querySelector('#d_e_m');
            		d_e_m.options.length = 1;
            		
            		if (sel2 == -1) return;
            		else {
            			var thirdCategory = <?php echo json_encode($thirdCategory);?>;
            			
            			for(var i = 0; i < thirdCategory[sel1][sel2].length; i++) {
            				var option = document.createElement('option');
            				option.innerText = thirdCategory[sel1][sel2][i];
            				option.value = thirdCategory[sel1][sel2][i];
            				d_e_m.append(option);
            			}
            		}
            	}
            </script>
        </form>
    </div>
<? include("footer.php") ?>