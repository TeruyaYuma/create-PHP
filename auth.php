<?php
//=======================
// ログイン認証
//=======================

if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです。');

    //有効期限の超過確認
    if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()){
        debug('有効期限を過ぎています。');

        //セッション削除
        session_destroy();
        header("Location:login.php");

    }else{
        debug('有効期限以内です。');
        //最終ログイン日時を更新
        $_SESSION['login_date'] = time();
        $u_id = $_SESSION['user_id'];
        debug('u_id:'.print_r($u_id,true));
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('TOPページへ遷移します。');
            header("Location:index.php");
        }
    }

}else{
    debug('未ログインユーザーです。');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
     header("Location:login.php"); //ログインページへ
    }
}