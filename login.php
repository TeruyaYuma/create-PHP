<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 ログインページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

if(!empty($_POST)){
    debug('POST送信がありました。');
    
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $save_path = (empty($_POST['save_path'])) ? false : true;

    validRequire($email,'email');
    validRequire($pass,'pass');
    

    if(empty($err_msg)){
        
        validEmail($email,'email');
        validHalf($email,'email');
        validMaxLen($email,'email');
        validMinLen($email,'email');

        validHalf($pass,'pass');
        validMaxLen($pass,'pass');
        validMinLen($pass,'pass');

        //db接続
        try{
            $dbh = dbConnect();

            $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array('email' => $email);
            
            $stmt = queryPost($dbh, $sql, $data);
            debug('SQL:'.print_r($stmt->errorInfo(),true));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('$result:'.print_r($result,true));

            //パスワード照合確認
            if(!empty($result) && password_verify($pass, array_shift($result))){
                debug('パスワードはマッチしてます。');
                //ログイン有効期限のデフォルト設定
                $sesLimit = 60*60;
                //最終ログインの日時を設定/更新
                $_SESSION['login_date'] = time();

                //ログイン保持チェック
                if($save_path){
                    debug('ログイン保持にチェックが入ってます。');
                    //１ヶ月に設定
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                    debug('ログイン保持にチェックはありません。');
                    //デフォルト設定
                    $_SESSION['login_limit'] = $sesLimit;
                }

                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身:'.print_r($_SESSION,true));
                debug('マイページへ遷移します。');
                header("Location:index.php");
            } else {
                debug('ぱスワードがあっていません。');
                debug('結果：'.print_r($result,true));
                $err_msg['common'] = MSG09;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' .$e->getMessage());
            $err_msg['common'] = MSG08;
        }
    }
    
}

?>

<!-- head -->
<?php
$title = 'ログイン';
require('head.php');
 ?>
<!-- body -->
<body>
<!-- header -->
    <?php
    require('header.php');
     ?>
<!-- main -->
     <div class="form-container">
        <form class="form" action="" method="post">
            <div class="title txt-center">
            <h2>ログイン</h2>
            </div> 
                <?php echo getErrMsg('common'); ?>
            <div>
            <!-- エラーメッセージ -->
            </div>
            <label class="area_msg">
                Email：
                <input type="text" name="email">
            </label>
            <div>
            <?php echo getErrMsg('email'); ?> 
            </div>
            <label>
                password：
                <input type="text" name="pass">
            </label class="area_msg">
            <div>
            <?php echo getErrMsg('pass'); ?>
            </div>
            <label>
                <input type="checkbox" name="save_path">次回ログインを省略する
            </label>
            <div class="btn-container">
            <input type="submit" value="ログイン">
            </div>
            新規登録は<a href="signup.php">コチラ</a>
        </form>       
     </div>
<!-- footer -->
    <?php
    require('footer.php');
    ?>

 </body>
</html>