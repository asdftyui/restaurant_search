<?
include "header.php";
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";  //유틸 함수
?>
<div class="container">
    <?
    $conn = dbconnect($host, $dbid, $dbpass, $dbname);
    
    $r_id = $_GET[r_id];
    
    mysqli_query($conn, "set_autocommit=0");
	mysqli_query($conn, "set session transaction isolation level read uncommitted");
	mysqli_query($conn, "start transaction");    
    
    $query_name = "select r_name from restaurant where r_id = $r_id";
    $n_res = mysqli_query($conn, $query_name);
    
	if(!$n_res)
	{
		mysqli_query($conn, "rollback");
		s_msg('Query Error : '.mysqli_error($conn));
	}
    
	while($row = mysqli_fetch_array($n_res)){
		$r_name = $row['r_name'];
	}
    
    $query = "select review_id, grade, content from restaurant natural join review where r_id = $r_id";
    $res = mysqli_query($conn, $query);
    
    if (!$res) {
        mysqli_query($conn, "rollback");
		s_msg('Query Error : '.mysqli_error($conn));
    }
    else mysqli_query($conn, "commit");
    ?>
    
	<h3><?echo $r_name?> 리뷰</h3>
	<a href="review_form.php?r_id=<?echo $r_id?>"><button class='button primary small' style="float: right;">리뷰 등록</button></a>
	<br>
	<br>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
        	<th>NO.</th>
        	<th>평점</th>
            <th>후기</th>
			<th>기능</th>
        </tr>
        </thead>
        <tbody>
        <?
        $row_index = 1;
       
        while ($row = mysqli_fetch_array($res)) {
            echo "<tr>";
            echo "<td>{$row_index}</td>";
            echo "<td>{$row['grade']}</td>";
            echo "<td>{$row['content']}</td>";
            echo "<td width='17%'>
                <a href='review_form.php?r_id={$r_id} & review_id={$row['review_id']}'><button class='button primary small'>수정</button></a>
                <button onclick='javascript:deleteConfirm({$row['review_id']}, {$r_id})' class='button danger small'>삭제</button>
                </td>";
            echo "</tr>";
            $row_index++;
        }
        ?>
        </tbody>
    </table>
    <script>
        function deleteConfirm(review_id, r_id) {
            if (confirm("정말 삭제하시겠습니까?") == true){    //확인
                window.location = `review_delete.php?review_id=${review_id} & r_id=${r_id}`;
            }else{   //취소
                return;
            }
        }
    </script>
</div>
<? include("footer.php") ?>