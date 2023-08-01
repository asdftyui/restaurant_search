<?
include "header.php";
include "config.php";    //데이터베이스 연결 설정파일
include "util.php"; //유틸 함수

$conn = dbconnect($host, $dbid, $dbpass, $dbname);
$mode = "입력";
$action = "restaurant_insert.php";

if (array_key_exists("r_id", $_GET)) {
    $r_id = $_GET["r_id"];
    
    mysqli_query($conn, "set_autocommit=0");
	mysqli_query($conn, "set session transaction isolation level read uncommited");
	mysqli_query($conn, "start transaction");    
    
    $query =  "select * from restaurant natural join location natural join restaurant_type where r_id = $r_id";    
    $res = mysqli_query($conn, $query);
    
	if(!$res)
	{
		mysqli_query($conn, "rollback");
		s_msg('Query Error : '.mysqli_error($conn));
	}
    
    $restaurant = mysqli_fetch_array($res);
    if(!$restaurant) {
        msg("음식점이 존재하지 않습니다.");
    }
    $mode = "수정";
    $action = "restaurant_modify.php";
}

$mainCategory = array();
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

    <div class="container">
    	<form name="restaurant_form" action="<?=$action?>" method="post" class="fullwidth">
    		<input type="hidden" name="r_id" value="<?=$restaurant['r_id']?>"/>
    		<h3>음식점 정보 <?php echo $mode; ?></h3>
    		<p>
                <label for="r_name">음식점 이름</label>
                <input type="text" placeholder="음식점 이름 입력" name="r_name" id="r_name" value="<?=$restaurant['r_name']?>"/>
            </p>
    		
    		<p>
                <label for="phone_number">전화번호</label>
                <input type="text" placeholder="전화번호 입력" name="phone_number" id="phone_number" value="<?=$restaurant['phone_number']?>"/>
            </p>
    		
    		<p>
	            <label for="city_d">특별시/특별자치시/특별자치도/광역시/도</label>
	            <select name="city_d" id="city_d" onchange="city_dChange()">
	                <option value="-1">모든 지역</option>
	                <?
	                	foreach($mainCategory as $value) {
	                		if ($value == $restaurant['city_d']) {
	                			echo "<option value='{$value}' selected>{$value}</option>";
	                		}
	                		else {
	                			echo "<option value='{$value}'>{$value}</option>";
	                		}
	                	}
	                ?>
	            </select>
	           
	            <label for="s_g_g">시/군/구</label>
	            <select name="s_g_g" id="s_g_g" onchange="s_g_gChange()">
	            	<option value="-1">모든 지역</option>
	                <?
	                	$arr = $subCategory[$restaurant[city_d]];
	                	foreach($arr as $value) {
	                		if ($value == $restaurant['s_g_g']) {
	                			echo "<option value='{$value}' selected>{$value}</option>";
	                		}
	                		else {
	                			echo "<option value='{$value}'>{$value}</option>";
	                		}
	                	}
	                ?>
	            </select>
	            
	            <label for="d_e_m">동/읍/면</label>
	            <select name="d_e_m" id="d_e_m">
	            	<option value="-1">모든 지역</option>
	                <?
	                	$arr = $thirdCategory[$restaurant[city_d]][$restaurant[s_g_g]];
	                	foreach($arr as $value) {
	                		if ($value == $restaurant['d_e_m']) {
	                			echo "<option value='{$value}' selected>{$value}</option>";
	                		}
	                		else {
	                			echo "<option value='{$value}'>{$value}</option>";
	                		}
	                	}
	                ?>
	            </select>
	            
           		 <label for="detailed_ad">상세 주소</label>
           		 <input type="text" placeholder="상세 주소 입력" name="detailed_ad" id="detailed_ad" value="<?=$restaurant['detailed_ad']?>"/>
           	</p>

            
            <p>
	            <label for="t_name">맛집 종류</label>
	            <select name="t_name" id="t_name">
	                <option value="-1">모든 종류</option>
	                <?
	                	foreach($type as $value) {
	                		if ($value == $restaurant['t_name']) {
	                			echo "<option value='{$value}' selected>{$value}</option>";
	                		}
	                		else {
	                			echo "<option value='{$value}'>{$value}</option>";
	                		}
	                	}
	                ?>
	            </select>
            </p>
            
            <p align="center"><button class="button primary large" onclick="javascript:return validate();"><?=$mode?></button></p>

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

            <script>
                function validate() {
                    if(document.getElementById("r_name").value == "") {
                        alert ("음식점 이름을 입력해 주십시오"); return false;
                    }
                    else if(document.getElementById("city_d").value == "-1") {
                        alert ("광역시/도를 선택해 주십시오"); return false;
                    }
                    else if(document.getElementById("s_g_g").value == "-1") {
                        alert ("시/군/구를 선택해 주십시오"); return false;
                    }
                    else if(document.getElementById("d_e_m").value == "-1") {
                        alert ("동/읍/면을 선택해 주십시오"); return false;
                    }
                     else if(document.getElementById("detailed_ad").value == "") {
                        alert ("상세주소를 입력해 주십시오"); return false;
                    }
                    return true;
                }
            </script>
        </form>
    </div>
<? include("footer.php") ?>