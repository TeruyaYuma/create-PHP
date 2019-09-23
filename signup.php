<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 新規登録　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(!empty($_POST)){
    debug('POST送信あります');
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
    // 未入力チェック
    validRequire($name,'name');
    validRequire($email,'email');
    validRequire($pass,'pass');
    validRequire($pass_re,'pass_re');

    if(empty($err_msg)){
        //nameチェック
        validMaxLen($name,'name');
        //emailチェック
        validEmail($email,'email');
        validMaxLen($email,'email');
        validEmailDup($email);
        //passチェック
        validHalf($pass,'pass');
        validMinLen($pass,'pass');
        validMaxLen($pass,'pass');
        //pass_reチェック
        validHalf($pass_re,'pass_re');
        validMaxLen($pass_re,'pass_re');
        validMinLen($pass_re,'pass_re');

        if(empty($err_msg)){
            //同値チェック
            validMatch($pass,$pass_re,'pass');

            if(empty($err_msg)){

                try{
                    $dbh = dbConnect();
                    
                    $sql = 'INSERT INTO users (name,email,password,login_date,create_date) VALUES (:name, :email, :pass, :login_date, :create_date)';
                    $data = array(':name' => $name, ':email' => $email, ':pass' =>  password_hash($pass, PASSWORD_DEFAULT),
                                  ':login_date' => date('Y-m-d H:i:s'),
                                  ':create_date' => date('Y-m-d H:i:s'));
                                  
                    
                    $stmt = queryPost($dbh, $sql, $data);
            
                    if($stmt){
                        debug('成功しました。');
                        //デフォルトリミットを１時間に
                        $sesLimit = 60*60;
                        //loginに現時刻を入れてリミットをデフォルトに設定
                        $_SESSION['login_time'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        //userのidをセッションに入れる
                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        debug('ID:' .print_r($_SESSION['user_id'],true));
                        header("Location:index.php");
                    }else{
                        debug('失敗しました。');
                    }
                } catch (Exeption $e){
                    echo $e->getMessage();
                }
            }
        }
    }
    

    
}
?>
<!-- head -->
<?php
$title = '新規登録';
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
            <h2>新規登録</h2>
            </div> 
            <div class="area_msg">
                <?php if(!empty($err_msg['common']))  echo $err_msg['common']; ?>
            </div>

            <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                名前：
                <input type="text" name="name" value="<?php saveFormStr('name'); ?>">
            </label>
            <div class="area_msg">
                <?php echo getErrMsg('name'); ?>
            </div>

            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                Email：
                <input type="text" name="email" value="<?php saveFormStr('email'); ?>">
            </label>
            <div class="area_msg">
                <?php echo getErrMsg('email'); ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                password：
                <input type="text" name="pass" value="<?php saveFormStr('pass'); ?>">
            </label>
            <div class="area_msg">
                <?php echo getErrMsg('pass'); ?>
            </div>

            <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                password_re：
                <input type="text" name="pass_re" value="<?php saveFormStr('pass_re'); ?>">
            </label>
            
            <input type="submit" value="登録">
        </form>      
     </div>
<!-- footer -->
    <?php
     require('footer.php');
    ?>
 </body>
</html>