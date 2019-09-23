<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 チャットページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=========================
// 画面処理
//=========================
$partnerUserId = '';
$partnerUserInfo = '';
$myUserInfo = '';
$viewData = '';
//画面表示用データ取得
//=========================
//GETパラメータを取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
debug('b_id:'.print_r($b_id,true));
//掲示板とメッセージのデータを取得
$viewData = getMsgAndBord($b_id);
debug('$viewData:'.print_r($viewData,true));
//パラメータ改ざんチェック
if(!isset($viewData)){
  error_log('エラー発生：指定ページに不正な値が入っています。');
  header("Location:index.php");//TOPページへ
}
//viewDataから相手のIDを取得
//相手ID
$dealUserId[] = $viewData[0]['master_id'];
//本人ID
$dealUserId[] = $viewData[0]['user_id'];
debug('to_user:'.print_r($dealUserId,true));
if(($key = array_search($_SESSION['user_id'], $dealUserId)) !== false){
  unset($dealUserId[$key]);
}
//相手のIDを取り出し変数に格納
$partnerUserId = array_shift($dealUserId);
debug('相手のユーザーID:'.$partnerUserId);
if(isset($partnerUserId)){
  $partnerUserInfo = getUser($partnerUserId);
  debug('取り出した相手のユーザー情報:'.print_r($partnerUserInfo,true));
}
$myUserInfo = getUser($_SESSION['user_id']);
debug('取得した自分の情報:'.print_r($myUserInfo,true));

if(!empty($_POST)){
  debug('POSTされました。');
  $msg = (!empty($_POST['msg'])) ? $_POST['msg'] : '';
  debug('MSG:'.print_r($msg,true));
  validRequire($msg,'msg');
  validMaxLen($msg,'msg',500);

  if(empty($err_msg)){

    try{
      $dbh = dbConnect();

      $sql = 'INSERT INTO message (bord_id, send_date,to_user,from_user,msg,create_date) VALUES (:b_id,:send_date,:to_user,:from_user,:msg,:date)';
      $data = array(':b_id' => $b_id, ':send_date' => date('Y-m-d H:i:s'), ':to_user' => $partnerUserId, ':from_user' => $_SESSION['user_id'], ':msg' => $msg, ':date' => date('Y-m-d H:i:s'));
      
      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        $_POST = array();
        debug('連絡掲示板へ遷移します。');
        header("Location:" . $_SERVER['PHP_SELF'] .'?b_id='.$b_id);
      }
    } catch (Exception $e) {
      error_log('エラー発生:'. $e->getMessage());
    }
  }
}
?>
<!-- header -->
<?php
$title = '掲示板';
require('head.php');
?>
<!-- body -->
  <body>
<!-- header -->
    <?php
    require('header.php');
     ?>
<!-- main -->
      <div class="msg-container site-width">
        <div class="msg title txt-center">
          <h3>チャットメッセージ</h3>
        </div>
        <div class="msg-contents">
<!-- チャットメッセージ -->
          <?php
          if(!empty($viewData)){
            foreach($viewData as $key => $val){
              if(!empty($val['from_user']) && $val['from_user'] === $partnerUserId){
          ?>
              <div class="left-container">
                <div class="contents-left">
                  <div class="contents-left-name"><?php echo sanitize($partnerUserInfo['name']); ?></div>
                    <div class="contents-left-txt">
                      <p><?php echo sanitize($val['msg']); ?></p>
                    </div>
                </div>
              </div>

          <?php
              } else {
          ?>

              <div class="right-container">
                <div class="contents-right">
                  <div class="contents-right-name"><?php echo sanitize($myUserInfo['name']); ?></div>
                    <div class="contents-right-txt">
                      <p><?php echo sanitize($val['msg']); ?></p>
                    </div>
                </div>
              </div>

          <?php      
              }
            }
          } else {
          ?>
              <p style="text-align:center; line-height:20;">メッセージはまだありません。</p>
          <?php  
          }
            ?>
        </div><!-- .msg-contents -->
<!-- チャットエリア -->
        <div class="send-msg-area">
          <form class="" action="" method="post">
            <div class="txt-container">
            <textarea  name="msg" id=""></textarea>
            <div class="area-msg">
              <?php echo getErrMsg('msg'); ?>
            </div>
            <input type="submit" name="">
            </div>            
          </form>
        </div>
      </div>
<!-- footer -->
    <?php
    require('footer.php');
    ?>
  </body>
</html>
