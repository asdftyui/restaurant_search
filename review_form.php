<?
include "header.php";
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";      //유틸 함수

$conn = dbconnect($host, $dbid, $dbpass, $dbname);

$mode = "등록";
$r_id = $_GET['r_id'];
$action = "review_insert.php?r_id=$r_id";

if (array_key_exists("review_id", $_GET)) {
    $review_id = $_GET["review_id"];
    
	mysqli_query($conn, "set_autocommit=0");
	mysqli_query($conn, "set session transaction isolation level read uncommitted");
	mysqli_query($conn, "start transaction");    
    
    $query =  "select * from review where review_id = $review_id";
    $res = mysqli_query($conn, $query);
    
	if(!$res)
	{
		mysqli_query($conn, "rollback");
		s_msg('Query Error : '.mysqli_error($conn));
	}
	else mysqli_query($conn, "commit");    
    
    $review = mysqli_fetch_array($res);
    if(!$review) {
        msg("리뷰가 존재하지 않습니다.");
    }
    $mode = "수정";
    $action = "review_modify.php?r_id=$r_id";
}
?>

    <div class="container">
        <form name="review_form" action="<?=$action?>" method="post" class="fullwidth">
            <input type="hidden" name="review_id" value="<?=$review_id?>"/>
            <h3>리뷰 <?php echo $mode; ?></h3>
            <p>
                <label for="grade">평점</label>
	            <select name="grade" id="grade">
	                <option value="-1">평점을 선택해주세요.</option>
	                <?
	                	for ($i = 1; $i < 6; $i++) {
	                		if ($i == $review['grade']) {
	                			echo "<option value='{$i}' selected>{$i}</option>";
	                		}
	                		else {
	                			echo "<option value='{$i}'>{$i}</option>";
	                		}
	                	}
	                ?>
	            </select>
            </p>

            <p>
                <label for="content">후기</label>
                <textarea row="5" cols="50" id="content" name="content" placeholder="후기를 입력하세요."></textarea>
            </p>

            <p align="center"><button class="button primary large" onclick="javascript:return validate();"><?=$mode?></button></p>

            <script>
                function validate() {
                    if(document.getElementById("grade").value == "-1") {
                        alert ("평점을 선택해 주십시오"); return false;
                    }
                    return true;
                }
            </script>

        </form>
    </div>
<? include("footer.php") ?>