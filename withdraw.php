<?php 
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 大会ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

if(!empty($_POST)){

    try{
        $dbh = dbConnect();

        $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
        $data = array(':us_id' => $_SESSION['user_id']);

        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){

            session_destroy();
            debug('セッションの中身：'.print_r($_SESSION,true));
            debug('トップページへ遷移します。');
            header("Location:index.php");
        }else{
            debug('クエリが失敗しました。');
            $err_msg['common'] = MSG08;
        }

    } catch (Exception $e) {
        error_log('エラー発生：'. $e->getMessage());
    }
}
debug('画面表示終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$title = '退会';
require('head.php');
 ?>
    <body>
        <?php
        require('header.php');
         ?>

<div class="form-container">
    <form class="form" action="" method="post">
        <div class="title txt-center">
            <h2>退会</h2>
        </div> 
        <div class="area_msg">
            <?php echo getErrMsg('common'); ?>
        </div>
        <input type="submit" name="submit" value="退会">
    </form>       
</div>
<?php
require('footer.php');
?>