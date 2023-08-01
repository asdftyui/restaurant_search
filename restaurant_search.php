<?
include "header.php";
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";//유틸 함수
?>
	<div class="container">
    <?
    $conn = dbconnect($host, $dbid, $dbpass, $dbname);
    
    mysqli_query($conn, "set_autocommit=0");
	mysqli_query($conn, "set session transaction isolation level read uncommitted");
	mysqli_query($conn, "start transaction");
    
    $query = "select distinct r_id, r_name, phone_number, city_d, s_g_g, d_e_m, detailed_ad, t_name from restaurant natural join restaurant_type natural join location 
    natural left outer join menu";
    
    if (array_key_exists("search_keyword", $_POST)) {  // array_key_exists() : Checks if the specified key exists in the array
        $search_keyword = $_POST["search_keyword"];
        $query =  $query . " where (r_name like '%$search_keyword%' or m_name like '%$search_keyword%')";
    }
    
    if ($_POST['city_d'] == "-1") {}
    else if ($_POST['s_g_g'] == "-1") {
    	$query = $query . " and city_d = '{$_POST['city_d']}'";
    }
    else if ($_POST['d_e_m'] == "-1") {
    	$query = $query . " and city_d = '{$_POST['city_d']}' and s_g_g = '{$_POST['s_g_g']}'";
    }
    else {
    	$query = $query . " and city_d = '{$_POST['city_d']}' and s_g_g = '{$_POST['s_g_g']}' and d_e_m = '{$_POST['d_e_m']}'";
    }
    
    if ($_POST['t_name'] != "-1") {
    	$query = $query . " and t_name = '{$_POST['t_name']}'";
    } 
    
    $res = mysqli_query($conn, $query);
    
	if(!$res)
	{
		mysqli_query($conn, "rollback");
		s_msg('Query Error : '.mysqli_error($conn));
	}
	else mysqli_query($conn, "commit");
    ?>

	    <table class="table table-striped table-bordered">
	        <thead>
	        <tr>
	        	<th>No.</th>
	            <th>이름</th>
	            <th>종류</th>
	            <th>위치</th>
	            <th>전화 번호</th>
	            <th>평균 평점</th>
	            <th>기능</th>
	        </tr>
	        </thead>
	        <tbody>
	        <?
	        $row_index = 1;
	       
	        while ($row = mysqli_fetch_array($res)) {
	        	$address = $row[city_d].' '. $row[s_g_g].' '.$row[d_e_m].' '.$row[detailed_ad];
	            echo "<tr>";
	            echo "<td>{$row_index}</td>";
	            echo "<td><a href='menu_view.php?r_id={$row['r_id']}'>{$row['r_name']}</a></td>";
	            echo "<td>{$row['t_name']}</td>";
	            echo "<td>{$address}</td>";
	            echo "<td>{$row['phone_number']}</td>";
	            
	            $grade_query = "select grade from restaurant natural join review where r_id = {$row['r_id']}";
	            $grade_res = mysqli_query($conn, $grade_query);
	            $row_num = mysqli_num_rows($grade_res);
	            $sum = 0;
	            while ($grade_row = mysqli_fetch_array($grade_res)) {
	            	$sum += $grade_row['grade'];
	            }
	            $average = round($sum / $row_num, 2);
	            
	            echo "<td><a href='review_view.php?r_id={$row['r_id']}'>$average</a></td>";
	            echo "<td width='17%'>
	                <a href='restaurant_form.php?r_id={$row['r_id']}'><button class='button primary small'>수정</button></a>
	                 <button onclick='javascript:deleteConfirm({$row['r_id']})' class='button danger small'>삭제</button>
	                </td>";
	            echo "</tr>";
	            $row_index++;
	        }
	        ?>
	        </tbody>
	    </table>
	    <script>
	        function deleteConfirm(r_id) {
	            if (confirm("정말 삭제하시겠습니까?") == true){    //확인
	                window.location = "restaurant_delete.php?r_id=" + r_id;
	            }else{   //취소
	                return;
	            }
	        }
	    </script>
	</div>
<? include("footer.php") ?>